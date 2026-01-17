<?php
require_once 'db_connection.php'; 
header("Content-Type: text/html; charset=utf-8"); 

/**
 * 1. GET AVAILABLE TIME FRAMES
 * Fetch all unique months/years from the Orders table to populate the dropdown.
 */
$timeFrameQuery = "SELECT DISTINCT DATE_FORMAT(order_date, '%Y-%m') as month_val, 
                          DATE_FORMAT(order_date, '%M %Y') as month_label 
                   FROM Orders 
                   ORDER BY order_date DESC";
$timeFrameResult = $conn->query($timeFrameQuery);

/**
 * 2. HANDLE SELECTION
 * Determine if the user selected a specific month or "All Time".
 * Default to the last month if no selection is made.
 */
$selectedTime = isset($_GET['time_frame']) ? $_GET['time_frame'] : date('Y-m', strtotime('last month'));

$whereClause = "WHERE o.delivery_status != 'Cancelled' ";
$displayLabel = "All Time";

if ($selectedTime !== 'all') {
    $whereClause .= " AND DATE_FORMAT(o.order_date, '%Y-%m') = '$selectedTime'";
    // Create a pretty label for the UI (e.g., "December 2025")
    $displayLabel = date('F Y', strtotime($selectedTime . "-01"));
}

/**
 * 3. DYNAMIC DATA FETCHING
 */

// Doughnut Chart Query
$salesQuery = "
    SELECT p.product_name, SUM(oi.quantity) as total_qty 
    FROM Orders o
    INNER JOIN Order_Items oi ON o.order_id = oi.order_id 
    INNER JOIN Products p ON oi.product_id = p.product_id 
    $whereClause
    GROUP BY p.product_name";

$salesResult = $conn->query($salesQuery);
$chartData = [];
if ($salesResult && $salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        $chartData[$row['product_name']] = (int)$row['total_qty'];
    }
}

// Top Selling Items (Filtered by Time)
$topSellingQuery = "
    SELECT p.product_name, SUM(oi.quantity) as total_sold 
    FROM Order_Items oi 
    JOIN Orders o ON oi.order_id = o.order_id
    JOIN Products p ON oi.product_id = p.product_id 
    $whereClause
    GROUP BY p.product_name 
    ORDER BY total_sold DESC LIMIT 5";
$topSellingResult = $conn->query($topSellingQuery);

// Highest Income (Filtered by Time)
$highestIncomeQuery = "
    SELECT p.product_name, SUM(oi.quantity * oi.price_at_order) as income 
    FROM Order_Items oi 
    JOIN Orders o ON oi.order_id = o.order_id
    JOIN Products p ON oi.product_id = p.product_id 
    $whereClause
    GROUP BY p.product_name 
    ORDER BY income DESC LIMIT 5";
$highestIncomeResult = $conn->query($highestIncomeQuery);

// Top Shops (Filtered by Time)
$vendorIncomeQuery = "
    SELECT s.shop_name, SUM(o.total_amount) as total_spent 
    FROM Shops s 
    JOIN Orders o ON s.shop_id = o.shop_id 
    $whereClause
    GROUP BY s.shop_name 
    ORDER BY total_spent DESC LIMIT 5";
$vendorIncomeResult = $conn->query($vendorIncomeQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insights - Pivo Holdings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50">
<div class="relative flex size-full min-h-screen flex-col overflow-x-hidden">
    <div class="flex h-full grow flex-col">
        <?php
        include 'Navbar.php';
        $current_page = basename(__FILE__);
        echo generateHeader($current_page);
        ?>


        <div class="px-6 flex flex-1 justify-center py-5 w-full">
            <div class="flex flex-col flex-1 w-full max-w-[1200px]">
                
                <div class="flex flex-wrap justify-between items-end gap-3 p-4">
                    <div class="flex flex-col gap-3">
                        <p class="text-[#0d171b] text-[32px] font-bold">Sales Insights</p>
                        <p class="text-[#4c809a] text-sm font-normal">
                            Real-time breakdown of your business performance for <strong><?php echo $displayLabel; ?></strong>.
                        </p>
                    </div>
                    
                    <form method="GET" action="insights.php" class="flex flex-col gap-1">
                        <label for="time_frame" class="text-xs font-bold text-[#4c809a] uppercase">Select Time Frame</label>
                        <select name="time_frame" id="time_frame" onchange="this.form.submit()" 
                                class="bg-white border border-[#cfdfe7] text-[#0d171b] text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="all" <?php echo ($selectedTime == 'all') ? 'selected' : ''; ?>>All Time</option>
                            <?php while($tf = $timeFrameResult->fetch_assoc()): ?>
                                <option value="<?php echo $tf['month_val']; ?>" <?php echo ($selectedTime == $tf['month_val']) ? 'selected' : ''; ?>>
                                    <?php echo $tf['month_label']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>

                <div class="p-4">
                    <div class="flex flex-col gap-2 rounded-xl border border-[#cfdfe7] p-6 bg-white shadow-sm">
                        <p class="text-[#0d171b] text-base font-bold mb-4">Unit Sales: <?php echo $displayLabel; ?></p>
                        
                        <div class="flex justify-center items-center min-h-[350px]">
                            <?php if(!empty($chartData)): ?>
                                <div class="w-full max-w-md">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="text-center p-10 bg-slate-50 rounded-lg border-2 border-dashed border-slate-200">
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
                                <?php if($topSellingResult->num_rows > 0): ?>
                                    <?php while($row = $topSellingResult->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-4 py-3 text-slate-700"><?php echo $row['product_name']; ?></td>
                                        <td class="px-4 py-3 text-right font-bold text-[#4c809a]"><?php echo number_format($row['total_sold']); ?> units</td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="px-4 py-3 text-center text-slate-400">No data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white rounded-xl border border-[#cfdfe7] shadow-sm overflow-hidden">
                        <p class="p-4 font-bold text-[#0d171b] border-b bg-slate-50">Highest Revenue Items</p>
                        <table class="w-full text-left text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <?php if($highestIncomeResult->num_rows > 0): ?>
                                    <?php while($row = $highestIncomeResult->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-4 py-3 text-slate-700"><?php echo $row['product_name']; ?></td>
                                        <td class="px-4 py-3 text-right font-bold text-green-600">Rs. <?php echo number_format($row['income'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="px-4 py-3 text-center text-slate-400">No data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white rounded-xl border border-[#cfdfe7] shadow-sm overflow-hidden">
                        <p class="p-4 font-bold text-[#0d171b] border-b bg-slate-50">Top Shops by Value</p>
                        <table class="w-full text-left text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <?php if($vendorIncomeResult->num_rows > 0): ?>
                                    <?php while($row = $vendorIncomeResult->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-4 py-3 text-slate-700"><?php echo $row['shop_name']; ?></td>
                                        <td class="px-4 py-3 text-right font-bold text-slate-900">Rs. <?php echo number_format($row['total_spent'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="px-4 py-3 text-center text-slate-400">No data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
<?php if(!empty($chartData)): ?>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_keys($chartData)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($chartData)); ?>,
                backgroundColor: ['#4c809a', '#078836', '#eab308', '#ef4444', '#8b5cf6'],
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
                        label: function(context) {
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