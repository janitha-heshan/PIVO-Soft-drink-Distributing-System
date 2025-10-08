<?php
header("Content-Type: text/html; charset=utf-8");

// Example PHP variables for last month sales
$lastMonthSales = [
    'Coffee' => 5000,
    'Tea' => 3000,
    'Juice' => 2000,
    'Smoothie' => 1500,
    'Soda' => 1000
];
$totalSales = array_sum($lastMonthSales);
?>
<html>
<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans:wght@400;500;700;900&amp;family=Work+Sans:wght@400;500;700;900" />

    <title>Insights</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="relative flex size-full min-h-screen flex-col bg-slate-50 overflow-x-hidden">
    <div class="flex h-full grow flex-col">
        <?php
        include 'Navbar.php';
        $current_page = basename(__FILE__);
        echo generateHeader($current_page);
        ?>
        <div class="px-6 flex flex-1 justify-center py-5 w-full">
            <div class="flex flex-col flex-1 w-full max-w-full">
                <div class="flex flex-wrap justify-between gap-3 p-4">
                    <div class="flex flex-col gap-3">
                        <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight">Sales Insights</p>
                        <p class="text-[#4c809a] text-sm font-normal leading-normal">Analyze sales data to identify trends and opportunities.</p>
                    </div>
                </div>

                <!-- Last Month Sales Section -->
                <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Last Month Sales</h3>
                <div class="flex justify-center p-4">
                    <div class="w-full max-w-md">
                        <canvas id="lastMonthSalesChart"></canvas>
                    </div>
                </div>

                <!-- Top Selling Items -->
                <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Top Selling Items</h3>
                <div class="px-4 py-3 @container">
                    <div class="flex overflow-hidden rounded-lg border border-[#cfdfe7] bg-slate-50">
                        <table class="flex-1">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Item</th>
                                    <th class="px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t border-t-[#cfdfe7]">
                                    <td class="h-[72px] px-4 py-2 text-[#0d171b] text-sm font-normal leading-normal">Organic Apples</td>
                                    <td class="h-[72px] px-4 py-2 text-[#4c809a] text-sm font-normal leading-normal">1200</td>
                                </tr>
                                <tr class="border-t border-t-[#cfdfe7]">
                                    <td class="h-[72px] px-4 py-2 text-[#0d171b] text-sm font-normal leading-normal">Whole Wheat Bread</td>
                                    <td class="h-[72px] px-4 py-2 text-[#4c809a] text-sm font-normal leading-normal">1150</td>
                                </tr>
                                <tr class="border-t border-t-[#cfdfe7]">
                                    <td class="h-[72px] px-4 py-2 text-[#0d171b] text-sm font-normal leading-normal">Free-Range Eggs</td>
                                    <td class="h-[72px] px-4 py-2 text-[#4c809a] text-sm font-normal leading-normal">1100</td>
                                </tr>
                                <tr class="border-t border-t-[#cfdfe7]">
                                    <td class="h-[72px] px-4 py-2 text-[#0d171b] text-sm font-normal leading-normal">Almond Milk</td>
                                    <td class="h-[72px] px-4 py-2 text-[#4c809a] text-sm font-normal leading-normal">1050</td>
                                </tr>
                                <tr class="border-t border-t-[#cfdfe7]">
                                    <td class="h-[72px] px-4 py-2 text-[#0d171b] text-sm font-normal leading-normal">Avocados</td>
                                    <td class="h-[72px] px-4 py-2 text-[#4c809a] text-sm font-normal leading-normal">1000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Start -->

           
            <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Highest Income Items</h3>
            <div class="px-4 py-3 @container">
              <div class="flex overflow-hidden rounded-lg border border-[#cfdfe7] bg-slate-50">
                <table class="flex-1">
                  <thead>
                    <tr class="bg-slate-50">
                      <th class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Item</th>
                      <th class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Income Generated
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Organic Apples
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$6,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Whole Wheat Bread
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,750</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Free-Range Eggs
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,500</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Almond Milk
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,250</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Avocados</td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,000</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
            </div>
            <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Vendors by Income Type</h3>
            <div class="px-4 py-3 @container">
              <div class="flex overflow-hidden rounded-lg border border-[#cfdfe7] bg-slate-50">
                <table class="flex-1">
                  <thead>
                    <tr class="bg-slate-50">
                      <th class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Vendor</th>
                      <th class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Income Type
                      </th>
                      <th class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Income Amount
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Green Valley Farms
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Produce</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$15,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Sunrise Bakery
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Bakery</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$12,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Happy Hens
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Dairy</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$10,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Nutty Delights
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Beverages
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$8,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Creamy Greens
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Produce</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$7,500</td>
                    </tr>
                  </tbody>
                </table>
              </div>
                <!-- end -->
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('lastMonthSalesChart').getContext('2d');

// PHP data to JS
const lastMonthLabels = <?php echo json_encode(array_keys($lastMonthSales)); ?>;
const lastMonthValues = <?php echo json_encode(array_values($lastMonthSales)); ?>;

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: lastMonthLabels,
        datasets: [{
            data: lastMonthValues,
            backgroundColor: ['#4c809a', '#078836', '#eab308', '#ef4444', '#8b5cf6'],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#0d171b',
                    font: { size: 14, weight: 'bold' }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let value = context.raw;
                        let total = context.dataset.data.reduce((a,b) => a+b, 0);
                        let percent = ((value / total) * 100).toFixed(1);
                        return context.label + ': ' + value + ' (' + percent + '%)';
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>
