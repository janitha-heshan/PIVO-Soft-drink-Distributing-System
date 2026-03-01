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

// Haversine formula
function getDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

try {
    $pdo->beginTransaction();

    // Verify Geofence
    $shopStmt = $pdo->prepare("SELECT s.latitude, s.longitude FROM orders o JOIN shops s ON o.shop_id = s.shop_id WHERE o.order_id = ?");
    $shopStmt->execute([$orderId]);
    $shop = $shopStmt->fetch();

    if ($shop && !is_null($shop['latitude']) && !is_null($shop['longitude'])) {
        $repLat = floatval($input['gps_lat'] ?? 0);
        $repLng = floatval($input['gps_lng'] ?? 0);

        $distance = getDistance($shop['latitude'], $shop['longitude'], $repLat, $repLng);

        if ($distance > 1.0) {
            throw new Exception("You are too far from the shop to complete this delivery. (Distance: " . round($distance, 2) . " km)");
        }
    }

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