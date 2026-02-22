<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'ShopOwner', 'Admin', 'FactoryOwner']);

$username = $_SESSION['username'];

// Fetch all Logistics Sales Reps to assign territories
$repsStmt = $pdo->prepare("SELECT user_id, username, full_name, email, role FROM users WHERE role IN ('SalesRep', 'SalesSupervisor')");
$repsStmt->execute();
$salesReps = $repsStmt->fetchAll();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Territory Management</title>
    <link rel="stylesheet" href="../assets/css/style.css" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <!-- Leaflet Draw CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

    <style>
        .map-wrapper {
            background: #fff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .controls {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .controls select {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            min-width: 250px;
        }

        #drawMap {
            width: 100%;
            height: 600px;
            border-radius: 8px;
            border: 1px solid #ddd;
            z-index: 1;
        }
    </style>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <!-- Leaflet Draw JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

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
            <span class="brand-name">PIVO Manager</span>
        </div>

        <nav class="dash-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="manage_products.php">Products</a>
            <a href="returns.php">Returns</a>
            <a href="manage_territories.php" class="active">Territories</a>

            <div class="user-menu" style="position:relative; margin-left:14px;">
                <div onclick="toggleDropdown()" style="cursor:pointer; display:flex; align-items:center;">
                    <button class="avatar" style="background:#5e35b1; margin:0;">
                        <?= strtoupper(substr($username, 0, 1)) ?>
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
                <h1>Geographic Territories</h1>
                <p style="color:#666; margin-top:4px;">Draw and assign delivery polygons directly to Sales
                    Representatives.</p>
            </div>
            <span class="pill" style="background:#e8f4fd; color:#0288d1;">Spatial Management</span>
        </div>

        <div class="map-wrapper">
            <div class="controls">
                <div>
                    <label for="repSelect" style="font-weight:bold; display:block; margin-bottom:6px;">Select Logistics
                        Representative:</label>
                    <select id="repSelect" onchange="loadRepTerritory()">
                        <option value="">-- Choose a Sales Rep --</option>
                        <?php foreach ($salesReps as $rep): ?>
                            <option value="<?= $rep['user_id'] ?>">
                                <?= htmlspecialchars($rep['full_name'] ?: $rep['username']) ?> (
                                <?= htmlspecialchars($rep['role']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="flex:1;"></div> <!-- Spacer -->

                <button id="saveBtn" class="primary" style="padding:10px 20px; font-weight:bold;"
                    onclick="saveTerritory()" disabled>Save Territory</button>
            </div>

            <div id="drawMap"></div>
        </div>
    </main>

    <script>
        let map, drawnItems, currentLayer = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize the map centered near Colombo
            map = L.map('drawMap').setView([6.9271, 79.8612], 12);

            // Base map tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Initialize the FeatureGroup to store editable layers
            drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            // Initialize the draw control and pass it the FeatureGroup of editable layers
            const drawControl = new L.Control.Draw({
                edit: {
                    featureGroup: drawnItems,
                    remove: true // allow removing drawn polygon
                },
                draw: {
                    polygon: {
                        allowIntersection: false,
                        showArea: true,
                        shapeOptions: {
                            color: '#16a34a'
                        }
                    },
                    polyline: false,
                    circle: false,
                    rectangle: false,
                    marker: false,
                    circlemarker: false
                }
            });
            map.addControl(drawControl);

            // Event triggered when a polygon is drawn
            map.on(L.Draw.Event.CREATED, function (e) {
                const type = e.layerType,
                    layer = e.layer;

                if (type === 'polygon') {
                    // Only allow ONE polygon per rep on screen
                    drawnItems.clearLayers();
                    drawnItems.addLayer(layer);

                    // Enable save button if a rep is selected
                    checkSaveState();
                }
            });

            // Event triggered when a polygon is edited or deleted
            map.on(L.Draw.Event.EDITED, checkSaveState);
            map.on(L.Draw.Event.DELETED, checkSaveState);
        });

        // Toggle Save Button depending on dropdown and drawn items
        function checkSaveState() {
            const repId = document.getElementById('repSelect').value;
            const hasLayer = drawnItems.getLayers().length > 0;
            const btn = document.getElementById('saveBtn');

            if (repId && hasLayer) {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            } else {
                btn.disabled = true;
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
            }
        }

        // Parse MySQL WKT string into Leaflet coordinate arrays
        // Input: "POLYGON((lng lat, lng lat))"
        function parseWKT(wkt) {
            if (!wkt || !wkt.startsWith("POLYGON")) return null;

            // Extract content inside POLYGON(( ... ))
            const matches = wkt.match(/POLYGON\(\((.*)\)\)/);
            if (!matches || !matches[1]) return null;

            const pointsStr = matches[1].split(',');
            const latLangs = [];

            pointsStr.forEach(pt => {
                const parts = pt.trim().split(' ');
                if (parts.length === 2) {
                    const lng = parseFloat(parts[0]);
                    const lat = parseFloat(parts[1]);
                    // Leaflet expects [lat, lng]
                    latLangs.push([lat, lng]);
                }
            });
            return latLangs;
        }

        // Fetch existing territory for selected rep and display on map
        function loadRepTerritory() {
            checkSaveState(); // Update btn state

            const repId = document.getElementById('repSelect').value;
            drawnItems.clearLayers(); // Clear map

            if (!repId) return;

            fetch('territory_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_territory', rep_id: repId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.wkt) {
                        const latLangs = parseWKT(data.wkt);
                        if (latLangs && latLangs.length > 0) {
                            // Recreate the polygon and add it to drawnItems
                            const poly = L.polygon(latLangs, { color: '#0288d1' });
                            drawnItems.addLayer(poly);
                            // Fit map bounds to the polygon
                            map.fitBounds(poly.getBounds());
                            checkSaveState();
                        }
                    }
                })
                .catch(err => console.error("Error fetching territory: ", err));
        }

        // Save polygon GeoJSON to backend
        function saveTerritory() {
            const repId = document.getElementById('repSelect').value;
            const layers = drawnItems.getLayers();

            if (!repId || layers.length === 0) {
                alert("Please select a Rep and draw a polygon.");
                return;
            }

            // Convert Leaflet Layer to GeoJSON
            const geoJsonData = layers[0].toGeoJSON();

            document.getElementById('saveBtn').innerText = "Saving...";

            fetch('territory_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'save_territory',
                    rep_id: repId,
                    geojson: geoJsonData
                })
            })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('saveBtn').innerText = "Save Territory";

                    if (data.success) {
                        alert('Territory successfully assigned to the Logistics Representative!');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    document.getElementById('saveBtn').innerText = "Save Territory";
                    alert("Network error while saving.");
                    console.error(err);
                });
        }
    </script>
</body>

</html>