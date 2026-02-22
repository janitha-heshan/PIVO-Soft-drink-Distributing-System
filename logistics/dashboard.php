<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['SalesSupervisor', 'SalesRep', 'Admin']);

$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];

// Fetch the logged-in user's assigned polygon territory
$territoryStmt = $pdo->prepare("
    SELECT ga.area_name, ST_AsText(ga.boundary_polygon) as polygon_text 
    FROM area_assignments aa
    JOIN geofenced_areas ga ON aa.area_id = ga.area_id
    WHERE aa.sales_rep_id = ?
");
$territoryStmt->execute([$userId]);
$territory = $territoryStmt->fetch();

// Parse the Polygon Text to an array of coordinate pairs for the frontend map
$polygonCoords = [];
if ($territory && !empty($territory['polygon_text'])) {
    // POLYGON((lng lat, lng lat, ...))
    preg_match("/POLYGON\(\((.*)\)\)/", $territory['polygon_text'], $matches);
    if (!empty($matches[1])) {
        $points = explode(',', $matches[1]);
        foreach ($points as $pt) {
            $coords = explode(' ', trim($pt));
            // Leaflet expects [lat, lng]
            if (count($coords) == 2) {
                $polygonCoords[] = [floatval($coords[1]), floatval($coords[0])];
            }
        }
    }
}

// Fetch Orders ready for delivery (Preparing) or already On Route
// NEW LOGIC: Only fetch orders that fall inside the assigned polygon using ST_Contains
$stmt = $pdo->prepare("
    SELECT o.order_id, o.order_date, o.delivery_status, o.total_amount, s.shop_name, s.address, s.contact_number, s.latitude, s.longitude
    FROM orders o
    JOIN shops s ON o.shop_id = s.shop_id
    LEFT JOIN area_assignments aa ON aa.sales_rep_id = ?
    LEFT JOIN geofenced_areas ga ON aa.area_id = ga.area_id
    WHERE o.delivery_status IN ('Preparing', 'Dispatched')
    AND (ga.boundary_polygon IS NULL OR ST_Contains(ga.boundary_polygon, POINT(s.longitude, s.latitude)))
    ORDER BY o.order_date ASC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

// Fetch Google Maps API Key (Optional now, as we'll use open-source Leaflet)
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

    <!-- Leaflet CSS for Interactive Routing Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        .map-container {
            flex: 1;
            background: #e0e0e0;
            border-radius: 12px;
            min-height: 500px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #fff;
        }

        #routeMap {
            width: 100%;
            height: 100%;
            z-index: 1;
        }
    </style>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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

        function toggleDropdown() {
            var d = document.getElementById("userDropdown");
            if (d.style.display === "block") {
                d.style.display = "none";
            } else {
                d.style.display = "block";
            }
        }
        window.onclick = function (event) {
            if (!event.target.closest('.user-menu')) {
                const drop = document.getElementById("userDropdown");
                if (drop) drop.style.display = "none";
            }
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

            <div class="user-menu" style="position:relative; margin-left:14px;">
                <div onclick="toggleDropdown()" style="cursor:pointer; display:flex; align-items:center;">
                    <button class="avatar" style="background:#f57c00; margin:0;">
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                    </button>
                </div>
                <div id="userDropdown" class="dropdown-content" style="right:0; left:auto;">
                    <a href="../profile.php">My Profile</a>
                    <a href="../logout.php" style="color:#d93025;">Logout</a>
                </div>
            </div>
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

            <!-- Interactive Route Map -->
            <div class="map-container">
                <div id="routeMap"></div>
            </div>

        </div>
    </main>

    <script>
        // Initialize Leaflet Map
        document.addEventListener('DOMContentLoaded', function () {
            // Default center data
            const polygonCoords = <?= json_encode($polygonCoords) ?>;
            const ordersList = <?= json_encode($orders) ?>;

            // Default to Colombo center if no polygon is set
            let mapCenter = [6.9271, 79.8612];
            let zoomLevel = 11;

            // Create the map
            const map = L.map('routeMap').setView(mapCenter, zoomLevel);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Draw Territory Polygon if exists
            if (polygonCoords && polygonCoords.length > 0) {
                const territoryPoly = L.polygon(polygonCoords, {
                    color: '#f57c00',    // Distinct orange line
                    fillColor: '#ffa726',
                    fillOpacity: 0.2,    // Light orange fill inside territory
                    weight: 3
                }).addTo(map);

                // Adjust map viewport to fit the polygon beautifully
                map.fitBounds(territoryPoly.getBounds());

                territoryPoly.bindPopup(`<b>Assigned Territory Area</b><br>Logistics operations restricted to this zone.`);
            }

            // Plot Order Delivery Markers
            if (ordersList && ordersList.length > 0) {
                ordersList.forEach(ord => {
                    const lat = parseFloat(ord.latitude);
                    const lng = parseFloat(ord.longitude);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        const marker = L.marker([lat, lng]).addTo(map);

                        // Bold informative popup
                        const popupHtml = `
                            <div style="font-family:sans-serif;">
                                <h3 style="margin:0 0 5px 0; color:#f57c00;">Order #${ord.order_id}</h3>
                                <strong>${ord.shop_name}</strong><br>
                                📍 ${ord.address}<br>
                                📞 ${ord.contact_number}<br>
                                📦 <span style="font-size:12px; background:#fff3e0; padding:2px 6px; border-radius:10px;">${ord.delivery_status}</span>
                            </div>
                        `;
                        marker.bindPopup(popupHtml);
                    }
                });
            }
        });
    </script>
</body>

</html>