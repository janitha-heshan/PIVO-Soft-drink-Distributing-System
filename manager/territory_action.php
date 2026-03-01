<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['StoreManager', 'ShopOwner', 'Admin', 'FactoryOwner'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'save_territory') {
    $repId = intval($input['rep_id'] ?? 0);
    $geoJson = $input['geojson'] ?? null;

    if (!$repId || !$geoJson || empty($geoJson['geometry']['coordinates'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing data. Please select a Rep and draw a polygon.']);
        exit;
    }

    // We expect a Polygon GeoJSON structure: Type: Polygon, Coordinates: [[[lng, lat], [lng, lat], ...]]
    $coordsArray = $geoJson['geometry']['coordinates'][0];
    if (count($coordsArray) < 4) {
        echo json_encode(['success' => false, 'message' => 'A valid territory polygon must have at least 3 points and be closed.']);
        exit;
    }

    // Convert GeoJSON coords to MySQL WKT (Well-Known Text) string format
    // MySQL format: POLYGON((lng lat, lng lat, ...))
    $wktPoints = [];
    foreach ($coordsArray as $point) {
        // Validation for safety
        if (count($point) >= 2) {
            $lng = floatval($point[0]);
            $lat = floatval($point[1]);
            $wktPoints[] = "$lng $lat";
        }
    }

    // Safety check: ensure the first and last points are exactly the same to close the polygon
    $firstPoint = $wktPoints[0];
    $lastPoint = end($wktPoints);
    if ($firstPoint !== $lastPoint) {
        $wktPoints[] = $firstPoint; // auto-close it
    }

    $wktString = "POLYGON((" . implode(", ", $wktPoints) . "))";
    $territoryName = "Assigned Territory (Rep #" . $repId . ")";

    try {
        $supervisorId = $_SESSION['user_id'] ?? 1;

        // First check if an existing territory exists for this user to update, otherwise insert
        $checkStmt = $pdo->prepare("SELECT aa.area_id FROM area_assignments aa WHERE aa.sales_rep_id = ?");
        $checkStmt->execute([$repId]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE geofenced_areas SET boundary_polygon = ST_GeomFromText(?), area_name = ? WHERE area_id = ?");
            $stmt->execute([$wktString, $territoryName, $existing['area_id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO geofenced_areas (area_name, supervisor_id, boundary_polygon) VALUES (?, ?, ST_GeomFromText(?))");
            $stmt->execute([$territoryName, $supervisorId, $wktString]);
            $newAreaId = $pdo->lastInsertId();

            $assignStmt = $pdo->prepare("INSERT INTO area_assignments (sales_rep_id, area_id) VALUES (?, ?)");
            $assignStmt->execute([$repId, $newAreaId]);
        }

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    }
} else if ($action === 'get_territory') {
    // Fetch an existing polygon for a specific rep to pre-fill the map
    $repId = intval($input['rep_id'] ?? 0);

    try {
        $stmt = $pdo->prepare("
            SELECT ST_AsText(ga.boundary_polygon) as wkt 
            FROM area_assignments aa
            JOIN geofenced_areas ga ON aa.area_id = ga.area_id
            WHERE aa.sales_rep_id = ?
        ");
        $stmt->execute([$repId]);
        $data = $stmt->fetch();

        if ($data && !empty($data['wkt'])) {
            echo json_encode(['success' => true, 'wkt' => $data['wkt']]);
        } else {
            echo json_encode(['success' => true, 'wkt' => null]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>