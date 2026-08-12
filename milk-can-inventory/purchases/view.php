<?php

require_once '../config/database.php';

$pageTitle = 'Purchase Details';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM purchases
    WHERE id = ?
");

$stmt->execute([$id]);

$purchase = $stmt->fetch();

if (!$purchase) {
    die('Purchase not found.');
}

$stmt = $pdo->prepare("
    SELECT *
    FROM purchase_items
    WHERE purchase_id = ?
    ORDER BY id
");

$stmt->execute([$id]);

$items = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="table-box">

<h3>Purchase Information</h3>

<p>
    <strong>Supplier:</strong>
    <?= htmlspecialchars($purchase['supplier_name']) ?>
</p>

<p>
    <strong>Invoice:</strong>
    <?= htmlspecialchars($purchase['invoice_no'] ?? '') ?>
</p>

<p>
    <strong>Date:</strong>
    <?= htmlspecialchars($purchase['purchase_date']) ?>
</p>

</div>

<div class="table-box">

<h3>Items</h3>

<table>

<tr>
    <th>Item</th>
    <th>Quantity</th>
    <th>Unit</th>
    <th>Rate</th>
    <th>Amount</th>
</tr>

<?php foreach ($items as $item): ?>

<tr>

    <td><?= htmlspecialchars($item['item_name']) ?></td>

    <td><?= $item['quantity'] ?></td>

    <td><?= htmlspecialchars($item['unit']) ?></td>

    <td>₹<?= number_format($item['rate'], 2) ?></td>

    <td>₹<?= number_format($item['amount'], 2) ?></td>

</tr>

<?php endforeach; ?>

<tr>

    <th colspan="4" style="text-align:right;">
        Total
    </th>

    <th>
        ₹<?= number_format($purchase['total_amount'], 2) ?>
    </th>

</tr>

</table>

<br>

<a href="index.php" class="btn btn-secondary">
    Back
</a>

</div>

<?php include '../includes/footer.php'; ?>