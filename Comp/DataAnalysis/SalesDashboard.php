<?php 
require_once 'db_connection.php'; 
header("Content-Type: text/html; charset=utf-8"); 

// Runs Prediction Script
exec("python C:/xampp/htdocs/PIVO-Soft-drink-Distributing-System/scripts/train_and_predict.py");


/**
 * 1. REVENUE HELPERS
 * Reference Date: 2026-01-17 (matching your database dump)
 */
function getMonthlyRevenue($conn, $monthOffset) {
    $sql = "SELECT SUM(total_amount) as total FROM orders 
            WHERE MONTH(order_date) = MONTH('2026-01-17' - INTERVAL $monthOffset MONTH)
            AND YEAR(order_date) = YEAR('2026-01-17' - INTERVAL $monthOffset MONTH)
            AND delivery_status != 'Cancelled'";
    $result = $conn->query($sql);
    if (!$result) return 0;
    $row = $result->fetch_assoc();
    return (float)($row['total'] ?? 0);
}

// URL State Management
$basis = isset($_GET['basis']) ? $_GET['basis'] : 'units'; // 'units' or 'income'
$sort = (isset($_GET['sort']) && $_GET['sort'] == 'ASC') ? 'ASC' : 'DESC';

// Growth Stats (Current Month vs Previous)
$revenueLM = getMonthlyRevenue($conn, 0); // Current Month (Jan 2026)
$revenueB4 = getMonthlyRevenue($conn, 1); // Previous Month (Dec 2025)
$growth = ($revenueB4 > 0) ? round((($revenueLM - $revenueB4) / $revenueB4) * 100, 1) : 0;

/**
 * 2. AGGREGATED PRODUCT PERFORMANCE DATA
 */
$performanceList = [];
$perfQuery = "SELECT p.product_id, p.product_name, p.unit_price, 
              (SELECT IFNULL(SUM(oi.quantity), 0) 
               FROM order_items oi 
               JOIN orders o ON oi.order_id = o.order_id 
               WHERE oi.product_id = p.product_id 
               AND o.order_date >= ('2026-01-17' - INTERVAL 30 DAY) 
               AND o.delivery_status != 'Cancelled') as velocity_30d
              FROM products p
              ORDER BY velocity_30d $sort";

$res = $conn->query($perfQuery);
if ($res) {
    while ($item = $res->fetch_assoc()) {
        $pId = $item['product_id'];
        
        // Fetch 3-month forecast
        $fQuery = "SELECT SUM(predicted_demand) as qty FROM sales_predictions 
                   WHERE product_id = $pId 
                   AND prediction_date > '2026-01-17' 
                   AND prediction_date <= ('2026-01-17' + INTERVAL 3 MONTH)";
        $fRes = $conn->query($fQuery);
        $fQty = ($fRes) ? ($fRes->fetch_assoc()['qty'] ?? 0) : 0;
        
        $item['f_qty'] = (int)$fQty;
        $item['f_val'] = $fQty * $item['unit_price'];
        $performanceList[] = $item;
    }
}

$totalProducts = count($performanceList);
$best = ($sort == 'DESC') ? ($performanceList[0] ?? null) : ($performanceList[$totalProducts-1] ?? null);
$worst = ($sort == 'DESC') ? ($performanceList[$totalProducts-1] ?? null) : ($performanceList[0] ?? null);

/**
 * 3. CHART DATA (Past 3M Actuals + Next 3M Predicted)
 */
$chartLabels = []; $chartValues = [];
// Last 3 Months Actual Revenue
for ($i = 2; $i >= 0; $i--) {
    $chartLabels[] = date('M', strtotime("2026-01-17 -$i months"));
    $chartValues[] = getMonthlyRevenue($conn, $i);
}

// Next 3 Months Predicted Demand (Volume)
$predLine = "SELECT DATE_FORMAT(prediction_date, '%b') as m, SUM(predicted_demand) as d 
             FROM sales_predictions WHERE prediction_date > '2026-01-17' 
             GROUP BY YEAR(prediction_date), MONTH(prediction_date) ORDER BY prediction_date LIMIT 3";
$plRes = $conn->query($predLine);
if($plRes){
    while($row = $plRes->fetch_assoc()) {
        $chartLabels[] = $row['m'] . " (Pred)";
        $chartValues[] = (float)$row['d'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Intelligence | Pivo Holdings</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-[#f8fafc]">
<div class="relative flex size-full min-h-screen flex-col overflow-x-hidden">
    <?php include 'Navbar.php'; echo generateHeader(basename(__FILE__)); ?>

    <div class="px-10 lg:px-40 py-10 flex flex-col gap-8 max-w-[1400px] mx-auto w-full">
        
        <div class="flex flex-wrap justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-black text-[#0d171b]">Sales Intelligence</h1>
                <p class="text-[#4c809a] text-sm font-medium uppercase tracking-wider">Strategic performance & forecasting</p>
            </div>
            <div class="flex bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
                <a href="?basis=units&sort=<?= $sort ?>" class="px-6 py-2 rounded-lg text-sm font-bold transition-all <?= $basis=='units'?'bg-[#4c809a] text-white shadow-md':'text-slate-400 hover:text-slate-600' ?>">Units</a>
                <a href="?basis=income&sort=<?= $sort ?>" class="px-6 py-2 rounded-lg text-sm font-bold transition-all <?= $basis=='income'?'bg-[#4c809a] text-white shadow-md':'text-slate-400 hover:text-slate-600' ?>">Income (Rs.)</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm col-span-1 lg:col-span-2 flex flex-col">
                <p class="text-xs font-bold text-slate-400 uppercase mb-4">6-Month Trend (Actuals vs Predicted)</p>
                <div class="h-48 w-full"><canvas id="salesChart"></canvas></div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center">
                <p class="text-xs font-bold text-slate-400 uppercase">Current Month Revenue</p>
                <h2 class="text-4xl font-black text-slate-900 my-2">Rs. <?= number_format($revenueLM) ?></h2>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded text-xs font-bold <?= $growth >= 0 ? 'bg-green-100 text-green-700':'bg-red-100 text-red-700' ?>">
                        <?= ($growth >= 0 ? '↑ +' : '↓ ') . $growth ?>%
                    </span>
                    <span class="text-slate-400 text-xs">vs Previous Month</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="relative overflow-hidden bg-white p-6 rounded-2xl border-t-4 border-t-green-500 shadow-sm">
                <span class="absolute top-4 right-6 text-5xl font-black text-slate-50 italic opacity-10">TOP 1</span>
                <p class="text-xs font-bold text-green-600 uppercase mb-1">Velocity Leader</p>
                <h3 class="text-2xl font-bold text-slate-900"><?= $best['product_name'] ?? 'N/A' ?></h3>
                <div class="mt-6 flex justify-between items-end">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold">30D Volume</p>
                        <p class="text-xl font-black"><?= number_format($best['velocity_30d'] ?? 0) ?> Units</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400 uppercase font-bold">3M Forecast (<?= ucfirst($basis) ?>)</p>
                        <p class="text-xl font-black text-[#4c809a]"><?= $basis=='units' ? number_format($best['f_qty'] ?? 0) : 'Rs.'.number_format($best['f_val'] ?? 0) ?></p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white p-6 rounded-2xl border-t-4 border-t-red-500 shadow-sm">
                <span class="absolute top-4 right-6 text-5xl font-black text-slate-50 italic opacity-10">RANK <?= $totalProducts ?></span>
                <p class="text-xs font-bold text-red-600 uppercase mb-1">Attention Required</p>
                <h3 class="text-2xl font-bold text-slate-900"><?= $worst['product_name'] ?? 'N/A' ?></h3>
                <div class="mt-6 flex justify-between items-end">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold">30D Volume</p>
                        <p class="text-xl font-black"><?= number_format($worst['velocity_30d'] ?? 0) ?> Units</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400 uppercase font-bold">3M Forecast (<?= ucfirst($basis) ?>)</p>
                        <p class="text-xl font-black text-orange-600"><?= $basis=='units' ? number_format($worst['f_qty'] ?? 0) : 'Rs.'.number_format($worst['f_val'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h4 class="font-bold text-slate-800">Product Performance Ranking</h4>
                <div class="flex gap-2">
                    <a href="?sort=DESC&basis=<?= $basis ?>" class="text-xs font-bold px-4 py-2 rounded-lg <?= $sort=='DESC'?'bg-slate-800 text-white shadow-sm':'bg-white border text-slate-500' ?>">Best First</a>
                    <a href="?sort=ASC&basis=<?= $basis ?>" class="text-xs font-bold px-4 py-2 rounded-lg <?= $sort=='ASC'?'bg-slate-800 text-white shadow-sm':'bg-white border text-slate-500' ?>">Worst First</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50">
                            <th class="px-6 py-4">Rank</th>
                            <th class="px-6 py-4">Product Name</th>
                            <th class="px-6 py-4">30D Velocity</th>
                            <th class="px-6 py-4 text-right">3M Strategic Forecast</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php 
                        $displayRank = ($sort == 'DESC') ? 1 : $totalProducts;
                        foreach($performanceList as $row): 
                            $bestVel = ($best['velocity_30d'] ?? 1) > 0 ? $best['velocity_30d'] : 1;
                            $vPct = ($row['velocity_30d'] / $bestVel) * 100;
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-slate-300 group-hover:text-slate-500">#<?= $displayRank ?></td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900"><?= $row['product_name'] ?></p>
                                <p class="text-[10px] text-slate-400">Price: Rs. <?= number_format($row['unit_price'], 2) ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold w-10"><?= $row['velocity_30d'] ?></span>
                                    <div class="flex-1 h-1.5 bg-slate-100 rounded-full max-w-[100px] overflow-hidden">
                                        <div class="bg-slate-400 h-full" style="width: <?= $vPct ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-black text-[#4c809a]">
                                    <?= $basis=='units' ? number_format($row['f_qty']).' U' : 'Rs. '.number_format($row['f_val']) ?>
                                </p>
                            </td>
                        </tr>
                        <?php ($sort == 'DESC') ? $displayRank++ : $displayRank--; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            data: <?= json_encode($chartValues) ?>,
            borderColor: '#4c809a',
            backgroundColor: 'rgba(76, 128, 154, 0.05)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' } },
            y: { display: false }
        }
    }
});
</script>
</body>
</html>
