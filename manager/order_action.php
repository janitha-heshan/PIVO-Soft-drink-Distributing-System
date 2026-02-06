<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !in_array($_SESSION['role'], ['StoreManager', 'Admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$orderId = intval($input['order_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$orderId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid Input']);
    exit;
}

try {
    $pdo->beginTransaction();

    if ($action === 'confirm') {
        // 1. Fetch Order Items
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();

        // 2. Check & Deduct Inventory
        $invCheck = $pdo->prepare("SELECT quantity_in_stock, inventory_id FROM inventory WHERE product_id = ? FOR UPDATE");
        $invUpdate = $pdo->prepare("UPDATE inventory SET quantity_in_stock = quantity_in_stock - ? WHERE inventory_id = ?");
        $logInsert = $pdo->prepare("INSERT INTO inventory_logs (product_id, change_amount, new_quantity, changed_by_user_id, reason, timestamp) VALUES (?, ?, ?, ?, ?, NOW())");

        foreach ($items as $item) {
            $pid = $item['product_id'];
            $qty = $item['quantity'];

            $invCheck->execute([$pid]);
            $inv = $invCheck->fetch();

            if (!$inv || $inv['quantity_in_stock'] < $qty) {
                throw new Exception("Insufficient stock for Product ID $pid");
            }

            $newQty = $inv['quantity_in_stock'] - $qty;

            // Deduct
            $invUpdate->execute([$qty, $inv['inventory_id']]);

            // Log
            $logInsert->execute([$pid, -$qty, $newQty, $userId, "Order #$orderId Confirmed"]);
        }

        // 3. Update Order Status
        $updOrder = $pdo->prepare("UPDATE orders SET delivery_status = 'Preparing', status_updated_at = NOW() WHERE order_id = ?");
        $updOrder->execute([$orderId]);

        // 4. Log Order Status Change
        $logOrder = $pdo->prepare("INSERT INTO order_tracking_logs (order_id, status, changed_by, timestamp) VALUES (?, 'Preparing', ?, NOW())");
        $logOrder->execute([$orderId, $userId]);

        // 5. Record Payment (Assuming 'Confirmed' implies payment/credit verified)
        // Check if payment already exists to avoid duplicates
        $payCheck = $pdo->prepare("SELECT payment_id FROM payments WHERE order_id = ?");
        $payCheck->execute([$orderId]);
        if ($payCheck->rowCount() == 0) {
            // Get Total Amount
            $amtStmt = $pdo->prepare("SELECT total_amount FROM orders WHERE order_id = ?");
            $amtStmt->execute([$orderId]);
            $amount = $amtStmt->fetchColumn();

            $insPay = $pdo->prepare("INSERT INTO payments (order_id, amount_paid, payment_date, payment_method) VALUES (?, ?, NOW(), 'Credit')");
            $insPay->execute([$orderId, $amount]);
        }

    } elseif ($action === 'reject') {
        // Simply update status to Cancelled
        $reason = $input['reason'] ?? 'Rejected by Manager';

        $updOrder = $pdo->prepare("UPDATE orders SET delivery_status = 'Cancelled', status_updated_at = NOW() WHERE order_id = ?");
        $updOrder->execute([$orderId]);

        $logOrder = $pdo->prepare("INSERT INTO order_tracking_logs (order_id, status, changed_by, timestamp) VALUES (?, 'Cancelled', ?, NOW())");
        $logOrder->execute([$orderId, $userId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>