<?php

require_once '../config/database.php';

$pageTitle = 'Sale Details';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM sales
    WHERE id = ?
");

$stmt->execute([$id]);

$sale = $stmt->fetch();

if (!$sale) {
    die('Sale not found.');
}

$stmt = $pdo->prepare("
    SELECT
        sale_items.*,
        products.product_name,
        products.capacity
    FROM sale_items

    INNER JOIN products
        ON products.id = sale_items.product_id

    WHERE sale_items.sale_id = ?

    ORDER BY sale_items.id
");

$stmt->execute([$id]);

$items = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="table-box">

<h3>Sale Information</h3>

<p>
    <strong>Customer:</strong>
    <?= htmlspecialchars(
        $sale['customer_name'] ?: 'Walk-in Customer'
    ) ?>
</p>

<p>
    <strong>Date:</strong>
    <?= htmlspecialchars($sale['sale_date']) ?>
</p>

</div>

<div class="table-box">

<table>

<tr>
    <th>Product</th>
    <th>Capacity</th>
    <th>Quantity</th>
    <th>Selling Price</th>
    <th>Total</th>
</tr>

<?php foreach ($items as $item): ?>

<tr>

    <td>
        <?= htmlspecialchars($item['product_name']) ?>
    </td>

    <td>
        <?= $item['capacity'] ?> L
    </td>

    <td>
        <?= $item['quantity'] ?>
    </td>

    <td>
        ₹<?= number_format($item['selling_price'], 2) ?>
    </td>

    <td>
        ₹<?= number_format($item['amount'], 2) ?>
    </td>

</tr>

<?php endforeach; ?>

<tr>

    <th colspan="4" style="text-align:right;">
        Grand Total
    </th>

    <th>
        ₹<?= number_format($sale['total_amount'], 2) ?>
    </th>

</tr>

</table>

<br>

<a href="index.php" class="btn btn-secondary">
    Back
</a>

</div>

<?php include '../includes/footer.php'; ?>