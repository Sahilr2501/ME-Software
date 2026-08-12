<?php

require_once '../config/database.php';

$pageTitle = 'Production Records';

$search = trim($_GET['search'] ?? '');
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

$sql = "
    SELECT
        production.*,
        products.product_name,
        products.capacity
    FROM production
    INNER JOIN products
        ON products.id = production.product_id
    WHERE 1=1
";

$params = [];

if ($search !== '') {

    $sql .= "
        AND products.product_name LIKE ?
    ";

    $params[] = "%$search%";
}

if ($fromDate !== '') {

    $sql .= " AND production.production_date >= ?";

    $params[] = $fromDate;
}

if ($toDate !== '') {

    $sql .= " AND production.production_date <= ?";

    $params[] = $toDate;
}

$sql .= " ORDER BY production.production_date DESC, production.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$records = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="actions" style="margin-bottom:20px;">

    <a href="create.php" class="btn btn-success">
        + Add Production
    </a>

</div>

<form class="search-form" method="GET">

    <input
        type="text"
        name="search"
        class="form-control"
        placeholder="Product name"
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
    <th>Product</th>
    <th>Capacity</th>
    <th>Quantity</th>
    <th>Notes</th>
    <th>Action</th>
</tr>

<?php foreach ($records as $row): ?>

<tr>

    <td><?= htmlspecialchars($row['production_date']) ?></td>

    <td><?= htmlspecialchars($row['product_name']) ?></td>

    <td><?= $row['capacity'] ?> L</td>

    <td><?= $row['quantity'] ?></td>

    <td><?= htmlspecialchars($row['notes'] ?? '') ?></td>

    <td>

        <a
            href="delete.php?id=<?= $row['id'] ?>"
            class="btn btn-danger"
            onclick="return confirm('Delete this production record? Stock will also be reversed.')"
        >
            Delete
        </a>

    </td>

</tr>

<?php endforeach; ?>

</table>

</div>

<?php include '../includes/footer.php'; ?>