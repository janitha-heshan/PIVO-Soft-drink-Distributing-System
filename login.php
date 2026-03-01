<?php
require_once 'config/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id, username, password_hash, role, full_name, pw_reset_pending FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Block login if password reset is pending
            if ($user['pw_reset_pending']) {
                $error = "Your account is pending a password reset. We will get back to you once your password has been reset by an administrator.";
            } else {
                // Login Success
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                // Redirect based on Role
                switch ($user['role']) {
                    case 'ShopOwner':
                        $shopCheck = $pdo->prepare("SELECT COUNT(*) FROM shops WHERE owner_id = ?");
                        $shopCheck->execute([$user['user_id']]);
                        if ($shopCheck->fetchColumn() == 0) {
                            header("Location: register_shop.php");
                        } else {
                            header("Location: shop_dashboard.php");
                        }
                        break;
                    case 'StoreManager':
                        header("Location: manager/dashboard.php");
                        break;
                    case 'SalesSupervisor':
                    case 'SalesRep':
                        header("Location: logistics/dashboard.php");
                        break;
                    case 'FactoryOwner':
                        header("Location: factory/dashboard.php");
                        break;
                    case 'Admin':
                        header("Location: admin/dashboard.php");
                        break;
                    default:
                        header("Location: dashboard.php");
                }
                exit;
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — Login</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .error-msg {
            color: #d93025;
            background: #ffebeb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Holdings</span>
        </div>
    </header>

    <main class="container">
        <section class="form-card">
            <h1>Welcome Back</h1>

            <?php if ($error): ?>
                <div class="error-msg">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form class="form" id="loginForm" method="POST" action="login.php">
                <label>
                    Username
                    <input type="text" id="username" name="username" placeholder="Enter your username" required />
                </label>

                <label>
                    Password
                    <input type="password" id="password" name="password" placeholder="Enter your password" required />
                </label>

                <div class="text-right">
                    <a href="forgot_password.php" class="link-sm">Forgot password?</a>
                </div>

                <button type="submit" class="primary full">Log In</button>
            </form>

            <p class="muted">
                Don't have an account? <a href="signup.php" class="accent-link">Sign up</a>
            </p>
        </section>
    </main>

    <footer class="site-footer">
        <small>©
            <?php echo date('Y'); ?> PIVO Holdings
        </small>
    </footer>

    <!-- <script src="assets/js/login.js"></script> -->
</body>

</html>