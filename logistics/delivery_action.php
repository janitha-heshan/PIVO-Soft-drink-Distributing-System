<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$orderId = intval($input['order_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$orderId || $action !== 'deliver') {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
    exit;
}

// In a real app, verify $input['gps_lat'] against Shop Coordinates

try {
    $pdo->beginTransaction();

    // Update Status
    $stmt = $pdo->prepare("UPDATE orders SET delivery_status = 'Delivered', status_updated_at = NOW() WHERE order_id = ?");
    $stmt->execute([$orderId]);

    // Log It
    $log = $pdo->prepare("INSERT INTO order_tracking_logs (order_id, status, changed_by, timestamp) VALUES (?, 'Delivered', ?, NOW())");
    $log->execute([$orderId, $userId]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>