<?php
require_once 'config/db.php';

try {
    // Check if key exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM api_configurations WHERE service_name = 'google_maps'");
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    $demoKey = "AIzaSy_DEMO_KEY_PLACEHOLDER_FOR_PIVO_SYSTEM";

    if ($exists) {
        $upd = $pdo->prepare("UPDATE api_configurations SET api_key = ? WHERE service_name = 'google_maps'");
        $upd->execute([$demoKey]);
        echo "Updated existing Google Maps key to Demo Key.\n";
    } else {
        $ins = $pdo->prepare("INSERT INTO api_configurations (service_name, api_key) VALUES ('google_maps', ?)");
        $ins->execute([$demoKey]);
        echo "Inserted new Demo Google Maps key.\n";
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>