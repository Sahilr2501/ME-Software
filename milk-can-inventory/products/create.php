<?php

require_once '../config/database.php';

$pageTitle = 'Add Product';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productName = trim($_POST['product_name'] ?? '');
    $capacity = (float)($_POST['capacity'] ?? 0);
    $material = trim($_POST['material'] ?? '');
    $stock = (int)($_POST['stock_quantity'] ?? 0);
    $minimumStock = (int)($_POST['minimum_stock'] ?? 5);

    if ($productName === '' || $capacity <= 0 || $material === '') {

        $error = 'Please fill all required fields.';

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO products
            (
                product_name,
                capacity,
                material,
                stock_quantity,
                minimum_stock
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $productName,
            $capacity,
            $material,
            $stock,
            $minimumStock
        ]);

        header('Location: index.php');
        exit;
    }
}

include '../includes/header.php';
?>

<div class="form-box">

<?php if ($error): ?>

<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<form method="POST">

    <div class="form-group">
        <label>Product Name *</label>

        <input
            type="text"
            name="product_name"
            class="form-control"
            required
        >
    </div>

    <div class="form-group">
        <label>Capacity (Litres) *</label>

        <input
            type="number"
            step="0.01"
            name="capacity"
            class="form-control"
            required
        >
    </div>

    <div class="form-group">
        <label>Material *</label>

        <input
            type="text"
            name="material"
            class="form-control"
            placeholder="Stainless Steel"
            required
        >
    </div>

    <div class="form-group">
        <label>Opening Stock</label>

        <input
            type="number"
            name="stock_quantity"
            class="form-control"
            value="0"
            min="0"
        >
    </div>

    <div class="form-group">
        <label>Minimum Stock</label>

        <input
            type="number"
            name="minimum_stock"
            class="form-control"
            value="5"
            min="0"
        >
    </div>

    <button class="btn btn-success">
        Save Product
    </button>

    <a href="index.php" class="btn btn-secondary">
        Cancel
    </a>

</form>

</div>

<?php include '../includes/footer.php'; ?>