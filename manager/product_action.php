<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'ShopOwner', 'Admin', 'FactoryOwner']);

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

        $imagePath = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/images/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['product_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                $imagePath = 'assets/images/products/' . $fileName;
            }
        }

        try {
            // Dynamic ENUM expansion (Add new product name format to DB if it doesn't exist)
            // MUST be outside transaction since ALTER TABLE implicitly commits!
            $enumStmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'product_name'");
            $enumRow = $enumStmt->fetch();
            preg_match("/^enum\(\'(.*)\'\)$/", $enumRow['Type'], $matches);
            $existingEnums = explode("','", $matches[1]);

            if (!in_array($name, $existingEnums)) {
                $existingEnums[] = $name;
                $newEnumStr = "'" . implode("','", array_map(function ($val) {
                    return addslashes($val);
                }, $existingEnums)) . "'";
                $pdo->exec("ALTER TABLE products MODIFY COLUMN product_name ENUM($newEnumStr) NOT NULL");
            }

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

            // Get the volume_ml string for the legacy column in products table (which is marked NOT NULL)
            $szStrStmt = $pdo->prepare("SELECT volume_ml FROM sizes WHERE size_id = ?");
            $szStrStmt->execute([$sizeId]);
            $szStr = $szStrStmt->fetchColumn();

            if (!$szStr) {
                throw new Exception("Invalid size selected.");
            }

            $insProd = $pdo->prepare("INSERT INTO products (product_name, size_id, unit_price, description, volume_ml, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $insProd->execute([$name, $sizeId, $price, $desc, $szStr, $imagePath]);
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

    // EDIT PRODUCT ACTION
    if ($_POST['action'] === 'edit_product') {
        $prodId = intval($_POST['product_id']);
        $name = trim($_POST['product_name']);
        $price = floatval($_POST['unit_price']);
        $desc = trim($_POST['description']);

        $imageUpdateSql = "";
        $imageParams = [];

        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/images/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['product_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                $imagePath = 'assets/images/products/' . $fileName;
                $imageUpdateSql = ", image_path = ?";
                $imageParams[] = $imagePath;
            }
        }

        if ($prodId && !empty($name)) {
            try {
                // Dynamic ENUM expansion for Edit
                $enumStmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'product_name'");
                $enumRow = $enumStmt->fetch();
                preg_match("/^enum\(\'(.*)\'\)$/", $enumRow['Type'], $matches);
                $existingEnums = explode("','", $matches[1]);

                if (!in_array($name, $existingEnums)) {
                    $existingEnums[] = $name;
                    $newEnumStr = "'" . implode("','", array_map(function ($val) {
                        return addslashes($val);
                    }, $existingEnums)) . "'";
                    $pdo->exec("ALTER TABLE products MODIFY COLUMN product_name ENUM($newEnumStr) NOT NULL");
                }

                $sql = "UPDATE products SET product_name = ?, unit_price = ?, description = ?" . $imageUpdateSql . " WHERE product_id = ?";
                $params = array_merge([$name, $price, $desc], $imageParams, [$prodId]);

                $updProd = $pdo->prepare($sql);
                $updProd->execute($params);

                header("Location: manage_products.php?success=1");
                exit;
            } catch (Exception $e) {
                header("Location: manage_products.php?error=" . urlencode($e->getMessage()));
                exit;
            }
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