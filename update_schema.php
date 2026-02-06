<?php
// Script to update the Database Schema
require_once 'config/db.php';

try {
    echo "Checking Database Schema...\n";

    // 1. Create inventory_logs table
    $sql = "CREATE TABLE IF NOT EXISTS `inventory_logs` (
        `log_id` int(11) NOT NULL AUTO_INCREMENT,
        `product_id` int(11) NOT NULL,
        `change_amount` int(11) NOT NULL,
        `new_quantity` int(11) NOT NULL,
        `changed_by_user_id` int(11) DEFAULT NULL,
        `reason` varchar(255) DEFAULT NULL,
        `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`log_id`),
        KEY `product_id` (`product_id`),
        KEY `changed_by_user_id` (`changed_by_user_id`),
        CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
        CONSTRAINT `inventory_logs_ibfk_2` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $pdo->exec($sql);
    echo "[SUCCESS] inventory_logs table created or already exists.\n";

    // 2. Ensure orders table has is_critical column
    // The previous SQL dump showed it, but let's be safe and check.
    $stmt = $pdo->query("SHOW COLUMNS FROM `orders` LIKE 'is_critical'");
    $col = $stmt->fetch();

    if (!$col) {
        $pdo->exec("ALTER TABLE `orders` ADD COLUMN `is_critical` tinyint(1) DEFAULT 0");
        echo "[SUCCESS] Added 'is_critical' column to orders table.\n";
    } else {
        echo "[INFO] 'orders.is_critical' already exists.\n";
    }

    echo "Schema Update Completed Successfully.\n";

} catch (PDOException $e) {
    echo "[ERROR] Schema Update Failed: " . $e->getMessage() . "\n";
}
?>