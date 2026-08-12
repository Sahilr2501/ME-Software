<?php

require_once '../config/database.php';

$pageTitle = 'Add Production';

$error = '';

$products = $pdo->query("
    SELECT id, product_name, capacity
    FROM products
    WHERE status = 1
    ORDER BY capacity
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productId = (int)$_POST['product_id'];
    $productionDate = $_POST['production_date'];
    $quantity = (int)$_POST['quantity'];
    $notes = trim($_POST['notes'] ?? '');

    if ($productId <= 0 || $quantity <= 0 || !$productionDate) {

        $error = 'Please enter valid production details.';

    } else {

        try {

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO production
                (
                    product_id,
                    production_date,
                    quantity,
                    notes
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $productId,
                $productionDate,
                $quantity,
                $notes
            ]);

            $productionId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("
                UPDATE products
                SET stock_quantity = stock_quantity + ?
                WHERE id = ?
            ");

            $stmt->execute([
                $quantity,
                $productId
            ]);

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
                VALUES (?, 'IN', ?, 'PRODUCTION', ?, ?)
            ");

            $stmt->execute([
                $productId,
                $quantity,
                $productionId,
                $notes
            ]);

            $pdo->commit();

            header('Location: index.php');
            exit;

        } catch (Exception $e) {

            $pdo->rollBack();

            $error = 'Unable to save production.';
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

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="form-group">

        <label>Production Date *</label>

        <input
            type="date"
            name="production_date"
            class="form-control"
            value="<?= date('Y-m-d') ?>"
            required
        >

    </div>

    <div class="form-group">

        <label>Quantity Produced *</label>

        <input
            type="number"
            name="quantity"
            class="form-control"
            min="1"
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
        Save Production
    </button>

    <a href="index.php" class="btn btn-secondary">
        Cancel
    </a>

</form>

</div>

<?php include '../includes/footer.php'; ?>