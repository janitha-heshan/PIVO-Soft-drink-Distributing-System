<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'ShopOwner', 'Admin', 'FactoryOwner']);

$success = isset($_GET['success']);

// Fetch inventory — volume_ml directly on products (no sizes table)
$inventory = $pdo->query("
    SELECT i.inventory_id, i.quantity_in_stock,
           p.product_id, p.product_name, p.unit_price, p.volume_ml
    FROM inventory i
    JOIN products p ON i.product_id = p.product_id
    ORDER BY p.product_name ASC, p.volume_ml ASC
")->fetchAll();

// Prepare chart data
$chartLabels = [];
$chartData = [];
$chartColors = [];
foreach ($inventory as $item) {
    $chartLabels[] = $item['product_name'] . ' (' . $item['volume_ml'] . ')';
    $chartData[] = (int) $item['quantity_in_stock'];
    $chartColors[] = $item['quantity_in_stock'] < 50 ? 'rgba(217,48,37,0.82)' : 'rgba(22,163,74,0.82)';
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Inventory</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            margin-bottom: 28px;
        }

        .chart-card h2 {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 16px;
        }

        .chart-wrap {
            position: relative;
            height: 260px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            width: 400px;
        }

        .flash-success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
    </style>
    <script>
        function openEditModal(id, name, stock, price) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('inv_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_price').value = price;
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

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
            <?php $dashLink = ($_SESSION['role'] === 'FactoryOwner') ? '../factory/dashboard.php' : 'dashboard.php'; ?>
            <a href="<?php echo $dashLink; ?>">Dashboard</a>
            <a href="inventory.php" class="active">Inventory</a>
            <a href="manage_products.php">Products</a>
            <a href="returns.php">Returns</a>
            <a href="manage_territories.php">Territories</a>

            <div class="user-menu" style="position:relative; margin-left:14px;">
                <div onclick="toggleDropdown()" style="cursor:pointer; display:flex; align-items:center;">
                    <button class="avatar" style="background:#5e35b1; margin:0;">
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
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
            <h1>Inventory Management</h1>
        </div>

        <?php if ($success): ?>
            <div class="flash-success">✅ Inventory updated successfully.</div>
        <?php endif; ?>

        <div class="row" style="display:flex; gap: 24px; flex-wrap:wrap;">
            <!-- Stock Histogram -->
            <div class="chart-card" style="flex:1; min-width:400px;">
                <h2>📊 Stock Level Overview — Threshold: 50 units</h2>
                <div class="chart-wrap">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>

            <!-- History Line Chart -->
            <div class="chart-card" style="flex:1; min-width:400px;">
                <h2>📈 Inventory Trends & History</h2>
                <div class="chart-wrap">
                    <canvas id="historyChart"></canvas>
                </div>
            </div>
        </div>

        <section class="summary-card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Price (LKR)</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['volume_ml']) ?></td>
                            <td><?= number_format($item['unit_price'], 2) ?></td>
                            <td
                                style="font-weight:bold; <?= $item['quantity_in_stock'] < 50 ? 'color:#d93025;' : 'color:#0f5132;' ?>">
                                <?= $item['quantity_in_stock'] ?>
                            </td>
                            <td>
                                <button
                                    onclick="openEditModal(<?= $item['inventory_id'] ?>, '<?= $item['product_name'] ?>', <?= $item['quantity_in_stock'] ?>, <?= $item['unit_price'] ?>)"
                                    class="link-btn">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <h2>Update Stock</h2>
                <form action="product_action.php" method="POST" class="form">
                    <input type="hidden" name="action" value="update_stock">
                    <input type="hidden" name="inventory_id" id="inv_id">
                    <label>Product <input type="text" id="edit_name" disabled /></label>
                    <label>Stock Quantity <input type="number" name="quantity" id="edit_stock" required /></label>
                    <label>Unit Price <input type="number" name="price" id="edit_price" step="0.01" required /></label>
                    <div style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" onclick="closeEditModal()" class="secondary full">Cancel</button>
                        <button type="submit" class="primary full">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('stockChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Units in Stock',
                    data: <?= json_encode($chartData) ?>,
                    backgroundColor: <?= json_encode($chartColors) ?>,
                    borderColor: <?= json_encode(array_map(fn($c) => str_replace('0.82', '1', $c), $chartColors)) ?>,
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                    x: { grid: { display: false }, ticks: { maxRotation: 30, font: { size: 11 } } }
                },
                animation: {
                    onComplete: function (animation) {
                        const chart = animation.chart;
                        const ctx2 = chart.ctx;
                        const yScale = chart.scales.y;
                        const xStart = chart.chartArea.left;
                        const xEnd = chart.chartArea.right;
                        const y50 = yScale.getPixelForValue(50);
                        ctx2.save();
                        ctx2.beginPath();
                        ctx2.moveTo(xStart, y50);
                        ctx2.lineTo(xEnd, y50);
                        ctx2.strokeStyle = '#d93025';
                        ctx2.lineWidth = 2;
                        ctx2.setLineDash([6, 4]);
                        ctx2.stroke();
                        ctx2.setLineDash([]);
                        ctx2.fillStyle = '#d93025';
                        ctx2.font = '11px sans-serif';
                        ctx2.fillText('Low Stock Threshold (50)', xEnd - 170, y50 - 5);
                        ctx2.restore();
                    }
                }
            }
        });

        // Fetch and Render History Line Chart
        fetch('api_inventory_history.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const hCtx = document.getElementById('historyChart').getContext('2d');
                    new Chart(hCtx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: data.datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                            },
                            scales: {
                                y: { beginAtZero: true },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            });
    </script>

</body>

</html>