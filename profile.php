<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch User
$stmt = $pdo->prepare("SELECT username, full_name, email, contact_number FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact_number']);
    $newPass = $_POST['new_password'];

    // Basic Validation
    if (empty($email)) {
        $error = "Email is required.";
    } else {
        try {
            // Update Base Info
            $upd = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, contact_number = ? WHERE user_id = ?");
            $upd->execute([$fullName, $email, $contact, $user_id]);

            // Update Password if provided
            if (!empty($newPass)) {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $passUpd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $passUpd->execute([$hash, $user_id]);
            }

            // Refresh Session Name
            $_SESSION['full_name'] = $fullName;
            $success = "Profile updated successfully!";

            // Refresh Data
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

        } catch (PDOException $e) {
            $error = "Update failed: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — My Profile</title>
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
            <a href="index.php">Home</a>
            <a href="<?php echo getDashboardPath($_SESSION['role']); ?>">Dashboard</a>

            <!-- User Dropdown -->
            <div class="user-menu" style="position:relative;">
                <div onclick="toggleDropdown()" style="cursor:pointer; display:flex; align-items:center; gap:8px;">
                    <span style="font-weight:600;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                </div>
                <div id="userDropdown" class="dropdown-content">
                    <a href="profile.php">My Profile</a>
                    <a href="logout.php" style="color:#d93025;">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <section class="form-card" style="max-width:500px; margin-top:40px;">
            <h1>Edit Profile</h1>

            <?php if ($success): ?>
                <div
                    style="background:#d1e7dd; color:#0f5132; padding:10px; border-radius:6px; margin-bottom:15px; text-align:center;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div
                    style="background:#ffebeb; color:#d93025; padding:10px; border-radius:6px; margin-bottom:15px; text-align:center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form class="form" method="POST">
                <label>Username
                    <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled
                        style="background:#eee; cursor:not-allowed;" />
                </label>

                <label>Full Name
                    <input type="text" name="full_name"
                        value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" placeholder="Your Name" />
                </label>

                <label>Email
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                        required />
                </label>

                <label>Contact Number
                    <input type="text" name="contact_number"
                        value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>" />
                </label>

                <div style="border-top:1px solid #eee; margin: 20px 0;"></div>

                <label>New Password <span class="muted">(Leave blank to keep current)</span>
                    <input type="password" name="new_password" placeholder="New Password" />
                </label>

                <button type="submit" class="primary full">Update Profile</button>
            </form>
        </section>
    </main>
</body>

</html>