<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['FactoryOwner', 'Admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'update_stock') {
    header("Location: inventory.php");
    exit;
}

$invId = intval($_POST['inventory_id']);
$newQty = intval($_POST['quantity']);
$newPrice = floatval($_POST['price']);

if (!$invId || $newQty < 0 || $newPrice < 0) {
    header("Location: inventory.php?error=" . urlencode("Invalid input values."));
    exit;
}

try {
    // Get product_id for price update
    $stmt = $pdo->prepare("SELECT product_id FROM inventory WHERE inventory_id = ?");
    $stmt->execute([$invId]);
    $row = $stmt->fetch();

    if (!$row) {
        header("Location: inventory.php?error=" . urlencode("Inventory record not found."));
        exit;
    }

    $pdo->beginTransaction();

    // Update stock
    $pdo->prepare("UPDATE inventory SET quantity_in_stock = ? WHERE inventory_id = ?")
        ->execute([$newQty, $invId]);

    // Update price
    $pdo->prepare("UPDATE products SET unit_price = ? WHERE product_id = ?")
        ->execute([$newPrice, $row['product_id']]);

    $pdo->commit();
    header("Location: inventory.php?success=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: inventory.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>