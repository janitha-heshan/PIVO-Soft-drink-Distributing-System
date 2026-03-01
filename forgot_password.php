<?php
require_once 'config/db.php';
session_start();

// Already logged in? Redirect home
if (isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if (empty($username)) {
        $error = "Please enter your username.";
    } else {
        // Check user exists
        $stmt = $pdo->prepare("SELECT user_id, username, pw_reset_pending FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = "No account found with that username.";
        } else {
            // Check for existing open ticket
            $chk = $pdo->prepare("SELECT ticket_id FROM pw_reset_tickets WHERE user_id = ? AND status = 'Open'");
            $chk->execute([$user['user_id']]);

            if (!$chk->fetch()) {
                // Create ticket
                $ins = $pdo->prepare("INSERT INTO pw_reset_tickets (user_id) VALUES (?)");
                $ins->execute([$user['user_id']]);
            }

            // Mark pending
            $pdo->prepare("UPDATE users SET pw_reset_pending = 1 WHERE user_id = ?")
                ->execute([$user['user_id']]);

            $submitted = true;
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — Forgot Password</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 20px 22px;
            text-align: center;
            margin-top: 8px;
        }

        .info-box .big-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .info-box h2 {
            font-size: 1.1rem;
            color: #1e3a8a;
            margin-bottom: 8px;
        }

        .info-box p {
            font-size: 0.875rem;
            color: #3730a3;
            line-height: 1.6;
        }

        .error-msg {
            color: #d93025;
            background: #ffebeb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            text-align: center;
            font-size: 14px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.875rem;
            color: #2563eb;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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
            <h1>Forgot Password</h1>

            <?php if ($submitted): ?>
                <!-- Success State -->
                <div class="info-box">
                    <div class="big-icon">📋</div>
                    <h2>Request Submitted</h2>
                    <p>
                        Your password reset request has been received.<br>
                        <strong>We will get back to you once your password has been reset</strong> by an
                        administrator.<br><br>
                        Please check back and try logging in again later.
                    </p>
                </div>
                <a class="back-link" href="login.php">← Back to Login</a>

            <?php else: ?>
                <!-- Form State -->
                <?php if ($error): ?>
                    <div class="error-msg">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <p style="font-size:0.875rem; color:#555; margin-bottom:18px;">
                    Enter your username below and an administrator will reset your password.
                </p>

                <form class="form" method="POST" action="forgot_password.php">
                    <label>
                        Username
                        <input type="text" name="username" placeholder="Enter your username"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus />
                    </label>
                    <button type="submit" class="primary full">Submit Request</button>
                </form>

                <a class="back-link" href="login.php">← Back to Login</a>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <small>©
            <?= date('Y') ?> PIVO Holdings
        </small>
    </footer>

</body>

</html>