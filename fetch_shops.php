<?php
// Database configuration
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "pivodb"; 
$port = 3307;        

// Establish connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name, $port);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Added shop_id and changed pivousers column to 'number'
$sql = "SELECT shops.shop_id, shops.shop_name,shops.Address ,shops.user_category, pivousers.number,pivousers.email,pivousers.username
        FROM shops 
        INNER JOIN pivousers ON shops.user_id = pivousers.id"; 

$result = $conn->query($sql);

if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>