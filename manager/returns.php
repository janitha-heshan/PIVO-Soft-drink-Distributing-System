<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'ShopOwner', 'Admin', 'FactoryOwner']);

$action = 'list';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process Return Logic
    $orderId = $_POST['order_id'];
    $prodId = $_POST['product_id'];
    $qty = intval($_POST['quantity']);
    $reason = $_POST['reason'];

    if ($qty > 0) {
        $stmt = $pdo->prepare("INSERT INTO product_returns (order_id, product_id, quantity, reason, return_date, status) VALUES (?, ?, ?, ?, NOW(), 'Approved')");
        $stmt->execute([$orderId, $prodId, $qty, $reason]);

        // Optionally put back into stock? or discard?
        // For now, let's assume valid returns go back to inventory
        $updInv = $pdo->prepare("UPDATE inventory SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
        $updInv->execute([$qty, $prodId]);

        header("Location: returns.php?success=Return Processed");
        exit;
    }
}

// Fetch Returns
$returns = $pdo->query("
    SELECT r.*, p.product_name, o.shop_id, s.shop_name 
    FROM product_returns r
    JOIN products p ON r.product_id = p.product_id
    JOIN orders o ON r.order_id = o.order_id
    JOIN shops s ON o.shop_id = s.shop_id
    ORDER BY r.return_date DESC
")->fetchAll();

// Fetch products for dropdown
$products = $pdo->query("SELECT product_id, product_name, volume_ml FROM products ORDER BY product_name ASC")->fetchAll();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Product Returns</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
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
    </style>
    <script>
        function openLogReturn() {
            document.getElementById('returnModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('returnModal').style.display = 'none';
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
            <a href="inventory.php">Inventory</a>
            <a href="manage_products.php">Products</a>
            <a href="returns.php" class="active">Returns</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Product Returns</h1>
            <button onclick="openLogReturn()" class="secondary">Log New Return</button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div style="background:#d1e7dd; color:#0f5132; padding:12px; border-radius:8px; margin-bottom:20px;">
                Return Logged Successfully. Inventory Updated.
            </div>
        <?php endif; ?>

        <section class="summary-card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order Ref</th>
                        <th>Shop</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($returns as $r): ?>
                        <tr>
                            <td>
                                <?php echo date('M d', strtotime($r['return_date'])); ?>
                            </td>
                            <td>#
                                <?php echo $r['order_id']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['shop_name']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['product_name']); ?>
                            </td>
                            <td style="color:#d93025; font-weight:bold;">-
                                <?php echo $r['quantity']; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['reason']); ?>
                            </td>
                            <td><span class="pill">
                                    <?php echo $r['status']; ?>
                                </span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Return Modal -->
        <div id="returnModal" class="modal">
            <div class="modal-content">
                <h2>Log Return</h2>
                <form method="POST" class="form">
                    <label>Order ID <input type="number" name="order_id" required placeholder="e.g. 101" /></label>
                    <label>Product
                        <select name="product_id" required>
                            <option value="" disabled selected>-- Select Product --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p['product_id']; ?>">
                                    <?php echo htmlspecialchars($p['product_name'] . ' - ' . $p['volume_ml']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Quantity <input type="number" name="quantity" required /></label>
                    <label>Reason <input type="text" name="reason" placeholder="e.g. Damaged, Expired"
                            required /></label>

                    <div style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" onclick="closeModal()" class="secondary full">Cancel</button>
                        <button type="submit" class="primary full">Process Return</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>

</html>