<?php
$users = [
    'shop1' => 'shop123',
    'manager' => 'manager123',
    'driver' => 'driver123',
    'admin' => 'admin123',
    'factory' => 'factory123',
];

echo "-- Execute this SQL to update passwords to their secure hashed versions:\n";
foreach ($users as $u => $p) {
    echo "UPDATE users SET password_hash = '" . password_hash($p, PASSWORD_DEFAULT) . "' WHERE username = '$u';\n";
}
?>