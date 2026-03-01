<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

requireRole(['ShopOwner', 'Admin']);

// Check if user already has a shop, if "strict single shop" or just "must have at least one"
// Strategy: This page is valid for creating ANY shop.
// Middleware (elsewhere) will force them here if they have NONE.

$error = '';
$success = '';

// Fetch API Key for Map
$keyStmt = $pdo->prepare("SELECT api_key FROM api_configurations WHERE service_name = 'google_maps'");
$keyStmt->execute();
$apiKey = $keyStmt->fetchColumn() ?: '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shopName = trim($_POST['shop_name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact_number']);
    $lat = trim($_POST['latitude']);
    $lng = trim($_POST['longitude']);

    if (empty($shopName) || empty($address) || empty($lat) || empty($lng)) {
        $error = "Shop Name, Address, and Location (Lat/Lng) are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO shops (owner_id, shop_name, address, contact_number, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $shopName, $address, $contact, $lat, $lng]);
            $success = "Shop registered successfully!";

            // Redirect to dashboard after short delay
            header("refresh:2;url=shop_dashboard.php");
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Register Shop</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .form-card {
            max-width: 600px;
            margin-top: 40px;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: center;
        }

        .alert-error {
            background: #ffebeb;
            color: #d93025;
        }

        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        #map {
            height: 300px;
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #ccc;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    <script>
        function initMap() {
            const defaultLoc = { lat: 6.9271, lng: 79.8612 }; // Colombo

            <?php if (!empty($apiKey)): ?>
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 13,
                    center: defaultLoc,
                });
                const marker = new google.maps.Marker({
                    position: defaultLoc,
                    map: map,
                    draggable: true,
                    title: "Drag to Shop Location"
                });

                // Update inputs on drag
                google.maps.event.addListener(marker, 'dragend', function (evt) {
                    document.getElementById('lat').value = evt.latLng.lat().toFixed(6);
                    document.getElementById('lng').value = evt.latLng.lng().toFixed(6);
                });

                // Update inputs on click
                map.addListener("click", (mapsMouseEvent) => {
                    const clickedLoc = mapsMouseEvent.latLng;
                    marker.setPosition(clickedLoc);
                    document.getElementById('lat').value = clickedLoc.lat().toFixed(6);
                    document.getElementById('lng').value = clickedLoc.lng().toFixed(6);
                });

            <?php else: ?>
                // Fallback if no API key
                document.getElementById('map').innerHTML = '<p class="text-muted">Map Unavailable (No API Key). Please enter coordinates manually.</p>';
            <?php endif; ?>
        }

        // Auto-fill button for testing
        function useCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    document.getElementById('lat').value = position.coords.latitude.toFixed(6);
                    document.getElementById('lng').value = position.coords.longitude.toFixed(6);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
    <?php if (!empty($apiKey)): ?>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap" async
            defer></script>
    <?php endif; ?>
</head>

<body <?php if (empty($apiKey))
    echo 'onload="initMap()"'; ?>>
    <header class="topbar">
        <div class="brand">
            <img src="assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Holdings</span>
        </div>
        <nav class="dash-nav">
            <a href="shop_dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <section class="form-card">
            <h1>Register New Shop</h1>
            <p class="muted" style="margin-bottom:20px;">You must register a shop location to proceed.</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form class="form" method="POST">
                <label>
                    Shop Name
                    <input type="text" name="shop_name" placeholder="e.g. City Mart" required />
                </label>

                <label>
                    Address
                    <input type="text" name="address" placeholder="Full Address" required />
                </label>

                <label>
                    Location (Latitude & Longitude)
                    <div class="row" style="display:flex; gap:10px;">
                        <input type="text" id="lat" name="latitude" placeholder="Latitude" required style="flex:1;" />
                        <input type="text" id="lng" name="longitude" placeholder="Longitude" required style="flex:1;" />
                    </div>
                    <button type="button" onclick="useCurrentLocation()" class="link-btn"
                        style="font-size:12px; margin-top:4px;">📍 Use My Current Location</button>
                </label>

                <!-- Map Container -->
                <div id="map"></div>

                <label style="margin-top:12px;">
                    Contact Number
                    <input type="text" name="contact_number" placeholder="07xxxxxxxx" />
                </label>

                <button type="submit" class="primary full" style="margin-top:20px;">Register Shop</button>
            </form>
        </section>
    </main>
</body>

</html>