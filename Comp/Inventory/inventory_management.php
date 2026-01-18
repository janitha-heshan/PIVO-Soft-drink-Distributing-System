<?php
require_once '../DataAnalysis/db_connection.php';
header("Content-Type: text/html; charset=utf-8");

// 1. HANDLE INSERT NEW INVENTORY ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert_stock'])) {
    $product_id = intval($_POST['product_id']);
    $initial_quantity = intval($_POST['quantity']);
    
    // Check if it already exists to prevent duplicates
    $check = $conn->query("SELECT inventory_id FROM inventory WHERE product_id = $product_id");
    if ($check->num_rows == 0) {
        $insertQuery = "INSERT INTO inventory (product_id, quantity_in_stock) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $product_id, $initial_quantity);
        $stmt->execute();
    }
}

// 2. HANDLE STOCK UPDATES (Existing Function)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $inventory_id = intval($_POST['inventory_id']);
    $new_quantity = intval($_POST['quantity']);
    
    $updateQuery = "UPDATE inventory SET quantity_in_stock = ? WHERE inventory_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $new_quantity, $inventory_id);
    $stmt->execute();
}

// 3. FETCH PRODUCTS NOT IN INVENTORY (For the Insert Dropdown)
$availableProductsQuery = "
    SELECT p.product_id, p.product_name, p.volume_ml 
    FROM products p 
    LEFT JOIN inventory i ON p.product_id = i.product_id 
    WHERE i.inventory_id IS NULL";
$availableProductsResult = $conn->query($availableProductsQuery);

// 4. FETCH CURRENT INVENTORY
$inventoryQuery = "
    SELECT i.inventory_id, p.product_name, p.volume_ml, i.quantity_in_stock, i.last_updated 
    FROM inventory i
    JOIN products p ON i.product_id = p.product_id
    ORDER BY p.product_name ASC, p.volume_ml DESC";
$inventoryResult = $conn->query($inventoryQuery);

$chartLabels = [];
$chartData = [];
$inventoryItems = [];

while ($row = $inventoryResult->fetch_assoc()) {
    $inventoryItems[] = $row;
    $chartLabels[] = $row['product_name'] . " (" . $row['volume_ml'] . ")";
    $chartData[] = (int)$row['quantity_in_stock'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management - Pivo Holdings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print { .no-print { display: none !important; } }
        /* Smooth transition for side panel */
        #insertPanel { transition: transform 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-slate-50 overflow-x-hidden">
    <div class="relative flex size-full min-h-screen flex-col">
        <div class="no-print">
            <?php 
            include '../DataAnalysis/Navbar.php'; 
            echo generateHeader(basename(__FILE__));
            ?>
        </div>

        <div class="px-6 flex flex-1 justify-center py-5 w-full">
            <div class="flex flex-col flex-1 w-full max-w-[1200px]">
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-[#0d171b] text-[32px] font-bold">Inventory Management</h1>
                        <p class="text-[#4c809a] text-sm">Monitor and manage real-time stock levels.</p>
                    </div>
                    <div class="flex gap-3 no-print">
                        <button onclick="togglePanel()" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold shadow-md transition-colors">
                            + Add New Stock
                        </button>
                        <button onclick="window.print()" class="flex items-center gap-2 bg-[#4c809a] text-white px-4 py-2 rounded-lg font-bold shadow-sm">
                            Export PDF
                        </button>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-[#cfdfe7] shadow-sm mb-8">
                    <h2 class="text-lg font-bold text-[#0d171b] mb-4">Stock Level Distribution</h2>
                    <div class="h-[300px] w-full">
                        <canvas id="inventoryHistogram"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-[#cfdfe7] shadow-sm overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b border-[#cfdfe7]">
                            <tr>
                                <th class="px-4 py-3 font-bold">Product Name</th>
                                <th class="px-4 py-3 font-bold">Size</th>
                                <th class="px-4 py-3 font-bold">Current Stock</th>
                                <th class="px-4 py-3 font-bold">Last Updated</th>
                                <th class="px-4 py-3 font-bold no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($inventoryItems as $item): ?>
                            <tr>
                                <td class="px-4 py-4 font-medium"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="px-4 py-4"><?= htmlspecialchars($item['volume_ml']) ?></td>
                                <td class="px-4 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold <?= $item['quantity_in_stock'] < 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                        <?= number_format($item['quantity_in_stock']) ?> Units
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-slate-500"><?= $item['last_updated'] ?></td>
                                <td class="px-4 py-4 no-print">
                                    <form method="POST" class="flex gap-2">
                                        <input type="hidden" name="inventory_id" value="<?= $item['inventory_id'] ?>">
                                        <input type="number" name="quantity" value="<?= $item['quantity_in_stock'] ?>" class="w-20 border rounded px-2 py-1 text-sm">
                                        <button type="submit" name="update_stock" class="text-blue-600 font-bold hover:underline">Update</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="panelOverlay" onclick="togglePanel()" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden no-print"></div>
    
    <div id="insertPanel" class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 transform translate-x-full no-print">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-xl font-bold text-[#0d171b]">Add New Stock</h2>
                <button onclick="togglePanel()" class="text-slate-400 hover:text-red-500 text-2xl">&times;</button>
            </div>

            <?php if($availableProductsResult->num_rows > 0): ?>
            <form method="POST" class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-bold text-[#4c809a]">Select Product</label>
                    <select name="product_id" required class="w-full border border-[#cfdfe7] rounded-lg p-2.5 text-sm">
                        <?php while($p = $availableProductsResult->fetch_assoc()): ?>
                            <option value="<?= $p['product_id'] ?>">
                                <?= $p['product_name'] ?> (<?= $p['volume_ml'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-bold text-[#4c809a]">Initial Quantity</label>
                    <input type="number" name="quantity" required min="0" placeholder="0" class="w-full border border-[#cfdfe7] rounded-lg p-2.5 text-sm">
                </div>

                <button type="submit" name="insert_stock" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg shadow-md hover:bg-blue-700 transition-colors mt-4">
                    Confirm Insertion
                </button>
            </form>
            <?php else: ?>
                <div class="text-center p-6 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                    <p class="text-slate-500 text-sm italic">All available products are already in inventory.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Toggle Panel Function
        function togglePanel() {
            const panel = document.getElementById('insertPanel');
            const overlay = document.getElementById('panelOverlay');
            
            if (panel.classList.contains('translate-x-full')) {
                panel.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                panel.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // Histogram Chart
        const ctx = document.getElementById('inventoryHistogram').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Units in Stock',
                    data: <?= json_encode($chartData) ?>,
                    backgroundColor: '#4c809a',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>