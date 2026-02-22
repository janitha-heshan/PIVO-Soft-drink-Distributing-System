<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['FactoryOwner', 'Admin']);

$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'] ?? $username;

// Count low-stock items (< 50 units)
$lowStock = $pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity_in_stock < 50")->fetchColumn();
// Total products
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
// Total inventory units
$totalUnits = $pdo->query("SELECT COALESCE(SUM(quantity_in_stock),0) FROM inventory")->fetchColumn();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — Factory Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            border-left: 4px solid #2563eb;
        }

        .stat-card.warn {
            border-left-color: #d93025;
        }

        .stat-card .stat-val {
            font-size: 2rem;
            font-weight: 700;
            color: #111;
        }

        .stat-card .stat-label {
            font-size: 0.82rem;
            color: #666;
            margin-top: 4px;
        }

        .stat-card.warn .stat-val {
            color: #d93025;
        }
    </style>
    <script>
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
            <span class="brand-name">PIVO Factory</span>
        </div>
        <nav class="dash-nav">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="../manager/inventory.php">Inventory</a>
            <a href="../manager/manage_products.php">Products</a>
            <a href="../manager/returns.php">Returns</a>
            <a href="../manager/manage_territories.php">Territories</a>
            <a href="../Comp/DataAnalysis/insights.php">Analytics</a>

            <div class="user-menu" style="position:relative; margin-left:14px;">
                <div onclick="toggleDropdown()" style="cursor:pointer; display:flex; align-items:center;">
                    <button class="avatar" style="background:#16a34a; margin:0;">
                        <?= strtoupper(substr($username, 0, 1)) ?>
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
            <h1>Factory Dashboard</h1>
            <span class="pill" style="background:#dcfce7; color:#14532d;">Factory Owner</span>
        </div>

        <!-- Stats -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-val">
                    <?= $totalProducts ?>
                </div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-val">
                    <?= number_format($totalUnits) ?>
                </div>
                <div class="stat-label">Units In Stock</div>
            </div>
            <div class="stat-card warn">
                <div class="stat-val">
                    <?= $lowStock ?>
                </div>
                <div class="stat-label">Low-Stock Alerts (&lt; 50)</div>
            </div>
        </div>

        <!-- Action Cards -->
        <section class="action-grid">
            <a href="../manager/inventory.php" class="action-card">
                <div class="icon-box green">
                    <span style="font-size:24px;">📦</span>
                </div>
                <h3>Inventory</h3>
                <p>View and manage stock levels and pricing.</p>
            </a>

            <a href="../manager/manage_products.php" class="action-card">
                <div class="icon-box" style="background:#fff7ed; color:#ea580c;">
                    <span style="font-size:24px;">🏷️</span>
                </div>
                <h3>Products</h3>
                <p>Manage product catalog and images.</p>
            </a>

            <a href="../manager/returns.php" class="action-card">
                <div class="icon-box" style="background:#fef2f2; color:#dc2626;">
                    <span style="font-size:24px;">🔄</span>
                </div>
                <h3>Returns</h3>
                <p>Handle damaged or expired goods.</p>
            </a>

            <a href="../Comp/DataAnalysis/insights.php" class="action-card">
                <div class="icon-box blue">
                    <span style="font-size:24px;">📊</span>
                </div>
                <h3>Business Insights</h3>
                <p>View sales performance and predictive analysis.</p>
            </a>
        </section>
    </main>

</body>

</html>