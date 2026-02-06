<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['Admin', 'FactoryOwner']);

$username = $_SESSION['username'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="../assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Admin</span>
        </div>

        <nav class="dash-nav">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="../Comp/DataAnalysis/insights.php">Analytics</a>
            <a href="../logout.php">Logout</a>
            <button class="avatar" style="background:#d93025;">A</button>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>System Administration</h1>
            <span class="pill" style="background:#e8f5e9; color:#0f5132;">Admin Access</span>
        </div>

        <section class="action-grid">

            <a href="../Comp/DataAnalysis/insights.php" class="action-card">
                <div class="icon-box blue">
                    <span style="font-size:24px;">📊</span>
                </div>
                <h3>Business Insights</h3>
                <p>View sales performance and predictive analysis.</p>
            </a>

            <!-- User Management -->
            <a href="manage_users.php" class="action-card">
                <div class="icon-box purple">
                    <span style="font-size:24px;">👥</span>
                </div>
                <h3>User Management</h3>
                <p>Manage users, roles, and permissions.</p>
            </a>

            <!-- Link to Manager View -->
            <a href="../manager/dashboard.php" class="action-card">
                <div class="icon-box orange">
                    <span style="font-size:24px;">🏪</span>
                </div>
                <h3>Manager View</h3>
                <p>Access Store Manager dashboard.</p>
            </a>

            <!-- Link to Shop View (for testing) -->
            <a href="../shop_dashboard.php" class="action-card">
                <div class="icon-box green">
                    <span style="font-size:24px;">🛒</span>
                </div>
                <h3>Shop View</h3>
                <p>Access Shop Owner dashboard.</p>
            </a>

        </section>
    </main>
</body>

</html>