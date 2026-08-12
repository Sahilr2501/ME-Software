<?php

require_once '../config/database.php';

$pageTitle = 'Edit Product';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM products
    WHERE id = ?
");

$stmt->execute([$id]);

$product = $stmt->fetch();

if (!$product) {
    die('Product not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productName = trim($_POST['product_name']);
    $capacity = (float)$_POST['capacity'];
    $material = trim($_POST['material']);
    $minimumStock = (int)$_POST['minimum_stock'];

    $stmt = $pdo->prepare("
        UPDATE products
        SET
            product_name = ?,
            capacity = ?,
            material = ?,
            minimum_stock = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $productName,
        $capacity,
        $material,
        $minimumStock,
        $id
    ]);

    header('Location: index.php');
    exit;
}

include '../includes/header.php';
?>

<div class="form-box">

<form method="POST">

    <div class="form-group">
        <label>Product Name</label>

        <input
            type="text"
            name="product_name"
            class="form-control"
            value="<?= htmlspecialchars($product['product_name']) ?>"
            required
        >
    </div>

    <div class="form-group">
        <label>Capacity</label>

        <input
            type="number"
            step="0.01"
            name="capacity"
            class="form-control"
            value="<?= $product['capacity'] ?>"
            required
        >
    </div>

    <div class="form-group">
        <label>Material</label>

        <input
            type="text"
            name="material"
            class="form-control"
            value="<?= htmlspecialchars($product['material']) ?>"
            required
        >
    </div>

    <div class="form-group">
        <label>Current Stock</label>

        <input
            type="text"
            class="form-control"
            value="<?= $product['stock_quantity'] ?>"
            disabled
        >

        <small>
            Use Stock In / Stock Out to change stock.
        </small>
    </div>

    <div class="form-group">
        <label>Minimum Stock</label>

        <input
            type="number"
            name="minimum_stock"
            class="form-control"
            value="<?= $product['minimum_stock'] ?>"
        >
    </div>

    <button class="btn btn-success">
        Update Product
    </button>

    <a href="index.php" class="btn btn-secondary">
        Cancel
    </a>

</form>

</div>

<?php include '../includes/footer.php'; ?>