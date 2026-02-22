<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['SalesSupervisor', 'SalesRep', 'Admin']);

$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];

// Fetch Orders ready for delivery (Preparing) or already On Route
$stmt = $pdo->query("
    SELECT o.order_id, o.order_date, o.delivery_status, o.total_amount, s.shop_name, s.address, s.contact_number
    FROM orders o
    JOIN shops s ON o.shop_id = s.shop_id
    WHERE o.delivery_status IN ('Preparing', 'Dispatched')
    ORDER BY o.order_date ASC
");
$orders = $stmt->fetchAll();

// Fetch Google Maps API Key
$keyStmt = $pdo->prepare("SELECT api_key FROM api_configurations WHERE service_name = 'google_maps'");
$keyStmt->execute();
$apiKey = $keyStmt->fetchColumn() ?: '';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Logistics</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script>
        function markDelivered(orderId) {
            if (!confirm('Confirm delivery for Order #' + orderId + '?')) return;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    sendDeliveryRequest(orderId, lat, lng);
                }, error => {
                    alert('Geolocation error: ' + error.message + '\nPlease allow location access to confirm delivery.');
                }, { enableHighAccuracy: true });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function sendDeliveryRequest(orderId, lat, lng) {
            fetch('delivery_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'deliver',
                    order_id: orderId,
                    gps_lat: lat,
                    gps_lng: lng
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Order Delivered Successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }
    </script>
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="../assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Logistics</span>
        </div>

        <nav class="dash-nav">
            <a href="dashboard.php" class="active">Route Map</a>
            <a href="../logout.php">Logout</a>
            <button class="avatar"
                style="background:#f57c00;"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></button>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Delivery Route</h1>
            <span class="pill" style="background:#fff3e0; color:#e65100;">Logistics View</span>
        </div>

        <div class="row" style="display:flex; gap: 24px;">

            <!-- Order List -->
            <div style="flex: 1;">
                <h2 style="margin-bottom:12px;">Orders to Deliver</h2>
                <div class="cards" style="display:flex; flex-direction:column; gap:16px;">
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $ord): ?>
                            <div class="product-card" style="display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <h3 style="margin:0; font-size:16px;">Order #<?php echo $ord['order_id']; ?></h3>
                                    <p
                                        style="text-transform:uppercase; font-size:11px; font-weight:bold; color:#666; margin-bottom:4px;">
                                        <?php echo htmlspecialchars($ord['shop_name']); ?>
                                    </p>
                                    <p style="font-size:13px; color:#444;">📍 <?php echo htmlspecialchars($ord['address']); ?>
                                    </p>
                                    <p style="font-size:13px; color:#444;">📞
                                        <?php echo htmlspecialchars($ord['contact_number']); ?>
                                    </p>
                                </div>
                                <div style="text-align:right;">
                                    <span class="pill"
                                        style="margin-bottom:8px; display:inline-block; font-size:11px;"><?php echo $ord['delivery_status']; ?></span>
                                    <br>
                                    <button onclick="markDelivered(<?php echo $ord['order_id']; ?>)" class="primary"
                                        style="padding: 8px 12px; font-size:13px;">Verify Delivery</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No active deliveries scheduled.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Map Placeholder -->
            <div
                style="flex: 1; background:#e0e0e0; border-radius:12px; min-height:400px; display:flex; align-items:center; justify-content:center; color:#777; position:relative; overflow:hidden;">
                <?php if (!empty($apiKey)): ?>
                    <iframe width="100%" height="100%" style="border:0" loading="lazy" allowfullscreen
                        src="https://www.google.com/maps/embed/v1/place?key=<?php echo htmlspecialchars($apiKey); ?>&q=Colombo,Sri+Lanka">
                    </iframe>
                <?php else: ?>
                    <div style="text-align:center;">
                        <h3 style="font-size:24px;">🗺️</h3>
                        <p>Google Maps Integration</p>
                        <p style="font-size:12px; color:#d93025;">(API Key not configured)</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</body>

</html>