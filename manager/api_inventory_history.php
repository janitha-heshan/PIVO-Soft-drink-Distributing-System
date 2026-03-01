<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !in_array($_SESSION['role'], ['StoreManager', 'ShopOwner', 'Admin', 'FactoryOwner'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Fetch logs grouped by date and product
    $stmt = $pdo->query("
        SELECT 
            DATE(l.timestamp) as log_date,
            p.product_name,
            p.volume_ml,
            AVG(l.new_quantity) as avg_quantity
        FROM inventory_logs l
        JOIN products p ON l.product_id = p.product_id
        GROUP BY DATE(l.timestamp), l.product_id
        ORDER BY log_date ASC
    ");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for Chart.js
    $dates = [];
    $datasets = [];

    foreach ($logs as $row) {
        $date = $row['log_date'];
        $product = $row['product_name'] . ' (' . $row['volume_ml'] . ')';
        $qty = round($row['avg_quantity']);

        if (!in_array($date, $dates)) {
            $dates[] = $date;
        }

        if (!isset($datasets[$product])) {
            $datasets[$product] = [];
        }
        $datasets[$product][$date] = $qty;
    }

    sort($dates); // Ensure chronological order

    // Fill missing dates with previous values
    $formattedDatasets = [];
    foreach ($datasets as $product => $dataPoints) {
        $lastVal = 0;
        $productData = [];
        foreach ($dates as $date) {
            if (isset($dataPoints[$date])) {
                $lastVal = $dataPoints[$date];
            }
            $productData[] = $lastVal;
        }

        $r = rand(50, 200);
        $g = rand(50, 200);
        $b = rand(50, 200);

        $formattedDatasets[] = [
            'label' => $product,
            'data' => $productData,
            'borderColor' => "rgba($r, $g, $b, 1)",
            'backgroundColor' => "rgba($r, $g, $b, 0.1)",
            'fill' => true,
            'tension' => 0.4 // Smooth lines
        ];
    }

    echo json_encode([
        'success' => true,
        'labels' => $dates,
        'datasets' => $formattedDatasets
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>