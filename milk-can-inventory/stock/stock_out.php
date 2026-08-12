<?php

require_once '../config/database.php';

$pageTitle = 'Stock Out';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM products
    WHERE id = ?
    AND status = 1
");

$stmt->execute([$id]);

$product = $stmt->fetch();

if (!$product) {
    die('Product not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantity = (int)$_POST['quantity'];
    $notes = trim($_POST['notes'] ?? '');

    if ($quantity <= 0) {

        $error = 'Quantity must be greater than zero.';

    } elseif ($quantity > $product['stock_quantity']) {

        $error = 'Stock out quantity cannot be greater than current stock.';

    } else {

        try {

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                UPDATE products
                SET stock_quantity = stock_quantity - ?
                WHERE id = ?
                AND stock_quantity >= ?
            ");

            $stmt->execute([
                $quantity,
                $id,
                $quantity
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception('Insufficient stock.');
            }

            $stmt = $pdo->prepare("
                INSERT INTO stock_movements
                (
                    product_id,
                    movement_type,
                    quantity,
                    reference_type,
                    notes
                )
                VALUES (?, 'OUT', ?, 'MANUAL', ?)
            ");

            $stmt->execute([
                $id,
                $quantity,
                $notes
            ]);

            $pdo->commit();

            header('Location: index.php');
            exit;

        } catch (Exception $e) {

            $pdo->rollBack();

            $error = 'Unable to update stock.';
        }
    }
}

include '../includes/header.php';
?>

<div class="form-box">

<h3>
    <?= htmlspecialchars($product['product_name']) ?>
    - <?= $product['capacity'] ?> L
</h3>

<p>
    Current Stock:
    <strong><?= $product['stock_quantity'] ?></strong>
</p>

<?php if ($error): ?>

<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<form method="POST">

    <div class="form-group">

        <label>Quantity *</label>

        <input
            type="number"
            name="quantity"
            class="form-control"
            min="1"
            max="<?= $product['stock_quantity'] ?>"
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

    <button class="btn btn-warning">
        Remove Stock
    </button>

    <a href="index.php" class="btn btn-secondary">
        Cancel
    </a>

</form>

</div>

<?php include '../includes/footer.php'; ?>