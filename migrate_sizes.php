<?php
require_once 'config/db.php';

echo "Migrating Sizes...\n";

try {
    // 1. Create sizes table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `sizes` (
        `size_id` int(11) NOT NULL AUTO_INCREMENT,
        `volume_ml` varchar(50) NOT NULL UNIQUE,
        PRIMARY KEY (`size_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Created sizes table.\n";

    // 2. Populate sizes from existing products
    $pdo->exec("INSERT IGNORE INTO sizes (volume_ml) SELECT DISTINCT volume_ml FROM products WHERE volume_ml IS NOT NULL");
    echo "Populated sizes table.\n";

    // 3. Add size_id column to products
    // Check if exists first
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'size_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE products ADD COLUMN `size_id` int(11) DEFAULT NULL");
        $pdo->exec("ALTER TABLE products ADD CONSTRAINT `fk_product_size` FOREIGN KEY (`size_id`) REFERENCES `sizes`(`size_id`)");
        echo "Added size_id column to products.\n";
    }

    // 4. Update products with size_id
    $pdo->exec("UPDATE products p JOIN sizes s ON p.volume_ml = s.volume_ml SET p.size_id = s.size_id");
    echo "Linked products to sizes.\n";

    // 5. Verification
    $stmt = $pdo->query("SELECT p.product_name, s.volume_ml FROM products p JOIN sizes s ON p.size_id = s.size_id LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>