<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

requireRole(['ShopOwner', 'Admin']);

$userId = $_SESSION['user_id'];

// Fetch all orders
$stmt = $pdo->prepare("
    SELECT o.order_id, o.order_date, o.delivery_status, o.total_amount, s.shop_name 
    FROM orders o
    JOIN shops s ON o.shop_id = s.shop_id
    WHERE s.owner_id = ?
    ORDER BY o.order_date DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Order History</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Holdings</span>
        </div>

        <nav class="dash-nav">
            <a href="shop_dashboard.php">Dashboard</a>
            <a href="order.php">Place Order</a>
            <a href="order_history.php" class="active">History</a>
            <button class="avatar">
                <?php echo substr($_SESSION['username'], 0, 1); ?>
            </button>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Order History</h1>
            <a href="shop_dashboard.php" class="link-btn">Back to Dashboard</a>
        </div>

        <section class="summary-card">
            <div class="table-wrapper">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Shop</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Total (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td style="font-weight:bold;">#
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
                                        switch ($order['delivery_status']) {
                                            case 'Pending':
                                                echo 'background:#fff3cd; color:#856404;';
                                                break;
                                            case 'Delivered':
                                                echo 'background:#e6fffa; color:#00bfa5;';
                                                break;
                                            case 'Cancelled':
                                                echo 'background:#ffebeb; color:#d93025;';
                                                break;
                                            default:
                                                echo 'background:#e3f2fd; color:#1565c0;';
                                        }
                                        ?>">
                                            <?php echo $order['delivery_status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="alert('TODO: Show items for Order #<?php echo $order['order_id']; ?>')"
                                            class="link-btn" style="font-size:12px;">View Items</button>
                                    </td>
                                    <td style="font-weight:bold;">Rs.
                                        <?php echo number_format($order['total_amount'], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:20px;">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>
</body>

</html>