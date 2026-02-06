<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'Admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update_stock') {
        // ... (Existing Stock Update Logic) ...
        $invId = intval($_POST['inventory_id']);
        $newQty = intval($_POST['quantity']);
        $newPrice = floatval($_POST['price']);
        $userId = $_SESSION['user_id'];

        if ($invId && $newQty >= 0) {
            try {
                $pdo->beginTransaction();
                // Get Current State
                $stmt = $pdo->prepare("SELECT quantity_in_stock, product_id FROM inventory WHERE inventory_id = ?");
                $stmt->execute([$invId]);
                $current = $stmt->fetch();
                if (!$current)
                    die("Inventory item not found.");

                $diff = $newQty - $current['quantity_in_stock'];
                // Update Inventory
                $upd = $pdo->prepare("UPDATE inventory SET quantity_in_stock = ? WHERE inventory_id = ?");
                $upd->execute([$newQty, $invId]);
                // Update Price
                $updPrice = $pdo->prepare("UPDATE products SET unit_price = ? WHERE product_id = ?");
                $updPrice->execute([$newPrice, $current['product_id']]);
                // Log
                if ($diff != 0) {
                    $log = $pdo->prepare("INSERT INTO inventory_logs (product_id, change_amount, new_quantity, changed_by_user_id, reason, timestamp) VALUES (?, ?, ?, ?, 'Manual Update', NOW())");
                    $log->execute([$current['product_id'], $diff, $newQty, $userId]);
                }
                $pdo->commit();
                header("Location: inventory.php?success=1");
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                die($e->getMessage());
            }
        }
    }

    // ADD PRODUCT ACTION
    if ($_POST['action'] === 'add_product') {
        $name = trim($_POST['product_name']);
        $price = floatval($_POST['unit_price']);
        $desc = trim($_POST['description']);
        $sizeId = $_POST['size_id'];

        try {
            $pdo->beginTransaction();

            // Handle New Size
            if ($sizeId === 'new') {
                $newSize = trim($_POST['new_size']);
                if (empty($newSize))
                    throw new Exception("New size is required.");

                // Check if exists
                $chkSz = $pdo->prepare("SELECT size_id FROM sizes WHERE volume_ml = ?");
                $chkSz->execute([$newSize]);
                if ($exSz = $chkSz->fetch()) {
                    $sizeId = $exSz['size_id'];
                } else {
                    $insSz = $pdo->prepare("INSERT INTO sizes (volume_ml) VALUES (?)");
                    $insSz->execute([$newSize]);
                    $sizeId = $pdo->lastInsertId();
                }
            }

            // Create Product
            $insProd = $pdo->prepare("INSERT INTO products (product_name, size_id, unit_price, description, volume_ml) VALUES (?, ?, ?, ?, '')");
            // Note: volume_ml in products is deprecated but keeping empty string to avoid DB error if NOT NULL constraint exists. 
            // Wait, schema has it as NOT NULL. Let's act safe.
            // Actually, we should fetch the volume_ml string to populate the legacy column just in case.
            $szStrStmt = $pdo->prepare("SELECT volume_ml FROM sizes WHERE size_id = ?");
            $szStrStmt->execute([$sizeId]);
            $szStr = $szStrStmt->fetchColumn();

            // Re-prepare insert with legacy column support
            $insProd = $pdo->prepare("INSERT INTO products (product_name, size_id, unit_price, description, volume_ml, image_path) VALUES (?, ?, ?, ?, ?, '')");
            $insProd->execute([$name, $sizeId, $price, $desc, $szStr]);
            $newProdId = $pdo->lastInsertId();

            // Initialize Inventory
            $insInv = $pdo->prepare("INSERT INTO inventory (product_id, quantity_in_stock) VALUES (?, 0)");
            $insInv->execute([$newProdId]);

            $pdo->commit();
            header("Location: manage_products.php?success=1");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            header("Location: manage_products.php?error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}

// DELETE PRODUCT ACTION via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete_product' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $pdo->beginTransaction();

        // Delete Inventory first (Foreign Key)
        $pdo->prepare("DELETE FROM inventory WHERE product_id = ?")->execute([$id]);

        // Delete Product
        $pdo->prepare("DELETE FROM products WHERE product_id = ?")->execute([$id]);

        $pdo->commit();
        header("Location: manage_products.php?success=Deleted");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: manage_products.php?error=" . urlencode("Cannot delete: Product might be in used in Orders."));
        exit;
    }
}
?>