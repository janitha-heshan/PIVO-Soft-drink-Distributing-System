<?php 
header("Content-Type: text/html; charset=utf-8"); 

$currentMonth = date('M'); // e.g., "October"
$lastMonth = date('M', strtotime('-1 month'));
$monthBeforeLast = date('M', strtotime('-2 months'));
$nextMonth = date('M', strtotime('+1 month'));
$monthAfterNext = date('M', strtotime('+2 months'));
$twoMonthsAfterNext = date('M', strtotime('+3 months'));

// percentages for each drink
$percentages = [
    'Coffee' => 15,
    'Tea' => 40,
    'Juice' => 75,
    'Smoothie' => 50,
    'Soda' => 30
];
$totalSales = 12500; // Total sales for display
$lastMonthChange = 15; // Percentage change for last month

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: sans-serif; background: #fff; padding: 20px; }
        .chart-container { width: 100%; max-width: 800px; height: 250px; margin: auto; }
    </style>
</head>
<body>
<div class="relative flex size-full min-h-screen flex-col bg-slate-50 overflow-x-hidden">
    <div class="flex h-full grow flex-col">
        <?php
        include 'Navbar.php';
        $current_page = basename(__FILE__);
        echo generateHeader($current_page);
        ?>
        <div class="px-40 flex flex-1 justify-center py-5">
            <div class="flex flex-col max-w-[960px] flex-1">
                <div class="flex flex-wrap justify-between gap-3 p-4">
                    <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight min-w-72">Sales Overview</p>
                </div>
                <div class="flex flex-wrap gap-4 px-4 py-6">
                    <!-- Sales by Drink Type -->
                    <!-- <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-lg border border-[#cfdfe7] p-6">
                        <p class="text-[#0d171b] text-base font-medium leading-normal">Sales by Drink Type</p>
                        <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight truncate">12,500</p>
                        <div class="flex gap-1">
                            <p class="text-[#4c809a] text-base font-normal leading-normal">Last Month</p>
                            <p class="text-[#078836] text-base font-medium leading-normal">+15%</p>
                        </div>
                        <div class="grid min-h-[180px] gap-x-4 gap-y-6 grid-cols-[auto_1fr] items-center py-3">
                            <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Coffee</p>
                            <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                            <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Tea</p>
                            <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                            <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Juice</p>
                            <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                            <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Smoothie</p>
                            <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                            <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Soda</p>
                            <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                        </div>
                    </div> -->
                    <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-lg border border-[#cfdfe7] p-6">
                        <p class="text-[#0d171b] text-base font-medium leading-normal">Sales by Drink Type</p>
                        <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight truncate"><?php echo number_format($totalSales); ?></p>
                        <div class="flex gap-1">
                            <p class="text-[#4c809a] text-base font-normal leading-normal">Last Month</p>
                            <p class="text-[#078836] text-base font-medium leading-normal">+<?php echo $lastMonthChange; ?>%</p>
                        </div>
                        <div class="grid min-h-[30px] gap-y-4">
                            <?php foreach ($percentages as $drink => $percent) : ?>
                                <div class="flex items-center gap-2">
                                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em] w-20"><?php echo $drink; ?></p>
                                    <div class="flex-1 h-4 bg-[#e7eff3] rounded relative">
                                        <div class="absolute top-0 left-0 h-full bg-[#4c809a] rounded" style="width: <?php echo $percent; ?>%;"></div>
                                    </div>
                                    <p class="text-[#0d171b] text-[13px] font-medium leading-normal w-8 text-right"><?php echo $percent; ?>%</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Sales Prediction Chart -->
                    <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-lg border border-[#cfdfe7] p-6">
                        <p class="text-[#0d171b] text-base font-medium leading-normal">Sales Prediction (Next 3 Months)</p>
                        <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight truncate">15,000</p>
                        <div class="flex gap-1">
                            <p class="text-[#4c809a] text-base font-normal leading-normal">Current Month</p>
                            <p class="text-[#078836] text-base font-medium leading-normal">+10%</p>
                        </div>
                        <div class="flex min-h-[180px] flex-1 flex-col gap-8 py-4">
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');

// Gradient fill
const gradient = ctx.createLinearGradient(0, 0, 0, 250);
gradient.addColorStop(0, 'rgba(231, 239, 243, 1)');
gradient.addColorStop(1, 'rgba(231, 239, 243, 0)');

// Pass PHP variables to JS
const labels = [
    "<?php echo $monthBeforeLast; ?>",
    "<?php echo $lastMonth; ?>",
    "<?php echo $currentMonth; ?>",
    "<?php echo $nextMonth; ?>",
    "<?php echo $monthAfterNext; ?>",
    "<?php echo $twoMonthsAfterNext; ?>"
];

const values = [21, 41, 93, 33, 101, 61];

const data = {
    labels: labels,
    datasets: [{
        label: 'Sales',
        data: values,
        fill: true,
        backgroundColor: gradient,
        borderColor: '#4c809a',
        borderWidth: 3,
        tension: 0.4,
        pointRadius: 5,
        pointBackgroundColor: '#4c809a',
        pointHoverRadius: 7
    }]
};

const config = {
    type: 'line',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                enabled: true,
                backgroundColor: '#fff',
                titleColor: '#4c809a',
                bodyColor: '#0d171b',
                borderColor: '#e7eff3',
                borderWidth: 1,
                cornerRadius: 6,
                padding: 10
            },
            datalabels: {
                color: '#0d171b',
                align: 'top',
                anchor: 'end',
                font: { weight: 'bold', size: 12 },
                formatter: function(value) { return value; }
            }
        },
        scales: {
            x: {
                display: true,
                title: { display: true, text: 'Labels', color: '#4c809a', font: { weight: 'bold', size: 12 } },
                ticks: { color: '#0d171b', font: { size: 12 } },
                grid: { display: false }
            },
            y: {
                display: true,
                title: { display: true, text: 'Sales', color: '#4c809a', font: { weight: 'bold', size: 12 } },
                ticks: { color: '#0d171b', font: { size: 12 } },
                grid: { color: 'rgba(231,239,243,0.5)' }
            }
        }
    },
    plugins: [ChartDataLabels]
};

new Chart(ctx, config);
</script>
</body>
</html>
