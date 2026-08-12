<?php

require_once '../config/database.php';

$pageTitle = 'Sales Records';

$search = trim($_GET['search'] ?? '');
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

$sql = "
    SELECT
        sales.*,
        GROUP_CONCAT(
            CONCAT(
                products.product_name,
                ' - ',
                products.capacity,
                'L x ',
                sale_items.quantity
            )
            SEPARATOR ', '
        ) AS products
    FROM sales

    INNER JOIN sale_items
        ON sale_items.sale_id = sales.id

    INNER JOIN products
        ON products.id = sale_items.product_id

    WHERE 1=1
";

$params = [];

if ($search !== '') {

    $sql .= "
        AND (
            sales.customer_name LIKE ?
            OR products.product_name LIKE ?
        )
    ";

    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($fromDate !== '') {

    $sql .= " AND sales.sale_date >= ?";

    $params[] = $fromDate;
}

if ($toDate !== '') {

    $sql .= " AND sales.sale_date <= ?";

    $params[] = $toDate;
}

$sql .= "
    GROUP BY sales.id
    ORDER BY sales.sale_date DESC, sales.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$sales = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="actions" style="margin-bottom:20px;">

    <a href="create.php" class="btn btn-success">
        + New Sale
    </a>

</div>

<form class="search-form" method="GET">

    <input
        type="text"
        name="search"
        class="form-control"
        placeholder="Customer / Product"
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
    <th>Customer</th>
    <th>Products</th>
    <th>Total</th>
    <th>Actions</th>
</tr>

<?php foreach ($sales as $sale): ?>

<tr>

    <td>
        <?= htmlspecialchars($sale['sale_date']) ?>
    </td>

    <td>
        <?= htmlspecialchars(
            $sale['customer_name'] ?: 'Walk-in Customer'
        ) ?>
    </td>

    <td>
        <?= htmlspecialchars($sale['products']) ?>
    </td>

    <td>
        ₹<?= number_format($sale['total_amount'], 2) ?>
    </td>

    <td>

        <div class="actions">

            <a
                href="view.php?id=<?= $sale['id'] ?>"
                class="btn"
            >
                View
            </a>

            <a
                href="delete.php?id=<?= $sale['id'] ?>"
                class="btn btn-danger"
                onclick="return confirm('Delete this sale? Stock will be restored.')"
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