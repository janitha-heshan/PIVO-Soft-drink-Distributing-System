<?php
// Database configuration
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "pivodb"; 
$port = 3307;        

$conn = new mysqli($host, $db_user, $db_pass, $db_name, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search variable
$search_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Shops</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }

        /* The white card container */
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 1000px;
        }

        h2 {
            color: #ff8c00; /* Dark orange */
            border-bottom: 2px solid #ff8c00;
            display: inline-block;
            margin-bottom: 20px;
            padding-bottom: 5px;
        }

        /* Search Bar Style */
        .search-section {
            margin-bottom: 20px;
        }
        input[type="number"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 8px 15px;
            background-color: #ff8c00;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #e67e00;
        }

        /* Table Styling to match your image */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #ff8c00; /* Orange header */
            color: white;
            text-align: left;
            padding: 12px;
            font-weight: 600;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            color: #333;
            font-size: 14px;
        }

        tr:hover {
            background-color: #fafafa;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Registered Shops</h2>

    <div class="search-section">
        <form method="GET">
            <input type="number" name="shop_id" placeholder="Search by Shop ID..." value="<?php echo htmlspecialchars($search_id); ?>">
            <button type="submit">Search</button>
            <a href="index.php" style="margin-left:10px; color:#666; text-decoration:none; font-size:12px;">Clear</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Shop ID</th>
                <th>Shop Name</th>
                <th>username</th>
                <th>Address</th>
                <th>user_category</th>
                <th>Owner Contact</th>
                <th>email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // SQL Logic
            if (!empty($search_id)) {
                $stmt = $conn->prepare("SELECT shops.shop_id, shops.shop_name, shops.Address, shops.user_category, 
                                               pivousers.number, pivousers.email, pivousers.username
                                        FROM shops 
                                        INNER JOIN pivousers ON shops.user_id = pivousers.id
                                        WHERE shops.shop_id = ?");
                $stmt->bind_param("i", $search_id);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                // Default: Show all if no search
                $sql = "SELECT shops.shop_id, shops.shop_name, shops.Address, shops.user_category, 
                               pivousers.number, pivousers.email, pivousers.username
                        FROM shops 
                        INNER JOIN pivousers ON shops.user_id = pivousers.id";
                $result = $conn->query($sql);
            }

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['shop_id']}</td>
                            <td>{$row['shop_name']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['Address']}</td>
                            <td>{$row['user_category']}</td>
                            <td>{$row['number']}</td>
                            <td>{$row['email']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>No shops found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>