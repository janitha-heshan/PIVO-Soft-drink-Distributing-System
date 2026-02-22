<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

requireRole(['ShopOwner', 'Admin']);

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch my shops
$stmt = $pdo->prepare("SELECT * FROM shops WHERE owner_id = ?");
$stmt->execute([$userId]);
$shops = $stmt->fetchAll();

// Fetch recent active orders
$orderStmt = $pdo->prepare("
    SELECT o.order_id, o.order_date, o.delivery_status, o.total_amount, s.shop_name 
    FROM orders o
    JOIN shops s ON o.shop_id = s.shop_id
    WHERE s.owner_id = ?
    ORDER BY o.order_date DESC LIMIT 5
");
$orderStmt->execute([$userId]);
$orders = $orderStmt->fetchAll();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Holdings</span>
        </div>

        <nav class="dash-nav">
            <a href="shop_dashboard.php" class="active">Dashboard</a>
            <a href="order.php">Place Order</a>
            <a href="order_history.php">History</a>
            <a href="logout.php">Logout</a>
            <button class="avatar"><?= strtoupper(substr($username, 0, 1)) ?></button>
        </nav>
    </header>

    <main class="container">

        <div class="dash-header">
            <div>
                <h1>Welcome,
                    <?php echo htmlspecialchars($username); ?>!
                </h1>
                <p class="text-muted">Manage your shops and orders.</p>
            </div>
            <span class="pill" style="background:#e3f2fd; color:#0d47a1;">Shop Owner</span>
        </div>

        <!-- Quick Actions -->
        <section class="action-grid">

            <a href="order.php" class="action-card">
                <div class="icon-box blue">
                    <!-- Icon placeholder -->
                    <span style="font-size:24px;">+</span>
                </div>
                <h3>Place Order</h3>
                <p>Restock your inventory.</p>
            </a>

            <a href="register_shop.php" class="action-card">
                <div class="icon-box green">
                    <span style="font-size:24px;">🏢</span>
                </div>
                <h3>Register Shop</h3>
                <p>Add a new outlet to your account.</p>
            </a>

        </section>

        <div class="row" style="display:flex; gap: 20px; flex-wrap: wrap;">
            <!-- My Shops -->
            <div style="flex: 1; min-width: 300px;">
                <h2 style="margin-bottom: 12px; font-size: 18px;">My Shops</h2>
                <?php if (count($shops) > 0): ?>
                    <div class="cards" style="display:flex; flex-direction:column; gap: 12px;">
                        <?php foreach ($shops as $shop): ?>
                            <div class="product-card" style="display:flex; align-items:center; gap: 12px; padding: 12px;">
                                <div
                                    style="width:40px; height:40px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                    🏪</div>
                                <div>
                                    <h3 style="font-size:16px; margin:0;">
                                        <?php echo htmlspecialchars($shop['shop_name']); ?>
                                    </h3>
                                    <p style="font-size:12px; color:#666;">
                                        <?php echo htmlspecialchars($shop['address']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="form-card" style="text-align:center; padding: 20px;">
                        <p>No shops registered yet.</p>
                        <a href="register_shop.php" class="primary" style="display:inline-block; margin-top:10px;">Register
                            One</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Orders -->
            <div style="flex: 2; min-width: 300px;">
                <h2 style="margin-bottom: 12px; font-size: 18px;">Recent Active Orders</h2>
                <div class="bg-white rounded-xl border border-[#cfdfe7] shadow-sm overflow-hidden"
                    style="background:white; border-radius:12px; padding:12px;">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Shop</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders) > 0): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#
                                            <?php echo $order['order_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($order['shop_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                        </td>
                                        <td>
                                            <span class="pill" style="font-size: 11px; <?php
                                            echo $order['delivery_status'] == 'Delivered' ? 'background:#e6fffa; color:#00bfa5;' :
                                                ($order['delivery_status'] == 'Pending' ? 'background:#fff3cd; color:#856404;' : 'background:#e3f2fd; color:#1565c0;');
                                            ?>">
                                                <?php echo $order['delivery_status']; ?>
                                            </span>
                                        </td>
                                        <td>Rs.
                                            <?php echo number_format($order['total_amount'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 20px;">No recent orders.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <footer class="site-footer">
        <small>©
            <?php echo date('Y'); ?> PIVO Holdings
        </small>
    </footer>

</body>

</html>