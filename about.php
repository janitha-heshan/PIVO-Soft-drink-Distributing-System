<?php
session_start();
require_once 'includes/auth.php'; // For getDashboardPath
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — About Us</title>
    <link rel="stylesheet" href="assets/css/style.css" />
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
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="about.php" class="active">About Us</a>

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

    <main class="container">
        <div class="dash-header" style="justify-content: center; text-align: center; margin-bottom: 60px;">
            <div style="max-width: 800px;">
                <h1 style="font-size: 48px; margin-bottom: 24px;">Revolutionizing Refreshment.</h1>
                <p class="text-muted" style="font-size: 20px; line-height: 1.6;">PIVO Holdings is Sri Lanka's premier
                    soft drink distributor, combining traditional flavors with modern logistics.</p>
            </div>
        </div>

        <section class="row" style="display:flex; gap: 60px; align-items:center; margin-bottom: 100px;">
            <div style="flex:1;">
                <!-- Placeholder for About Image with shadow -->
                <div
                    style="width:100%; height: 300px; background:linear-gradient(45deg, #eee, #f5f5f5); border-radius:24px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); display:flex; align-items:center; justify-content:center; color:#999; font-weight:600;">
                    Factory & Logistics Center
                </div>
            </div>
            <div style="flex:1;">
                <h2 style="margin-bottom: 24px; font-size: 32px;">Our Mission</h2>
                <p style="margin-bottom: 30px; line-height: 1.8; color: #555; font-size: 16px;">
                    We are dedicated to bringing the freshest, most authentic fruit nectars to every corner of the
                    island.
                    Using our state-of-the-art <strong>Anti-Gravity Distribution System</strong>, we ensure that our
                    shop partners
                    never run out of stock and that our customers always get the premium quality they deserve.
                </p>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 16px; display:flex; align-items:center; gap: 16px; font-weight: 500;">
                        <span style="background:#eef4fb; color:#1170d6; padding:8px; border-radius:50%;">✓</span>
                        GPS-Tracked Delivery
                    </li>
                    <li style="margin-bottom: 16px; display:flex; align-items:center; gap: 16px; font-weight: 500;">
                        <span style="background:#eef4fb; color:#1170d6; padding:8px; border-radius:50%;">✓</span>
                        AI-Powered Insights
                    </li>
                    <li style="margin-bottom: 16px; display:flex; align-items:center; gap: 16px; font-weight: 500;">
                        <span style="background:#eef4fb; color:#1170d6; padding:8px; border-radius:50%;">✓</span>
                        Authentic Flavors
                    </li>
                </ul>
            </div>
        </section>

        <section
            style="text-align:center; padding: 80px 20px; background: #fff; border-radius: 24px; box-shadow: 0 10px 40px rgba(15,23,36,0.02);">
            <h2 style="margin-bottom: 40px; font-size: 32px;">Meet the Leadership</h2>
            <div class="cards" style="justify-content:center; display:flex; gap: 40px; flex-wrap:wrap;">
                <div style="width: 240px; text-align: center;">
                    <div
                        style="width:120px; height:120px; background:#f5f8fa; border-radius:50%; margin: 0 auto 20px auto; overflow:hidden; border: 4px solid white; box-shadow: 0 8px 16px rgba(0,0,0,0.05);">
                        <div
                            style="display:flex; align-items:center; justify-content:center; height:100%; font-size:32px;">
                            👨‍💼</div>
                    </div>
                    <h3 style="font-size:18px; margin-bottom: 4px;">CEO</h3>
                    <p
                        style="font-size:14px; color:#1170d6; font-weight:600; text-transform: uppercase; letter-spacing: 0.5px;">
                        Chief Executive Officer</p>
                </div>

                <div style="width: 240px; text-align: center;">
                    <div
                        style="width:120px; height:120px; background:#f5f8fa; border-radius:50%; margin: 0 auto 20px auto; overflow:hidden; border: 4px solid white; box-shadow: 0 8px 16px rgba(0,0,0,0.05);">
                        <div
                            style="display:flex; align-items:center; justify-content:center; height:100%; font-size:32px;">
                            💻</div>
                    </div>
                    <h3 style="font-size:18px; margin-bottom: 4px;">Admin</h3>
                    <p
                        style="font-size:14px; color:#1170d6; font-weight:600; text-transform: uppercase; letter-spacing: 0.5px;">
                        Head of Technology</p>
                </div>
            </div>
        </section>

    </main>

    <footer class="site-footer" style="padding: 40px 0; margin-top: 60px;">
        <small>© <?php echo date('Y'); ?> PIVO Holdings. All Rights Reserved.</small>
    </footer>

</body>

</html>