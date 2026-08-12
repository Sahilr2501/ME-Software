<?php

require_once '../config/database.php';

$pageTitle = 'Purchase Records';

$search = trim($_GET['search'] ?? '');
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

$sql = "
    SELECT *
    FROM purchases
    WHERE 1=1
";

$params = [];

if ($search !== '') {

    $sql .= "
        AND (
            supplier_name LIKE ?
            OR invoice_no LIKE ?
        )
    ";

    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($fromDate !== '') {

    $sql .= " AND purchase_date >= ?";
    $params[] = $fromDate;
}

if ($toDate !== '') {

    $sql .= " AND purchase_date <= ?";
    $params[] = $toDate;
}

$sql .= " ORDER BY purchase_date DESC, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$purchases = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="actions" style="margin-bottom:20px;">

    <a href="create.php" class="btn btn-success">
        + Add Purchase
    </a>

</div>

<form class="search-form" method="GET">

    <input
        type="text"
        name="search"
        class="form-control"
        placeholder="Supplier / Invoice"
        value="<?= htmlspecialchars($search) ?>"
    >

    <input
        type="date"
        name="from_date"
        class="form-control"
        value="<?= htmlspecialchars($fromDate) ?>"
    >

    <input
        type="date"
        name="to_date"
        class="form-control"
        value="<?= htmlspecialchars($toDate) ?>"
    >

    <button class="btn">
        Search
    </button>

    <a href="index.php" class="btn btn-secondary">
        Reset
    </a>

</form>

<div class="table-box">

<table>

<tr>
    <th>Date</th>
    <th>Supplier</th>
    <th>Invoice</th>
    <th>Total</th>
    <th>Actions</th>
</tr>

<?php foreach ($purchases as $purchase): ?>

<tr>

    <td><?= htmlspecialchars($purchase['purchase_date']) ?></td>

    <td><?= htmlspecialchars($purchase['supplier_name']) ?></td>

    <td><?= htmlspecialchars($purchase['invoice_no'] ?? '') ?></td>

    <td>
        ₹<?= number_format($purchase['total_amount'], 2) ?>
    </td>

    <td>

        <div class="actions">

            <a
                href="view.php?id=<?= $purchase['id'] ?>"
                class="btn"
            >
                View
            </a>

            <a
                href="delete.php?id=<?= $purchase['id'] ?>"
                class="btn btn-danger"
                onclick="return confirm('Delete this purchase?')"
            >
                Delete
            </a>

        </div>

    </td>

</tr>

<?php endforeach; ?>

</table>

</div>

<?php include '../includes/footer.php'; ?>