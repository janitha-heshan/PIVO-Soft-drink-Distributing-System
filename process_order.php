<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !in_array($_SESSION['role'], ['ShopOwner', 'Admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid Input']);
    exit;
}

$shopId = intval($input['shop_id']);
$items = $input['items'];
$totalAmount = floatval($input['total_amount']);
$userId = $_SESSION['user_id'];

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Create Order
    $stmt = $pdo->prepare("INSERT INTO orders (shop_id, order_date, delivery_status, total_amount, status_updated_at) VALUES (?, NOW(), 'Pending', ?, NOW())");
    $stmt->execute([$shopId, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // 2. Insert Items
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");

    // 3. Log Tracking
    $logStmt = $pdo->prepare("INSERT INTO order_tracking_logs (order_id, status, changed_by, timestamp) VALUES (?, 'Pending', ?, NOW())");
    $logStmt->execute([$orderId, $userId]);

    foreach ($items as $item) {
        $itemStmt->execute([
            $orderId,
            intval($item['productId']),
            intval($item['qty']),
            floatval($item['price'])
        ]);

        // NOTE: We do NOT deduct inventory yet. Inventory deduction happens on 'Preparing' or 'Dispatched' (Confirmed by Manager).
        // If we wanted to reserve stock, we would update inventory here.
        // For now, based on Plan, deduction is on Manager Confirmation.
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>