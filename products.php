<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth.php';

// Fetch Products with Sizes
$stmt = $pdo->query("
    SELECT p.*, s.volume_ml 
    FROM products p 
    JOIN sizes s ON p.size_id = s.size_id 
    ORDER BY p.product_name, s.volume_ml
");
$products = $stmt->fetchAll(PDO::FETCH_GROUP); // Group by ID (actually name would be better if normalized differently, but here each row is unique variant)
// Re-organize to grouping by Name
$groupedParams = [];
$rawProds = $stmt->fetchAll(); // Reset fetch? No, need to re-query or iterate.

$stmt->execute();
$allProds = $stmt->fetchAll();

$displayProds = [];
foreach ($allProds as $row) {
    if (!isset($displayProds[$row['product_name']])) {
        $displayProds[$row['product_name']] = [
            'image' => $row['image_path'],
            'desc' => $row['description'],
            'variants' => []
        ];
    }
    $displayProds[$row['product_name']]['variants'][] = [
        'size' => $row['volume_ml'],
        'price' => $row['unit_price']
    ];
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Our Products</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <script>
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("show");
        }
        window.onclick = function (event) {
            if (!event.target.closest('.user-menu')) {
                var d = document.getElementById("userDropdown");
                if (d) d.classList.remove('show');
            }
        }
    </script>
</head>

<body>

    <header class="topbar">
        <a href="index.php" class="brand" style="text-decoration:none;">
            <img src="assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Holdings</span>
        </a>

        <nav class="dash-nav">
            <a href="index.php">Home</a>
            <a href="products.php" class="active">Products</a>
            <a href="about.php">About Us</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo getDashboardPath($_SESSION['role']); ?>">Dashboard</a>
                <div class="user-menu" style="position:relative;">
                    <div onclick="toggleDropdown()" style="cursor:pointer; display:flex; align-items:center; gap:8px;">
                        <div class="avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                    </div>
                    <div id="userDropdown" class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php" style="color:#d93025;">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="primary" style="padding: 8px 16px; border-radius:20px;">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header" style="text-align:center; display:block;">
            <h1>Our Premium Collection</h1>
            <p class="text-muted">Explore our range of authentic fruit nectars and refreshing sodas.</p>
        </div>

        <section class="product-overview">
            <div class="cards">
                <?php foreach ($displayProds as $name => $data): ?>
                    <div class="product-card">
                        <!-- Placeholder Logic for Colors -->
                        <?php
                        $bg = '#f5f5f5';
                        $color = '#666';
                        if (strpos($name, 'Mango') !== false) {
                            $bg = '#fff3e0';
                            $color = '#ef6c00';
                        } elseif (strpos($name, 'Apple') !== false) {
                            $bg = '#e8f5e9';
                            $color = '#2e7d32';
                        } elseif (strpos($name, 'Fruit') !== false) {
                            $bg = '#f3e5f5';
                            $color = '#7b1fa2';
                        } elseif (strpos($name, 'Aloe') !== false) {
                            $bg = '#e0f2f1';
                            $color = '#00695c';
                        }
                        ?>
                        <div class="img-placeholder" style="background:<?php echo $bg; ?>; color:<?php echo $color; ?>;">
                            <?php echo htmlspecialchars($name); ?>
                        </div>

                        <h3>
                            <?php echo htmlspecialchars($name); ?>
                        </h3>
                        <p class="desc">
                            <?php echo htmlspecialchars($data['desc'] ?? 'Authentic flavor.'); ?>
                        </p>

                        <div style="margin-top:16px; border-top:1px solid #eee; padding-top:10px;">
                            <?php foreach ($data['variants'] as $v): ?>
                                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
                                    <span style="font-weight:600; color:#555;">
                                        <?php echo htmlspecialchars($v['size']); ?>
                                    </span>
                                    <span style="color:var(--primary);">Rs.
                                        <?php echo number_format($v['price'], 2); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'ShopOwner'): ?>
                            <a href="order.php" class="primary full"
                                style="display:block; text-align:center; margin-top:12px;">Order Now</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        <small>©
            <?php echo date('Y'); ?> PIVO Holdings. All Rights Reserved.
        </small>
    </footer>

</body>

</html>