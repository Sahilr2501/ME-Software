<?php

require_once '../config/database.php';

$pageTitle = 'New Sale';

$error = '';

$products = $pdo->query("
    SELECT id, product_name, capacity, stock_quantity
    FROM products
    WHERE status = 1
    ORDER BY capacity
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customerName = trim($_POST['customer_name'] ?? '');
    $saleDate = $_POST['sale_date'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $sellingPrice = (float)($_POST['selling_price'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if ($productId <= 0) {

        $error = 'Please select a product.';

    } elseif ($quantity <= 0) {

        $error = 'Quantity must be greater than zero.';

    } elseif ($sellingPrice < 0) {
    
        $error = 'Selling price cannot be negative.';

    } elseif (!$saleDate) {

        $error = 'Please select sale date.';

    } else {

        try {

            $pdo->beginTransaction();

            // Get current stock
            $stmt = $pdo->prepare("
                SELECT *
                FROM products
                WHERE id = ?
                AND status = 1
                FOR UPDATE
            ");

            $stmt->execute([$productId]);

            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception('Product not found.');
            }

            // Check stock
            if ($quantity > $product['stock_quantity']) {

                throw new Exception(
                    'Not enough stock. Available stock: '
                    . $product['stock_quantity']
                );
            }

            $totalAmount = $quantity * $sellingPrice;

            // Create sale
            $stmt = $pdo->prepare("
                INSERT INTO sales
                (
                    customer_name,
                    sale_date,
                    total_amount,
                    notes
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $customerName,
                $saleDate,
                $totalAmount,
                $notes
            ]);

            $saleId = $pdo->lastInsertId();

            // Add sale item
            $stmt = $pdo->prepare("
                INSERT INTO sale_items
                (
                    sale_id,
                    product_id,
                    quantity,
                    selling_price,
                    amount
                )
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $saleId,
                $productId,
                $quantity,
                $sellingPrice,
                $totalAmount
            ]);

            // Reduce stock
            $stmt = $pdo->prepare("
                UPDATE products
                SET stock_quantity = stock_quantity - ?
                WHERE id = ?
            ");

            $stmt->execute([
                $quantity,
                $productId
            ]);

            // Stock movement
            $stmt = $pdo->prepare("
                INSERT INTO stock_movements
                (
                    product_id,
                    movement_type,
                    quantity,
                    reference_type,
                    reference_id,
                    notes
                )
                VALUES (?, 'OUT', ?, 'SALE', ?, ?)
            ");

            $stmt->execute([
                $productId,
                $quantity,
                $saleId,
                $notes
            ]);

            $pdo->commit();

            header('Location: index.php');
            exit;

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = $e->getMessage();
        }
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

        <label>Customer Name</label>

        <input
            type="text"
            name="customer_name"
            class="form-control"
            placeholder="Customer / Dairy Name"
        >

    </div>

    <div class="form-group">

        <label>Sale Date *</label>

        <input
            type="date"
            name="sale_date"
            class="form-control"
            value="<?= date('Y-m-d') ?>"
            required
        >

    </div>

    <div class="form-group">

        <label>Product *</label>

        <select
            name="product_id"
            class="form-control"
            required
        >

            <option value="">Select Product</option>

            <?php foreach ($products as $product): ?>

                <option value="<?= $product['id'] ?>">

                    <?= htmlspecialchars($product['product_name']) ?>
                    -
                    <?= $product['capacity'] ?> L
                    -
                    Stock: <?= $product['stock_quantity'] ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="form-group">

        <label>Quantity *</label>

        <input
            type="number"
            name="quantity"
            class="form-control"
            min="1"
            required
        >

    </div>

    <div class="form-group">

        <label>Selling Price Per Can *</label>

        <input
            type="number"
            step="0.01"
            name="selling_price"
            class="form-control"
            min="0"
            placeholder="Enter today's selling price"
            required
        >

    </div>

    <div class="form-group">

        <label>Notes</label>

        <textarea
            name="notes"
            class="form-control"
            rows="4"
        ></textarea>

    </div>

    <button class="btn btn-success">
        Save Sale
    </button>

    <a href="index.php" class="btn btn-secondary">
        Cancel
    </a>

</form>

</div>

<?php include '../includes/footer.php'; ?>