<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['Admin']);

$success = $_GET['success'] ?? '';

// Fetch all open tickets
$tickets = $pdo->query("
    SELECT t.ticket_id, t.requested_at, t.status,
           u.user_id, u.username, u.full_name, u.role, u.email
    FROM pw_reset_tickets t
    JOIN users u ON t.user_id = u.user_id
    WHERE t.status = 'Open'
    ORDER BY t.requested_at DESC
")->fetchAll();

// Count for badge
$openCount = count($tickets);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>PIVO — Password Reset Tickets</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        .page-desc {
            font-size: 0.875rem;
            color: #555;
            margin-bottom: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #777;
        }

        .empty-state .icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
        }

        .badge-count {
            display: inline-block;
            background: #d93025;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 100px;
            margin-left: 8px;
        }

        /* Inline resolve form row */
        .resolve-form {
            display: none;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: 16px 20px;
        }

        .resolve-form.open {
            display: table-row;
        }

        .resolve-form td {
            padding: 16px 20px;
        }

        .resolve-inner {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .resolve-inner label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #555;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .resolve-inner input[type="password"] {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            width: 200px;
        }

        .flash-success {
            background: #dcfce7;
            color: #14532d;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }

        .pill-role {
            display: inline-block;
            background: #ede9fe;
            color: #5b21b6;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .time-ago {
            font-size: 0.8rem;
            color: #777;
        }
    </style>
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="../assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Admin</span>
        </div>
        <nav class="dash-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_users.php">Users</a>
            <a href="pw_reset_tickets.php" class="active">Reset Tickets</a>
            <a href="../logout.php">Logout</a>
            <button class="avatar" style="background:#d93025;">A</button>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>
                Password Reset Tickets
                <?php if ($openCount > 0): ?>
                    <span class="badge-count">
                        <?= $openCount ?> open
                    </span>
                <?php endif; ?>
            </h1>
        </div>

        <p class="page-desc">Review and resolve user password reset requests. Enter a new password to resolve each
            ticket.</p>

        <?php if ($success === 'resolved'): ?>
            <div class="flash-success">✅ Password updated and ticket resolved. The user can now log in with their new
                password.</div>
        <?php endif; ?>

        <section class="summary-card">
            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <div class="icon">🎉</div>
                    <p>No open tickets — all clear!</p>
                </div>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t): ?>
                            <tr id="row-<?= $t['ticket_id'] ?>">
                                <td>
                                    <?= $t['ticket_id'] ?>
                                </td>
                                <td><strong>
                                        <?= htmlspecialchars($t['username']) ?>
                                    </strong></td>
                                <td>
                                    <?= htmlspecialchars($t['full_name'] ?? '—') ?>
                                </td>
                                <td><span class="pill-role">
                                        <?= htmlspecialchars($t['role']) ?>
                                    </span></td>
                                <td class="time-ago">
                                    <?= date('d M Y, H:i', strtotime($t['requested_at'])) ?>
                                </td>
                                <td>
                                    <button class="primary" style="padding:6px 14px; font-size:0.8rem;"
                                        onclick="toggleResolve(<?= $t['ticket_id'] ?>)">
                                        🔑 Resolve
                                    </button>
                                </td>
                            </tr>
                            <!-- Inline resolve form -->
                            <tr id="resolve-<?= $t['ticket_id'] ?>" class="resolve-form">
                                <td colspan="6">
                                    <form method="POST" action="ticket_action.php" class="resolve-inner">
                                        <input type="hidden" name="ticket_id" value="<?= $t['ticket_id'] ?>" />
                                        <input type="hidden" name="user_id" value="<?= $t['user_id'] ?>" />
                                        <label>
                                            New Password for <em>
                                                <?= htmlspecialchars($t['username']) ?>
                                            </em>
                                            <input type="password" name="new_password" placeholder="Enter new password" required
                                                minlength="6" />
                                        </label>
                                        <label>
                                            Confirm Password
                                            <input type="password" name="confirm_password" placeholder="Confirm password"
                                                required />
                                        </label>
                                        <button type="submit" class="primary" style="padding:8px 20px;">Update Password</button>
                                        <button type="button" class="secondary" style="padding:8px 20px;"
                                            onclick="toggleResolve(<?= $t['ticket_id'] ?>)">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function toggleResolve(id) {
            const row = document.getElementById('resolve-' + id);
            row.classList.toggle('open');
        }
    </script>

</body>

</html>