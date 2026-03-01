<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['Admin']);

// Fetch Users
$stmt = $pdo->query("SELECT user_id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Roles for Dropdown
$roles = ['Admin', 'StoreManager', 'ShopOwner', 'SalesRep', 'SalesSupervisor', 'ITSupport', 'FactoryOwner'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO Admin — Manage Users</title>
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
            z-index: 100;
        }

        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            width: 400px;
        }
    </style>
    <script>
        function openEditModal(id, username, role) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_username_val').innerText = username;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_password').value = ''; // Reset
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        function confirmDelete(id, username) {
            if (confirm('Are you sure you want to PERMANENTLY delete user ' + username + '?')) {
                window.location.href = 'user_action.php?action=delete_user&id=' + id;
            }
        }
        function toggleAddModal() {
            const el = document.getElementById('addModal');
            el.style.display = (el.style.display === 'flex') ? 'none' : 'flex';
        }
    </script>
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="../assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Admin</span>
        </div>
        <nav class="dash-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_users.php" class="active">Users</a>
            <a href="pw_reset_tickets.php">Reset Tickets</a>
            <a href="../Comp/DataAnalysis/insights.php">Analytics</a>
            <a href="../logout.php">Logout</a>
            <button class="avatar" style="background:#d93025;">A</button>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>User Management</h1>
            <button onclick="toggleAddModal()" class="primary">Add User +</button>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="background:#ffebeb; color:#d93025; padding:12px; border-radius:8px; margin-bottom:20px;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div style="background:#d1e7dd; color:#0f5132; padding:12px; border-radius:8px; margin-bottom:20px;">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <section class="summary-card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#
                                <?php echo $u['user_id']; ?>
                            </td>
                            <td><strong>
                                    <?php echo htmlspecialchars($u['username']); ?>
                                </strong></td>
                            <td><span class="pill">
                                    <?php echo $u['role']; ?>
                                </span></td>
                            <td>
                                <?php echo htmlspecialchars($u['email']); ?>
                            </td>
                            <td>
                                <button
                                    onclick="openEditModal(<?php echo $u['user_id']; ?>, '<?php echo $u['username']; ?>', '<?php echo $u['role']; ?>')"
                                    class="link-btn">Edit</button>
                                <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                    <button
                                        onclick="confirmDelete(<?php echo $u['user_id']; ?>, '<?php echo $u['username']; ?>')"
                                        class="link-btn" style="color:#d93025;">Delete</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <h2>Edit User: <span id="edit_username_val"></span></h2>
                <form action="user_action.php" method="POST" class="form">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <label>Role
                        <select name="role" id="edit_role" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r; ?>">
                                    <?php echo $r; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>New Password (Optional)
                        <input type="text" name="password" id="edit_password"
                            placeholder="Leave blank to keep current" />
                    </label>

                    <div style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" onclick="closeEditModal()" class="secondary full">Cancel</button>
                        <button type="submit" class="primary full">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Modal -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <h2>Create New User</h2>
                <form action="user_action.php" method="POST" class="form">
                    <input type="hidden" name="action" value="add_user">

                    <label>Username
                        <input type="text" name="username" required />
                    </label>
                    <label>Email
                        <input type="email" name="email" required />
                    </label>
                    <label>Role
                        <select name="role" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r; ?>">
                                    <?php echo $r; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Password
                        <input type="password" name="password" required />
                    </label>

                    <div style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" onclick="toggleAddModal()" class="secondary full">Cancel</button>
                        <button type="submit" class="primary full">Create User</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>

</html>