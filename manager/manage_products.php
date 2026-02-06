<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['StoreManager', 'Admin']);

// Fetch Products
$stmt = $pdo->query("
    SELECT p.*, s.volume_ml 
    FROM products p 
    JOIN sizes s ON p.size_id = s.size_id 
    ORDER BY p.product_name ASC
");
$products = $stmt->fetchAll();

// Fetch All Sizes for Dropdown
$sizeStmt = $pdo->query("SELECT * FROM sizes ORDER BY volume_ml");
$sizes = $sizeStmt->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PIVO — Manage Products</title>
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
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 12px;
            width: 450px;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function checkSize(select) {
            const newSizeInput = document.getElementById('new_size_input');
            if (select.value === 'new') {
                newSizeInput.style.display = 'block';
                newSizeInput.required = true;
            } else {
                newSizeInput.style.display = 'none';
                newSizeInput.required = false;
            }
        }

        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete ' + name + '? This will also remove it from inventory.')) {
                window.location.href = 'product_action.php?action=delete_product&id=' + id;
            }
        }
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
            <a href="inventory.php">Inventory</a>
            <a href="manage_products.php" class="active">Products</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <div class="dash-header">
            <h1>Product Catalog</h1>
            <button onclick="openAddModal()" class="primary">Add New Product +</button>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="background:#ffebeb; color:#d93025; padding:12px; border-radius:8px; margin-bottom:20px;">
                Error:
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div style="background:#d1e7dd; color:#0f5132; padding:12px; border-radius:8px; margin-bottom:20px;">
                Action completed successfully!
            </div>
        <?php endif; ?>

        <section class="summary-card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Price (LKR)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <div style="width:40px; height:40px; background:#eee; border-radius:4px;"></div>
                            </td>
                            <td><strong>
                                    <?php echo htmlspecialchars($p['product_name']); ?>
                                </strong></td>
                            <td>
                                <?php echo htmlspecialchars($p['volume_ml']); ?>
                            </td>
                            <td>
                                <?php echo number_format($p['unit_price'], 2); ?>
                            </td>
                            <td>
                                <button
                                    onclick="confirmDelete(<?php echo $p['product_id']; ?>, '<?php echo htmlspecialchars($p['product_name']); ?>')"
                                    class="link-btn" style="color:#d93025;">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Add Modal -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <h2>Add New Product</h2>
                <form action="product_action.php" method="POST" class="form">
                    <input type="hidden" name="action" value="add_product">

                    <label>Product Name
                        <input type="text" name="product_name" list="product_names_list" placeholder="e.g. Mango Nectar"
                            required />
                        <!-- Datalist for suggestions of existing names -->
                        <datalist id="product_names_list">
                            <?php
                            $uniqueNames = array_unique(array_column($products, 'product_name'));
                            foreach ($uniqueNames as $name)
                                echo "<option value='$name'>";
                            ?>
                        </datalist>
                    </label>

                    <label>Size
                        <select name="size_id" onchange="checkSize(this)" required>
                            <option value="" disabled selected>-- Select Size --</option>
                            <?php foreach ($sizes as $s): ?>
                                <option value="<?php echo $s['size_id']; ?>">
                                    <?php echo htmlspecialchars($s['volume_ml']); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="new">+ Add New Size</option>
                        </select>
                        <input type="text" name="new_size" id="new_size_input" placeholder="Enter Size (e.g. 1.25L)"
                            style="display:none; margin-top:8px;" />
                    </label>

                    <label>Unit Price (LKR)
                        <input type="number" name="unit_price" step="0.01" required />
                    </label>

                    <label>Description
                        <textarea name="description" rows="3"
                            style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;"></textarea>
                    </label>

                    <div style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" onclick="closeAddModal()" class="secondary full">Cancel</button>
                        <button type="submit" class="primary full">Create Product</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>

</html>