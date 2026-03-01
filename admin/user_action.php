<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['Admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add_user') {
            // Basic Add User (if needed, or reliance on Signup)
            // Implementation:
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];

            // Validation
            if (empty($username) || empty($password))
                throw new Exception("Username and Password are required.");

            // Check existing
            $check = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $check->execute([$username, $email]);
            if ($check->rowCount() > 0)
                throw new Exception("Username or Email already exists.");

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            $ins->execute([$username, $email, $hash, $role]);

            header("Location: manage_users.php?success=User Created");
            exit;
        }

        if ($action === 'update_user') {
            $userId = intval($_POST['user_id']);
            $role = $_POST['role'];
            $password = trim($_POST['password']); // Optional

            $pdo->beginTransaction();

            // Update Role
            $upd = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
            $upd->execute([$role, $userId]);

            // Update Password if provided
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $updPwd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $updPwd->execute([$hash, $userId]);
            }

            $pdo->commit();
            header("Location: manage_users.php?success=User Updated");
            exit;
        }

    } catch (Exception $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        header("Location: manage_users.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

// GET Actions (Delete)
if (isset($_GET['action']) && $_GET['action'] === 'delete_user' && isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Prevent deleting self
    if ($userId == $_SESSION['user_id']) {
        header("Location: manage_users.php?error=Cannot delete yourself.");
        exit;
    }

    try {
        $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$userId]);
        header("Location: manage_users.php?success=User Deleted");
        exit;
    } catch (Exception $e) {
        header("Location: manage_users.php?error=" . urlencode("Cannot delete user (likely has linked data)."));
        exit;
    }
}
?>