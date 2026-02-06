<?php
require_once 'config/db.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            // Create User
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'ShopOwner'; // Default role for public signup

            try {
                $sql = "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $email, $passwordHash, $role]);

                $success = "Account created successfully! You can now log in.";
                // Optional: Redirect to login after a delay or immediately
                // header("Location: login.php");
            } catch (PDOException $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — Sign Up</title>
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

        .success-msg {
            color: #0f5132;
            background: #d1e7dd;
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
            <h1>Create Account</h1>

            <?php if ($error): ?>
                <div class="error-msg">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-msg">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form class="form" id="signupForm" method="POST" action="signup.php">
                <label>
                    Username
                    <input type="text" id="username" name="username" placeholder="Choose a username" required />
                </label>

                <label>
                    Email Address
                    <input type="email" id="email" name="email" placeholder="Enter your email" required />
                </label>

                <label>
                    Password
                    <input type="password" id="password" name="password" placeholder="Create a password" required />
                </label>

                <label>
                    Confirm Password
                    <input type="password" id="confirmPassword" name="confirmPassword"
                        placeholder="Confirm your password" required />
                </label>

                <button type="submit" class="primary full">Sign Up</button>
            </form>

            <p class="muted">
                Already have an account? <a href="login.php" class="accent-link">Log In</a>
            </p>
        </section>
    </main>

    <footer class="site-footer">
        <small>©
            <?php echo date('Y'); ?> PIVO Holdings
        </small>
    </footer>

    <!-- <script src="assets/js/signup.js"></script> -->
</body>

</html>