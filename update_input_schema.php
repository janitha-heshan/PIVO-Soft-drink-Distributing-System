<?php
require_once 'config/db.php';

echo "Updating Schema for API Keys...\n";

try {
    $sql = "CREATE TABLE IF NOT EXISTS `api_configurations` (
        `config_id` int(11) NOT NULL AUTO_INCREMENT,
        `service_name` varchar(100) NOT NULL UNIQUE,
        `api_key` varchar(255) NOT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`config_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "[SUCCESS] api_configurations table created.\n";

    // Insert Default Placeholder for Google Maps
    $stmt = $pdo->prepare("INSERT IGNORE INTO api_configurations (service_name, api_key) VALUES ('google_maps', '')");
    $stmt->execute();
    echo "[INFO] Placeholder for google_maps inserted.\n";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
?>