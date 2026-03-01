<?php 
require_once 'db_connection.php';
header("Content-Type: text/html; charset=utf-8"); 

// 1. GET FILTER OPTIONS
$shopsResult = $conn->query("SELECT shop_id, shop_name FROM shops ORDER BY shop_name ASC");
$productsResult = $conn->query("SELECT product_id, product_name FROM products ORDER BY product_name ASC");

$selectedVendor = $_GET['vendor_id'] ?? 'all';
$selectedProduct = $_GET['product_id'] ?? 'all';

// 2. CONSTRUCT WHERE CLAUSE
$whereClauses = ["o.delivery_status != 'Cancelled'"];
if ($selectedVendor !== 'all') { $whereClauses[] = "s.shop_id = " . intval($selectedVendor); }
if ($selectedProduct !== 'all') { $whereClauses[] = "p.product_id = " . intval($selectedProduct); }
$whereSql = "WHERE " . implode(" AND ", $whereClauses);

/**
 * 3. DATA FETCHING FOR REGRESSION (Past 3 Months)
 */
$pastMonths = [];
for ($i = 3; $i >= 1; $i--) {
    $m = date('Y-m', strtotime("-$i month"));
    $name = date('M', strtotime("-$i month"));
    $q = "SELECT SUM(oi.quantity) as qty FROM order_items oi 
          JOIN orders o ON oi.order_id = o.order_id 
          JOIN shops s ON o.shop_id = s.shop_id
          JOIN products p ON oi.product_id = p.product_id
          $whereSql AND DATE_FORMAT(o.order_date, '%Y-%m') = '$m'";
    $res = $conn->query($q)->fetch_assoc();
    $pastMonths[$name] = (int)($res['qty'] ?? 0);
}

/**
 * 4. CURRENT MONTH DATA
 */
$currentMonthKey = date('Y-m');
$currentMonthName = date('F');
$currQ = "SELECT SUM(oi.quantity) as qty FROM order_items oi 
          JOIN orders o ON oi.order_id = o.order_id 
          JOIN shops s ON o.shop_id = s.shop_id
          JOIN products p ON oi.product_id = p.product_id
          $whereSql AND DATE_FORMAT(o.order_date, '%Y-%m') = '$currentMonthKey'";
$currRes = $conn->query($currQ)->fetch_assoc();
$actualCurrentSales = (int)($currRes['qty'] ?? 0);

/**
 * 5. LINEAR REGRESSION MATH
 */
$y_values = array_values($pastMonths);
$x_values = [1, 2, 3]; 
$n = count($x_values);
$sumX = array_sum($x_values);
$sumY = array_sum($y_values);
$sumXY = 0; $sumX2 = 0;
for($i=0; $i<$n; $i++) {
    $sumXY += ($x_values[$i] * $y_values[$i]);
    $sumX2 += ($x_values[$i] ** 2);
}
$denom = ($n * $sumX2 - $sumX**2);
$slope = ($denom != 0) ? ($n * $sumXY - $sumX * $sumY) / $denom : 0;
$intercept = ($sumY - $slope * $sumX) / $n;

$projectedNext = max(0, round(($slope * 5) + $intercept));
$nextMonthName = date('F', strtotime('+1 month'));

$growthPct = ($actualCurrentSales > 0) 
    ? (($projectedNext - $actualCurrentSales) / $actualCurrentSales) * 100 
    : 100;

$graphPredictions = [];
for ($x = 4; $x <= 6; $x++) {
    $name = date('M', strtotime("+" . ($x-3) . " month"));
    $graphPredictions[$name] = max(0, round(($slope * $x) + $intercept));
}

// 6. MAIN TABLE DATA
$salesDataQuery = "
    SELECT s.shop_name, p.product_name, SUM(oi.quantity) as units_sold, SUM(oi.quantity * oi.price_at_order) as income
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN shops s ON o.shop_id = s.shop_id
    JOIN products p ON oi.product_id = p.product_id
    $whereSql
    GROUP BY s.shop_name, p.product_name
    ORDER BY income DESC";
$salesDataResult = $conn->query($salesDataQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Overview - Pivo Holdings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* PDF/Print Optimization */
        @media print {
            body { background-color: white !important; }
            .no-print { display: none !important; }
            .print-container { width: 100% !important; max-width: 100% !important; padding: 0 !important; }
            .shadow-sm { border: 1px solid #cfdfe7 !important; box-shadow: none !important; }
            canvas { max-width: 500px !important; margin: 0 auto; }
            .grid { display: grid !important; grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; }
            .bg-white { border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50">
    <div class="relative flex size-full min-h-screen flex-col">
        <div class="no-print">
            <?php
            include 'Navbar.php';
            $current_page = basename(__FILE__);
            echo generateHeader($current_page);
            ?>
        </div>
        
        <div class="px-10 lg:px-40 py-10 print-container">
            <div class="max-w-[1000px] mx-auto bg-white p-8 rounded-xl border border-[#cfdfe7] shadow-sm">
                
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-[#0d171b]">Sales Performance & Forecast</h1>
                    <button onclick="window.print()" class="no-print flex items-center gap-2 bg-[#4c809a] hover:bg-[#3a6377] text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Print PDF
                    </button>
                </div>

                <form method="GET" class="no-print grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <select name="vendor_id" onchange="this.form.submit()" class="border-[#cfdfe7] rounded-lg h-12 px-3">
                        <option value="all">All Vendors</option>
                        <?php $shopsResult->data_seek(0); while($s = $shopsResult->fetch_assoc()): ?>
                            <option value="<?= $s['shop_id'] ?>" <?= $selectedVendor == $s['shop_id'] ? 'selected' : '' ?>><?= $s['shop_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <select name="product_id" onchange="this.form.submit()" class="border-[#cfdfe7] rounded-lg h-12 px-3">
                        <option value="all">All Products</option>
                        <?php $productsResult->data_seek(0); while($p = $productsResult->fetch_assoc()): ?>
                            <option value="<?= $p['product_id'] ?>" <?= $selectedProduct == $p['product_id'] ? 'selected' : '' ?>><?= $p['product_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </form>

                <div class="mb-10">
                    <h3 class="text-lg font-bold text-[#0d171b] mb-4">Prediction Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-slate-50 rounded-lg border border-[#cfdfe7]">
                            <p class="text-xs font-bold text-[#4c809a] uppercase mb-1">Current Month</p>
                            <p class="text-xl font-bold text-[#0d171b]"><?= $currentMonthName ?></p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-lg border border-[#cfdfe7]">
                            <p class="text-xs font-bold text-[#4c809a] uppercase mb-1">Current Sales</p>
                            <p class="text-xl font-bold text-[#0d171b]"><?= number_format($actualCurrentSales) ?> Units</p>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-xs font-bold text-blue-600 uppercase mb-1">Expected (<?= $nextMonthName ?>)</p>
                            <p class="text-xl font-bold text-blue-900"><?= number_format($projectedNext) ?> Units</p>
                        </div>
                        <div class="p-4 rounded-lg border <?= $growthPct >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?>">
                            <p class="text-xs font-bold <?= $growthPct >= 0 ? 'text-green-600' : 'text-red-600' ?> uppercase mb-1">Trend Forecast</p>
                            <p class="text-xl font-bold <?= $growthPct >= 0 ? 'text-green-900' : 'text-red-900' ?>">
                                <?= ($growthPct >= 0 ? '+' : '') . number_format($growthPct, 1) ?>%
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-10 chart-box">
                    <h3 class="text-lg font-bold text-[#0d171b] mb-4">6-Month Demand Trend</h3>
                    <div class="h-[350px] w-full">
                        <canvas id="forecastChart"></canvas>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-[#0d171b] mb-4">Detailed Sales Data</h2>
                <div class="overflow-hidden rounded-lg border border-[#cfdfe7]">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 font-bold">Vendor / Product</th>
                                <th class="px-4 py-3 font-bold text-right">Units Sold</th>
                                <th class="px-4 py-3 font-bold text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if($salesDataResult->num_rows > 0): ?>
                                <?php while($row = $salesDataResult->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-[#0d171b]"><?= htmlspecialchars($row['shop_name']) ?></div>
                                            <div class="text-xs text-[#4c809a]"><?= htmlspecialchars($row['product_name']) ?></div>
                                        </td>
                                        <td class="px-4 py-4 text-right"><?= number_format($row['units_sold']) ?></td>
                                        <td class="px-4 py-4 text-right font-bold text-green-700">Rs. <?= number_format($row['income'], 2) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="px-4 py-10 text-center text-slate-400">No data found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="hidden print:block text-center text-[10px] text-slate-400 mt-10 border-t pt-4">
                    Pivo Holdings Vendor Performance Report | Generated on: <?php echo date('Y-m-d H:i'); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('forecastChart').getContext('2d');
    const labels = [...<?= json_encode(array_keys($pastMonths)) ?>, ...<?= json_encode(array_keys($graphPredictions)) ?>];
    const pastData = [...<?= json_encode(array_values($pastMonths)) ?>, null, null, null];
    // This bridges the actual line to the predicted line
    const predData = [null, null, <?= json_encode(end($pastMonths)) ?>, ...<?= json_encode(array_values($graphPredictions)) ?>];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Actual Units',
                    data: pastData,
                    borderColor: '#4c809a',
                    backgroundColor: '#4c809a',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 6
                },
                {
                    label: 'Predicted Trend',
                    data: predData,
                    borderColor: '#078836',
                    borderDash: [5, 5],
                    tension: 0.3,
                    fill: false,
                    pointRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    </script>
</body>
</html>