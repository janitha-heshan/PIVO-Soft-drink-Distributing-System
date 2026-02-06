<?php
require_once 'config/db.php';

echo "Seeding Users...\n";

$users = [
    ['username' => 'admin', 'password' => 'admin123', 'role' => 'Admin', 'email' => 'admin@pivo.com'],
    ['username' => 'manager', 'password' => 'manager123', 'role' => 'StoreManager', 'email' => 'manager@pivo.com'],
    ['username' => 'supervisor', 'password' => 'super123', 'role' => 'SalesSupervisor', 'email' => 'super@pivo.com'],
    ['username' => 'factory', 'password' => 'factory123', 'role' => 'FactoryOwner', 'email' => 'factory@pivo.com'],
    ['username' => 'shop1', 'password' => 'shop123', 'role' => 'ShopOwner', 'email' => 'shop1@pivo.com'],
    ['username' => 'driver', 'password' => 'driver123', 'role' => 'SalesRep', 'email' => 'driver@pivo.com'],
];

foreach ($users as $u) {
    // Delete if exists to ensure password matches
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
    $stmt->execute([$u['username']]);

    $hash = password_hash($u['password'], PASSWORD_DEFAULT);

    // Insert sans status
    $ins = $pdo->prepare("INSERT INTO users (username, password_hash, role, email) VALUES (?, ?, ?, ?)");
    $ins->execute([$u['username'], $hash, $u['role'], $u['email']]);
    echo "Created/Reset user: {$u['username']} ({$u['role']})\n";
}

echo "Seeding Completed.\n";
?>