<?php
require_once '../config/db.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_password'])) {
    $userId = (int) $_POST['user_id'];
    $newPassword = trim($_POST['new_password']);
    $confirmPw = trim($_POST['confirm_password']);

    if (empty($newPassword)) {
        $message = 'Password cannot be empty.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPw) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->execute([$hash, $userId]);

        if ($stmt->rowCount() > 0) {
            $message = 'Password updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'No user found with that ID, or password unchanged.';
            $messageType = 'error';
        }
    }
}

// Fetch all users
$users = $pdo->query("SELECT user_id, username, role, email FROM users ORDER BY role, username")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password — Dev Tool</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0f1117;
            --surface: #1a1d27;
            --surface2: #22263a;
            --border: #2e3350;
            --accent: #6c63ff;
            --accent2: #a78bfa;
            --success: #22c55e;
            --error: #ef4444;
            --text: #e2e8f0;
            --muted: #7c8098;
            --radius: 14px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            width: 100%;
            max-width: 520px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            background: linear-gradient(135deg, #6c63ff22, #a78bfa11);
            border-bottom: 1px solid var(--border);
            padding: 1.75rem 2rem;
        }

        .badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent2);
            background: #6c63ff22;
            border: 1px solid #6c63ff44;
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            margin-bottom: 0.75rem;
        }

        .card-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }

        .card-header p {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 0.35rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Alert */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1.1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background: #22c55e18;
            border: 1px solid #22c55e44;
            color: var(--success);
        }

        .alert-error {
            background: #ef444418;
            border: 1px solid #ef444444;
            color: var(--error);
        }

        .alert-icon {
            font-size: 1.1rem;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form */
        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.5rem;
        }

        select,
        input[type="password"] {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%237c8098'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            cursor: pointer;
        }

        select:focus,
        input[type="password"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px #6c63ff30;
        }

        select option {
            background: var(--surface2);
        }

        /* User preview card */
        #userPreview {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            margin-top: 0.75rem;
            font-size: 0.82rem;
            color: var(--muted);
            display: none;
            gap: 0.5rem 1.5rem;
            flex-wrap: wrap;
        }

        #userPreview.visible {
            display: flex;
        }

        #userPreview span strong {
            color: var(--text);
        }

        /* Strength indicator */
        .strength-bar-wrap {
            display: flex;
            gap: 4px;
            margin-top: 0.5rem;
        }

        .strength-seg {
            height: 4px;
            flex: 1;
            border-radius: 100px;
            background: var(--border);
            transition: background 0.3s;
        }

        /* Submit button */
        .btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 20px #6c63ff40;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 8px 28px #6c63ff55;
        }

        .btn:active {
            transform: translateY(0);
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 1.5rem 0;
        }

        .footer-note {
            text-align: center;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .footer-note span {
            display: inline-block;
            background: #ef444418;
            border: 1px solid #ef444433;
            color: #ef9090;
            padding: 0.15rem 0.6rem;
            border-radius: 100px;
            font-weight: 600;
            margin-right: 0.3rem;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="card-header">
            <div class="badge">🔧 Dev Tool</div>
            <h1>Update User Password</h1>
            <p>Select any user and set a new password instantly.</p>
        </div>
        <div class="card-body">

            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <span class="alert-icon">
                        <?= $messageType === 'success' ? '✅' : '❌' ?>
                    </span>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">

                <!-- User Selector -->
                <div class="form-group">
                    <label for="user_id">Select User</label>
                    <select name="user_id" id="user_id" required onchange="updatePreview(this)">
                        <option value="">— Choose a user —</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['user_id'] ?>" data-role="<?= htmlspecialchars($u['role']) ?>"
                                data-email="<?= htmlspecialchars($u['email']) ?>" <?= (isset($_POST['user_id']) && $_POST['user_id'] == $u['user_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- User info preview -->
                    <div id="userPreview">
                        <span>Role: <strong id="previewRole">—</strong></span>
                        <span>Email: <strong id="previewEmail">—</strong></span>
                    </div>
                </div>

                <hr class="divider">

                <!-- New Password -->
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password"
                        required oninput="checkStrength(this.value)">
                    <div class="strength-bar-wrap">
                        <div class="strength-seg" id="seg1"></div>
                        <div class="strength-seg" id="seg2"></div>
                        <div class="strength-seg" id="seg3"></div>
                        <div class="strength-seg" id="seg4"></div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter password"
                        required>
                </div>

                <button type="submit" class="btn">🔑 Update Password</button>
            </form>

            <hr class="divider">
            <p class="footer-note"><span>TEST</span> Not linked to production — dev use only.</p>
        </div>
    </div>

    <script>
        // Show user role/email preview when selected
        function updatePreview(sel) {
            const opt = sel.options[sel.selectedIndex];
            const preview = document.getElementById('userPreview');
            if (!opt.value) {
                preview.classList.remove('visible');
                return;
            }
            document.getElementById('previewRole').textContent = opt.dataset.role || '—';
            document.getElementById('previewEmail').textContent = opt.dataset.email || '—';
            preview.classList.add('visible');
        }

        // Password strength indicator
        function checkStrength(val) {
            const segs = [
                document.getElementById('seg1'),
                document.getElementById('seg2'),
                document.getElementById('seg3'),
                document.getElementById('seg4'),
            ];
            const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            segs.forEach((s, i) => {
                s.style.background = i < score ? colors[score - 1] : 'var(--border)';
            });
        }

        // Trigger preview if a user is already selected (after form error)
        window.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('user_id');
            if (sel.value) updatePreview(sel);
        });
    </script>

</body>

</html>