<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['Admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pw_reset_tickets.php");
    exit;
}

$ticketId = intval($_POST['ticket_id'] ?? 0);
$userId = intval($_POST['user_id'] ?? 0);
$newPw = trim($_POST['new_password'] ?? '');
$confirmPw = trim($_POST['confirm_password'] ?? '');

if (!$ticketId || !$userId || empty($newPw)) {
    header("Location: pw_reset_tickets.php?error=" . urlencode("Missing required fields."));
    exit;
}

if ($newPw !== $confirmPw) {
    header("Location: pw_reset_tickets.php?error=" . urlencode("Passwords do not match."));
    exit;
}

// Verify ticket belongs to user and is open
$chk = $pdo->prepare("SELECT ticket_id FROM pw_reset_tickets WHERE ticket_id = ? AND user_id = ? AND status = 'Open'");
$chk->execute([$ticketId, $userId]);
if (!$chk->fetch()) {
    header("Location: pw_reset_tickets.php?error=" . urlencode("Ticket not found or already resolved."));
    exit;
}

try {
    $pdo->beginTransaction();

    // Update password
    $hash = password_hash($newPw, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password_hash = ?, pw_reset_pending = 0 WHERE user_id = ?")
        ->execute([$hash, $userId]);

    // Resolve ticket
    $pdo->prepare("UPDATE pw_reset_tickets SET status = 'Resolved', resolved_at = NOW() WHERE ticket_id = ?")
        ->execute([$ticketId]);

    $pdo->commit();
    header("Location: pw_reset_tickets.php?success=resolved");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: pw_reset_tickets.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>