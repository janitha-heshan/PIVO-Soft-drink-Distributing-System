<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

requireRole(['ShopOwner', 'Admin']);

$userId = $_SESSION['user_id'];

// Fetch User's Shops
$shopStmt = $pdo->prepare("SELECT * FROM shops WHERE owner_id = ?");
$shopStmt->execute([$userId]);
$shops = $shopStmt->fetchAll();

// Fetch Products
// Fetch Products
$prodStmt = $pdo->query("SELECT p.*, s.volume_ml FROM products p JOIN sizes s ON p.size_id = s.size_id ORDER BY p.product_name, s.volume_ml");
$products = $prodStmt->fetchAll();

// Prepare Prices Array for JS
$jsPrices = [];
$productNames = [];
$volumes = [];

foreach ($products as $p) {
    // Structure: prices['Mango']['1L'] = 450
    $jsPrices[$p['product_name']][$p['volume_ml']] = $p['unit_price'];
    $jsPrices[$p['product_name']]['id_' . $p['volume_ml']] = $p['product_id']; // Store ID for backend

    if (!in_array($p['product_name'], $productNames))
        $productNames[] = $p['product_name'];
    if (!in_array($p['volume_ml'], $volumes))
        $volumes[] = $p['volume_ml'];
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Place Order</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <script>
        // Inject PHP data into JS
        const prices = <?php echo json_encode($jsPrices); ?>;
        const availableProducts = <?php echo json_encode($productNames); ?>;
        const availableSizes = <?php echo json_encode($volumes); ?>;
    </script>
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Holdings</span>
        </div>

        <nav class="dash-nav">
            <a href="shop_dashboard.php">Dashboard</a>
            <a href="order.php" class="active">Place Order</a>
            <button class="avatar">
                <?php echo substr($_SESSION['username'], 0, 1); ?>
            </button>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Place New Order</h1>
            <p class="text-muted">Select your shop and items.</p>
        </div>

        <?php if (count($shops) == 0): ?>
            <div class="alert alert-error" style="background:#ffebeb; padding:20px; text-align:center;">
                You need to register a shop before placing an order.
                <a href="register_shop.php" class="primary" style="margin-left:10px;">Register Shop</a>
            </div>
        <?php else: ?>

            <div class="order-layout">

                <section class="form-card order-form">
                    <h2>Order Details</h2>
                    <form id="addItemForm" class="form">
                        <label>
                            Select Shop
                            <select id="shopSelect" required>
                                <?php foreach ($shops as $shop): ?>
                                    <option value="<?php echo $shop['shop_id']; ?>">
                                        <?php echo htmlspecialchars($shop['shop_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <hr style="border:0; border-top:1px solid #eee; margin: 10px 0;">

                        <label>
                            Select Product
                            <select id="productSelect" required>
                                <option value="" disabled selected>-- Choose Drink --</option>
                                <?php foreach ($productNames as $name): ?>
                                    <option value="<?php echo $name; ?>">
                                        <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <div class="row">
                            <label class="half">
                                Size
                                <select id="sizeSelect" required>
                                    <!-- Populated by JS -->
                                    <option value="" disabled selected>-- Select Product First --</option>
                                </select>
                            </label>

                            <label class="half">
                                Quantity
                                <input type="number" id="quantityInput" min="1" value="1" required />
                            </label>
                        </div>

                        <div class="price-display">
                            Unit Price: <span id="unitPriceDisplay">LKR 0.00</span>
                        </div>

                        <button type="submit" class="primary full">Add to Order</button>
                    </form>
                </section>

                <section class="summary-card">
                    <h2>Order Summary</h2>
                    <div class="table-wrapper">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Total (LKR)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="orderTableBody">
                                <tr id="emptyRow">
                                    <td colspan="5" style="text-align:center; color:var(--muted);">No items added yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="grand-total-area">
                        <span>Grand Total:</span>
                        <span class="total-amount" id="grandTotal">LKR 0.00</span>
                    </div>

                    <button id="submitOrderBtn" class="primary full" style="margin-top: 20px;" disabled>Submit
                        Order</button>
                    <div id="orderMessage" style="text-align:center; margin-top:10px;"></div>
                </section>

            </div>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <small>©
            <?php echo date('Y'); ?> PIVO Holdings
        </small>
    </footer>

    <script src="assets/js/order_logic.js"></script>

</body>

</html>