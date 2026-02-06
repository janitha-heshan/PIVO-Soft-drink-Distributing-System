<?php
require_once 'db_connection.php';
require_once '../../includes/auth.php';
requireRole(['Admin', 'FactoryOwner', 'SalesSupervisor']);

header("Content-Type: text/html; charset=utf-8");

/**
 * 1. GET AVAILABLE TIME FRAMES
 * Fetch unique months from Orders to populate the dropdown.
 */
$timeFrameQuery = "SELECT DISTINCT DATE_FORMAT(order_date, '%Y-%m') as month_val, 
                          DATE_FORMAT(order_date, '%M %Y') as month_label 
                   FROM orders 
                   ORDER BY order_date DESC";
$timeFrameResult = $conn->query($timeFrameQuery);

/**
 * 2. HANDLE SELECTION
 * Default to 'all' or the most recent month.
 */
$selectedTime = isset($_GET['time_frame']) ? $_GET['time_frame'] : 'all';

$whereClause = "WHERE o.delivery_status != 'Cancelled' ";
$displayLabel = "All Time";

if ($selectedTime !== 'all') {
    $whereClause .= " AND DATE_FORMAT(o.order_date, '%Y-%m') = '$selectedTime'";
    $displayLabel = date('F Y', strtotime($selectedTime . "-01"));
}

/**
 * 3. DYNAMIC DATA FETCHING
 */

// Doughnut Chart Query (Unit Sales)
$salesQuery = "
    SELECT p.product_name, SUM(oi.quantity) as total_qty 
    FROM orders o
    INNER JOIN order_items oi ON o.order_id = oi.order_id 
    INNER JOIN products p ON oi.product_id = p.product_id 
    $whereClause
    GROUP BY p.product_name";

$salesResult = $conn->query($salesQuery);
$chartData = [];
if ($salesResult && $salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        $chartData[$row['product_name']] = (int) $row['total_qty'];
    }
}

// Top Selling Items (Filtered by Time)
$topSellingQuery = "
    SELECT p.product_name, SUM(oi.quantity) as total_sold 
    FROM order_items oi 
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id 
    $whereClause
    GROUP BY p.product_name 
    ORDER BY total_sold DESC LIMIT 5";
$topSellingResult = $conn->query($topSellingQuery);

// Highest Income (Filtered by Time)
$highestIncomeQuery = "
    SELECT p.product_name, SUM(oi.quantity * oi.price_at_order) as income 
    FROM order_items oi 
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id 
    $whereClause
    GROUP BY p.product_name 
    ORDER BY income DESC LIMIT 5";
$highestIncomeResult = $conn->query($highestIncomeQuery);

// Top Shops (Filtered by Time)
$vendorIncomeQuery = "
    SELECT s.shop_name, SUM(o.total_amount) as total_spent 
    FROM shops s 
    JOIN orders o ON s.shop_id = o.shop_id 
    $whereClause
    GROUP BY s.shop_name 
    ORDER BY total_spent DESC LIMIT 5";
$vendorIncomeResult = $conn->query($vendorIncomeQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Insights - Pivo Holdings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* PDF/Print Optimization */
        @media print {
            body {
                background-color: white !important;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }

            .shadow-sm {
                border: 1px solid #cfdfe7 !important;
                box-shadow: none !important;
            }

            canvas {
                max-width: 350px !important;
                margin: 0 auto;
            }

            .grid {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
            }

            /* Force unit sales to be full width in print if needed */
            .chart-box {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-slate-50">
    <div class="relative flex size-full min-h-screen flex-col overflow-x-hidden">
        <div class="flex h-full grow flex-col">

            <div class="no-print">
                <?php
                include 'Navbar.php';
                // Assuming generateHeader is a function in Navbar.php
                if (function_exists('generateHeader')) {
                    echo generateHeader(basename(__FILE__));
                }
                ?>
            </div>

            <div class="px-6 flex flex-1 justify-center py-5 w-full print-container">
                <div class="flex flex-col flex-1 w-full max-w-[1200px]">

                    <div class="flex flex-wrap justify-between items-end gap-3 p-4">
                        <div class="flex flex-col gap-3">
                            <p class="text-[#0d171b] text-[32px] font-bold">Sales Insights</p>
                            <p class="text-[#4c809a] text-sm font-normal">
                                Business performance report for <strong><?php echo $displayLabel; ?></strong>.
                            </p>
                        </div>

                        <div class="flex items-end gap-3 no-print">
                            <form method="GET" action="insights.php" class="flex flex-col gap-1">
                                <label for="time_frame" class="text-xs font-bold text-[#4c809a] uppercase">Time
                                    Frame</label>
                                <select name="time_frame" id="time_frame" onchange="this.form.submit()"
                                    class="bg-white border border-[#cfdfe7] text-[#0d171b] text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 h-[42px]">
                                    <option value="all" <?php echo ($selectedTime == 'all') ? 'selected' : ''; ?>>All Time
                                    </option>
                                    <?php
                                    $timeFrameResult->data_seek(0); // Reset pointer
                                    while ($tf = $timeFrameResult->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $tf['month_val']; ?>" <?php echo ($selectedTime == $tf['month_val']) ? 'selected' : ''; ?>>
                                            <?php echo $tf['month_label']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </form>

                            <button onclick="window.print()"
                                class="flex items-center gap-2 bg-[#4c809a] hover:bg-[#3a6377] text-white px-4 rounded-lg text-sm font-bold transition-colors shadow-sm h-[42px]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M6 9V2h12v7"></path>
                                    <path
                                        d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                    </path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                                Print PDF
                            </button>
                        </div>
                    </div>

                    <div class="p-4 chart-box">
                        <div class="flex flex-col gap-2 rounded-xl border border-[#cfdfe7] p-6 bg-white shadow-sm">
                            <p class="text-[#0d171b] text-base font-bold mb-4">Unit Sales Distribution</p>
                            <div class="flex justify-center items-center min-h-[350px]">
                                <?php if (!empty($chartData)): ?>
                                    <div class="w-full max-w-md">
                                        <canvas id="salesChart"></canvas>
                                    </div>
                                <?php else: ?>
                                    <div
                                        class="text-center p-10 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200 w-full">
                                        <p class="text-slate-500 font-medium">No sales data found for this period.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4">

                        <div class="bg-white rounded-xl border border-[#cfdfe7] shadow-sm overflow-hidden">
                            <p class="p-4 font-bold text-[#0d171b] border-b bg-slate-50">Top Selling Items</p>
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    <?php if ($topSellingResult && $topSellingResult->num_rows > 0): ?>
                                        <?php while ($row = $topSellingResult->fetch_assoc()): ?>
                                            <tr>
                                                <td class="px-4 py-3 text-slate-700"><?php echo $row['product_name']; ?></td>
                                                <td class="px-4 py-3 text-right font-bold text-[#4c809a]">
                                                    <?php echo number_format($row['total_sold']); ?> units</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-center text-slate-400 italic">No records
                                                found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-white rounded-xl border border-[#cfdfe7] shadow-sm overflow-hidden">
                            <p class="p-4 font-bold text-[#0d171b] border-b bg-slate-50">Highest Revenue Items</p>
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    <?php if ($highestIncomeResult && $highestIncomeResult->num_rows > 0): ?>
                                        <?php while ($row = $highestIncomeResult->fetch_assoc()): ?>
                                            <tr>
                                                <td class="px-4 py-3 text-slate-700"><?php echo $row['product_name']; ?></td>
                                                <td class="px-4 py-3 text-right font-bold text-green-600">Rs.
                                                    <?php echo number_format($row['income'], 2); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-center text-slate-400 italic">No records
                                                found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-white rounded-xl border border-[#cfdfe7] shadow-sm overflow-hidden">
                            <p class="p-4 font-bold text-[#0d171b] border-b bg-slate-50">Top Shops by Value</p>
                            <table class="w-full text-left text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    <?php if ($vendorIncomeResult && $vendorIncomeResult->num_rows > 0): ?>
                                        <?php while ($row = $vendorIncomeResult->fetch_assoc()): ?>
                                            <tr>
                                                <td class="px-4 py-3 text-slate-700"><?php echo $row['shop_name']; ?></td>
                                                <td class="px-4 py-3 text-right font-bold text-slate-900">Rs.
                                                    <?php echo number_format($row['total_spent'], 2); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-center text-slate-400 italic">No records
                                                found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="hidden print:block text-center text-[10px] text-slate-400 mt-10 border-t pt-4">
                        Pivo Holdings Business Intelligence Report | Generated on: <?php echo date('Y-m-d H:i:s'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if (!empty($chartData)): ?>
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode(array_keys($chartData)); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_values($chartData)); ?>,
                        backgroundColor: ['#4c809a', '#078836', '#eab308', '#ef4444', '#8b5cf6', '#3b82f6', '#f97316'],
                        hoverOffset: 20,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((context.raw / total) * 100).toFixed(1);
                                    return ` ${context.label}: ${context.raw} units (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>