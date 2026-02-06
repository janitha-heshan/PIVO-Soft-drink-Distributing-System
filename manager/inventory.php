<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'Admin']);

// Fetch Inventory
$stmt = $pdo->query("
    SELECT i.inventory_id, i.quantity_in_stock, p.product_name, p.unit_price, s.volume_ml
    FROM inventory i
    JOIN products p ON i.product_id = p.product_id
    JOIN sizes s ON p.size_id = s.size_id
    ORDER BY p.product_name ASC
");
$inventory = $stmt->fetchAll();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Inventory</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            width: 400px;
        }
    </style>
    <script>
        function openEditModal(id, name, stock, price) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('inv_id').value = id;
            document.getElementById('edit_name').value = name; // Just for display
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_price').value = price;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Add Product Modal logic could be similar
    </script>
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <img src="../assets/images/logo-placeholder.png" alt="PIVO" class="logo" />
            <span class="brand-name">PIVO Manager</span>
        </div>
        <nav class="dash-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php" class="active">Inventory</a>
            <a href="manage_products.php">Products</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Inventory Management</h1>
            <button onclick="alert('Feature coming soon: Add Product')" class="primary">Add Product +</button>
        </div>

        <section class="summary-card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Price (LKR)</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $item): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($item['volume_ml']); ?>
                            </td>
                            <td>
                                <?php echo number_format($item['unit_price'], 2); ?>
                            </td>
                            <td
                                style="font-weight:bold; <?php echo ($item['quantity_in_stock'] < 50) ? 'color:#d93025;' : 'color:#0f5132;'; ?>">
                                <?php echo $item['quantity_in_stock']; ?>
                            </td>
                            <td>
                                <button
                                    onclick="openEditModal(<?php echo $item['inventory_id']; ?>, '<?php echo $item['product_name']; ?>', <?php echo $item['quantity_in_stock']; ?>, <?php echo $item['unit_price']; ?>)"
                                    class="link-btn">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <h2>Update Stock</h2>
                <form action="product_action.php" method="POST" class="form">
                    <input type="hidden" name="action" value="update_stock">
                    <input type="hidden" name="inventory_id" id="inv_id">

                    <label>Product <input type="text" id="edit_name" disabled /></label>
                    <label>Stock Quantity <input type="number" name="quantity" id="edit_stock" required /></label>
                    <label>Unit Price <input type="number" name="price" id="edit_price" step="0.01" required /></label>

                    <div style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" onclick="closeEditModal()" class="secondary full">Cancel</button>
                        <button type="submit" class="primary full">Update</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>

</html>