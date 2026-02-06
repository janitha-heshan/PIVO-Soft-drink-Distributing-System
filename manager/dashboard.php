<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'Admin']);

$currentUser = $_SESSION['username'];

// Fetch Pending Orders
$pendStmt = $pdo->query("
    SELECT o.order_id, o.order_date, o.total_amount, s.shop_name 
    FROM orders o
    JOIN shops s ON o.shop_id = s.shop_id
    WHERE o.delivery_status = 'Pending'
    ORDER BY o.order_date ASC
");
$pendingOrders = $pendStmt->fetchAll();

// Check Low Stock (Threshold < 50 for demo)
$stockStmt = $pdo->query("
    SELECT p.product_name, s.volume_ml, i.quantity_in_stock
    FROM inventory i
    JOIN products p ON i.product_id = p.product_id
    JOIN sizes s ON p.size_id = s.size_id
    WHERE i.quantity_in_stock < 50
");
$lowStock = $stockStmt->fetchAll();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Manager Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script>
        function confirmOrder(orderId) {
            if (!confirm('Are you sure you want to CONFIRM Order #' + orderId + '? This will deduct inventory.')) return;

            fetch('order_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'confirm', order_id: orderId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Order Confirmed!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }

        function rejectOrder(orderId) {
            const reason = prompt("Enter reason for rejection:");
            if (!reason) return;

            fetch('order_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'reject', order_id: orderId, reason: reason })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Order Rejected!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
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
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="manage_products.php">Products</a>
            <a href="../logout.php">Logout</a>
            <button class="avatar" style="background:#5e35b1;">M</button>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Store Manager Dashboard</h1>
            <span class="pill" style="background:#f3e5f5; color:#7b1fa2;">Manager Access</span>
        </div>

        <div class="row" style="display:flex; gap: 24px; flex-wrap:wrap;">

            <!-- Pending Orders -->
            <div style="flex: 2; min-width: 400px;">
                <h2 style="margin-bottom:12px;">Pending Orders</h2>
                <div class="summary-card" style="padding:0;">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Shop</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($pendingOrders) > 0): ?>
                                <?php foreach ($pendingOrders as $ord): ?>
                                    <tr>
                                        <td>#
                                            <?php echo $ord['order_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($ord['shop_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo date('M d, H:i', strtotime($ord['order_date'])); ?>
                                        </td>
                                        <td>Rs.
                                            <?php echo number_format($ord['total_amount'], 2); ?>
                                        </td>
                                        <td>
                                            <button onclick="confirmOrder(<?php echo $ord['order_id']; ?>)" class="primary"
                                                style="padding: 6px 12px; font-size:12px; background:#0f5132;">Confirm</button>
                                            <button onclick="rejectOrder(<?php echo $ord['order_id']; ?>)" class="secondary"
                                                style="padding: 6px 12px; font-size:12px; border-color:#d93025; color:#d93025;">Reject</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:20px;">No pending orders.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div style="flex: 1; min-width: 300px;">
                <h2 style="margin-bottom:12px; color:#d93025;">Low Stock Alerts</h2>
                <div class="summary-card" style="border: 1px solid #ffebeb;">
                    <?php if (count($lowStock) > 0): ?>
                        <ul style="list-style:none;">
                            <?php foreach ($lowStock as $item): ?>
                                <li
                                    style="padding: 8px 0; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                                    <span>
                                        <?php echo $item['product_name'] . ' (' . $item['volume_ml'] . ')'; ?>
                                    </span>
                                    <span style="font-weight:bold; color:#d93025;">
                                        <?php echo $item['quantity_in_stock']; ?> units
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p style="color:#0f5132;">Inventory levels look good.</p>
                    <?php endif; ?>
                    <a href="inventory.php" class="link-btn" style="margin-top:10px; display:block;">Manage Inventory
                        &rarr;</a>
                </div>
            </div>

        </div>
    </main>
</body>

</html>