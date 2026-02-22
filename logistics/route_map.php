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
    preg_match("/POLYGON\(\((.*)\)\)/", $territory['polygon_text'], $matches);
    if (!empty($matches[1])) {
        $points = explode(',', $matches[1]);
        foreach ($points as $pt) {
            $coords = explode(' ', trim($pt));
            if (count($coords) == 2) {
                $polygonCoords[] = [floatval($coords[1]), floatval($coords[0])];
            }
        }
    }
}

// Fetch Orders ready for delivery (Preparing) or already On Route
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

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Route Map</title>
    <link rel="stylesheet" href="../assets/css/style.css" />

    <!-- Leaflet CSS for Interactive Routing Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
        .map-container {
            flex: 1;
            background: #e0e0e0;
            border-radius: 12px;
            min-height: 600px;
            /* Big full-screen map */
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <script>
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
            <a href="dashboard.php">Dashboard</a>
            <a href="route_map.php" class="active">Route Map</a>

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
            <div>
                <h1>Intelligent Delivery Route</h1>
                <p style="color:#666; margin-top:4px;">Uber-like optimized routing for all your pending delivery
                    drop-offs.</p>
            </div>
            <span class="pill" style="background:#e8f4fd; color:#0288d1;">GPS Navigation</span>
        </div>

        <div style="display:flex; flex-direction:column; gap: 24px;">

            <!-- Interactive Route Map -->
            <div class="map-container">
                <button id="routeBtn" onclick="generateRoute()" class="primary"
                    style="position:absolute; top:20px; right:20px; z-index:1000; box-shadow:0 4px 15px rgba(0,0,0,0.4); font-size:16px; font-weight:bold; background:#000; color:#fff; border:none; padding:14px 24px; border-radius:30px; cursor:pointer; transition: all 0.2s ease;">
                    Calculate Best Route 🚗</button>
                <div id="routeMap"></div>
            </div>

        </div>
    </main>

    <script>
        // Initialize Leaflet Map
        document.addEventListener('DOMContentLoaded', function () {
            const polygonCoords = <?= json_encode($polygonCoords) ?>;
            const ordersList = <?= json_encode($orders) ?>;

            let mapCenter = [6.9271, 79.8612];
            let zoomLevel = 11;

            window.map = L.map('routeMap').setView(mapCenter, zoomLevel);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(window.map);

            if (polygonCoords && polygonCoords.length > 0) {
                const territoryPoly = L.polygon(polygonCoords, {
                    color: '#f57c00',
                    fillColor: '#ffa726',
                    fillOpacity: 0.1,
                    weight: 2
                }).addTo(window.map);
                window.map.fitBounds(territoryPoly.getBounds());
            }

            if (ordersList && ordersList.length > 0) {
                ordersList.forEach(ord => {
                    const lat = parseFloat(ord.latitude);
                    const lng = parseFloat(ord.longitude);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        const marker = L.marker([lat, lng]).addTo(window.map);
                        marker.bindPopup(`<b>Order #${ord.order_id}</b><br>${ord.shop_name}<br>📍 ${ord.address}`);
                    }
                });
            }
        });

        let routingControl = null;

        function generateRoute() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.");
                return;
            }

            document.getElementById('routeBtn').innerText = "Locating Driver...";

            navigator.geolocation.getCurrentPosition(position => {
                const startLat = position.coords.latitude;
                const startLng = position.coords.longitude;

                const waypoints = [L.latLng(startLat, startLng)];

                const ordersList = <?= json_encode($orders) ?>;
                if (ordersList && ordersList.length > 0) {
                    ordersList.forEach(ord => {
                        const lat = parseFloat(ord.latitude);
                        const lng = parseFloat(ord.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            waypoints.push(L.latLng(lat, lng));
                        }
                    });
                }

                if (waypoints.length < 2) {
                    alert("No pending deliveries found to draw a route.");
                    document.getElementById('routeBtn').innerText = "Calculate Best Route 🚗";
                    return;
                }

                if (routingControl && window.map) {
                    window.map.removeControl(routingControl);
                }

                routingControl = L.Routing.control({
                    waypoints: waypoints,
                    routeWhileDragging: false,
                    show: true, // Show the detailed turn-by-turn instruction box
                    lineOptions: {
                        styles: [{ color: '#000', opacity: 0.8, weight: 6 }]
                    },
                    createMarker: function () { return null; }
                }).addTo(window.map);

                document.getElementById('routeBtn').style.display = 'none';

            }, error => {
                alert('Geolocation error: ' + error.message);
                document.getElementById('routeBtn').innerText = "Calculate Best Route 🚗";
            }, { enableHighAccuracy: true });
        }
    </script>
</body>

</html>