<?php
// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json'); // Tell the browser we are sending JSON

$host = "localhost";
$db_user = "root"; 
$db_pass = "";     
$db_name = "pivodb";
$port = 3307; // Verify if your MySQL is on 3307 or 3306

try {
    $conn = new mysqli($host, $db_user, $db_pass, $db_name, $port);

    // Check for POST request instead of the 'submit' button name
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $user_id       = $_POST['user_id'] ?? null;
        $shop_name     = $_POST['shop_name'] ?? null;
        $address       = $_POST['Address'] ?? null; // Matches HTML name='Address'
        $user_category = $_POST['user_category'] ?? null;

        if (empty($user_id) || empty($shop_name)) {
            echo json_encode(["status" => "error", "message" => "Required fields are missing."]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO shops (user_id, shop_name, Address, user_category) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $shop_name, $address, $user_category);

        if ($stmt->execute()) {
            $new_shop_id = $conn->insert_id;
            echo json_encode([
                "status" => "success", 
                "shop_id" => $new_shop_id
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Execution failed."]);
        }
        $stmt->close();
    }
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>