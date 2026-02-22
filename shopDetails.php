<?php 
include 'fetch_shops.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PIVO | Shop Management</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 30px; background-color: #f4f7f6; }
        .table-container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #FF8C00; margin-bottom: 20px; border-bottom: 2px solid #FF8C00; display: inline-block; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #FF8C00; color: white; }
    </style>
</head>
<body>

    <div class="table-container">
        <h2>Registered Shops</h2>
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
                if (isset($result) && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["shop_id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["shop_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["Address"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["user_category"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["number"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center;'>No shops found in the system.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php 
if (isset($conn)) { $conn->close(); } 
?>