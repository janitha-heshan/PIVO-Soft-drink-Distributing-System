<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['FactoryOwner', 'Admin']);

$success = isset($_GET['success']);
$error = $_GET['error'] ?? '';

// Fetch inventory — no sizes table, volume_ml is directly on products
$inventory = $pdo->query("
    SELECT i.inventory_id, i.quantity_in_stock,
           p.product_id, p.product_name, p.unit_price, p.volume_ml, p.description
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
            background: rgba(0, 0, 0, 0.45);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal.open {
            display: flex;
        }

        .modal-content {
            background: #fff;
            padding: 28px;
            border-radius: 14px;
            width: 420px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
        }

        .modal-content h2 {
            margin-bottom: 18px;
            font-size: 1.2rem;
        }

        .badge-low {
            display: inline-block;
            background: #fee2e2;
            color: #d93025;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 100px;
            margin-left: 6px;
        }

        .flash-success {
            background: #dcfce7;
            color: #14532d;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="../assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Factory</span>
        </div>
        <nav class="dash-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php" class="active">Inventory</a>
            <a href="../Comp/DataAnalysis/insights.php">Analytics</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Inventory Management</h1>
        </div>

        <?php if ($success): ?>
            <div class="flash-success">✅ Inventory updated successfully.</div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="flash-error">❌ <?= htmlspecialchars(urldecode($error)) ?></div>
        <?php endif; ?>

        <!-- Stock Histogram -->
        <div class="chart-card">
            <h2>📊 Stock Level Overview — Threshold: 50 units</h2>
            <div class="chart-wrap">
                <canvas id="stockChart"></canvas>
            </div>
        </div>

        <!-- Inventory Table -->
        <section class="summary-card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Description</th>
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
                            <td><?= htmlspecialchars($item['description'] ?? '—') ?></td>
                            <td><?= number_format($item['unit_price'], 2) ?></td>
                            <td
                                style="font-weight:bold; <?= $item['quantity_in_stock'] < 50 ? 'color:#d93025;' : 'color:#0f5132;' ?>">
                                <?= $item['quantity_in_stock'] ?>
                                <?php if ($item['quantity_in_stock'] < 50): ?>
                                    <span class="badge-low">LOW</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="link-btn" onclick="openEdit(
                                <?= $item['inventory_id'] ?>,
                                '<?= htmlspecialchars(addslashes($item['product_name'] . ' ' . $item['volume_ml'])) ?>',
                                <?= $item['quantity_in_stock'] ?>,
                                <?= $item['unit_price'] ?>
                            )">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Update Stock</h2>
            <form action="inventory_action.php" method="POST" class="form">
                <input type="hidden" name="action" value="update_stock" />
                <input type="hidden" name="inventory_id" id="inv_id" />
                <label>Product <input type="text" id="edit_name" disabled /></label>
                <label>Stock Quantity <input type="number" name="quantity" id="edit_stock" min="0" required /></label>
                <label>Unit Price (LKR) <input type="number" name="price" id="edit_price" step="0.01" min="0"
                        required /></label>
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeEdit()" class="secondary full">Cancel</button>
                    <button type="submit" class="primary full">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Chart
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
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            afterLabel: (ctx) => ctx.parsed.y < 50 ? '⚠ Below threshold!' : '✓ Sufficient'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: { font: { size: 12 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, maxRotation: 30 }
                    }
                },
                // Threshold annotation via plugin (inline)
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

        // Modal controls
        function openEdit(id, name, stock, price) {
            document.getElementById('inv_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_price').value = price;
            document.getElementById('editModal').classList.add('open');
        }
        function closeEdit() {
            document.getElementById('editModal').classList.remove('open');
        }
        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) closeEdit();
        });
    </script>

</body>

</html>