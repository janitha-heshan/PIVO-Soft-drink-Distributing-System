<?php
session_start();
require_once 'includes/auth.php'; // For getDashboardPath
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — Authentic Refreshment</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        /* Hero Section Override */
        .hero-section {
            background: linear-gradient(135deg, #eef4fb 0%, #fefefe 100%);
            padding: 80px 20px;
            text-align: center;
            border-radius: 0 0 40px 40px;
            margin-bottom: 60px;
        }

        .hero-title {
            font-size: 56px;
            line-height: 1.1;
            margin-bottom: 20px;
            background: linear-gradient(to right, #1170d6, #0b66d1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            font-size: 20px;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto 30px auto;
        }

        .btn-sky {
            background: #1170d6;
            color: white;
            padding: 14px 32px;
            font-size: 16px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: transform 0.2s;
            box-shadow: 0 10px 20px rgba(17, 112, 214, 0.2);
        }

        .btn-sky:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px rgba(17, 112, 214, 0.3);
        }

        .feature {
            text-align: left;
            padding: 30px;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: transform 0.2s;
        }

        .feature:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 32px;
            margin-bottom: 16px;
            display: inline-block;
            padding: 12px;
            background: #eef4fb;
            border-radius: 12px;
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
        // Close dropdown if clicked outside
        window.onclick = function (event) {
            if (!event.target.closest('.user-menu')) {
                document.getElementById("userDropdown").style.display = "none";
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
            <a href="index.php" class="active">Home</a>
            <a href="products.php">Products</a>
            <a href="about.php">About Us</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo getDashboardPath($_SESSION['role']); ?>">Dashboard</a>
                <!-- User Dropdown -->
                <div class="user-menu" style="position:relative;">
                    <div onclick="toggleDropdown()" style="cursor:pointer; display:flex; align-items:center; gap:8px;">
                        <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                    </div>
                    <div id="userDropdown" class="dropdown-content">
                        <div style="padding:12px 16px; border-bottom:1px solid #eee;">
                            <strong
                                style="display:block; font-size:14px;"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></strong>
                            <span style="font-size:11px; color:#888;"><?php echo $_SESSION['role']; ?></span>
                        </div>
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php" style="color:#d93025;">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="primary" style="padding: 8px 16px; border-radius:20px;">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="hero-section">
        <h1 class="hero-title">Taste the Future<br>of Refreshment.</h1>
        <p class="hero-sub">Premium fruit nectars delivered with absolute precision. Experience the PIVO difference
            today.</p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo getDashboardPath($_SESSION['role']); ?>" class="btn-sky">Go to Dashboard</a>
        <?php else: ?>
            <a href="signup.php" class="btn-sky">Join as Partner</a>
        <?php endif; ?>

        <div style="margin-top: 50px;">
            <img src="assets/images/dashboard.jpg"
                style="width:100%; max-width:800px; border-radius:20px; box-shadow: 0 20px 60px rgba(0,0,0,0.1);"
                alt="PIVO Dashboard">
        </div>
    </div>

    <main class="container">

        <!-- Features -->
        <section class="row"
            style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 80px;">
            <div class="feature">
                <span class="feature-icon">🏆</span>
                <h3>Serving Since 2016</h3>
                <p class="text-muted">For over 10 years, PIVO has been the trusted name in refreshment. We celebrate a
                    decade of excellence in every bottle.</p>
            </div>
            <div class="feature">
                <span class="feature-icon">🛡️</span>
                <h3>Verified Security</h3>
                <p class="text-muted">From role-based access to GPS-tracked deliveries, every step of the supply chain
                    is secure.</p>
            </div>
            <div class="feature">
                <span class="feature-icon">🍊</span>
                <h3>Authentic Flavors</h3>
                <p class="text-muted">Sourced directly from local farmers, our nectars retain the true essence of the
                    fruit.</p>
            </div>
        </section>

        <!-- Products Preview -->
        <div class="dash-header">
            <h2>Best Sellers</h2>
            <a href="products.php" class="accent-link">See All Products &rarr;</a>
        </div>

        <section class="product-overview">
            <div class="cards">
                <div class="product-card">
                    <div class="img-placeholder" style="background:#fff3e0; color:#ef6c00;">Mango Image</div>
                    <h3>Mango Nectar</h3>
                    <p class="desc">Rich, pulpy sweetness from Karthakolomban mangoes.</p>
                    <div class="tags"><span class="pill">Best Seller</span></div>
                </div>
                <div class="product-card">
                    <div class="img-placeholder" style="background:#e8f5e9; color:#2e7d32;">Apple Image</div>
                    <h3>Apple Soda</h3>
                    <p class="desc">Crisp, sparkling refreshment with real apple juice..</p>
                </div>
                <div class="product-card">
                    <div class="img-placeholder" style="background:#f3e5f5; color:#7b1fa2;">Mix Fruit Image</div>
                    <h3>Mix Fruit Blast</h3>
                    <p class="desc">A tropical explosion of 5 different fruits.</p>
                </div>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        <div style="margin-bottom: 20px;">
            <img src="assets/images/logo-placeholder.png" alt="PIVO" style="width: 40px; opacity:0.5;" />
        </div>
        <div style="display:flex; justify-content:center; gap:20px; margin-bottom:20px;">
            <a href="about.php" style="color:#666; text-decoration:none;">About Us</a>
            <a href="#" style="color:#666; text-decoration:none;">Privacy Policy</a>
            <a href="#" style="color:#666; text-decoration:none;">Contact</a>
        </div>
        <small>© <?php echo date('Y'); ?> PIVO Holdings.</small>
    </footer>

</body>

</html>