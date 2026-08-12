<?php

require_once '../config/database.php';

$pageTitle = 'Monthly Production Report';

$month = (int)($_GET['month'] ?? date('m'));
$year = (int)($_GET['year'] ?? date('Y'));

$startDate = sprintf('%04d-%02d-01', $year, $month);

$endDate = date(
    'Y-m-d',
    strtotime($startDate . ' +1 month')
);

$stmt = $pdo->prepare("
    SELECT
        products.product_name,
        products.capacity,
        SUM(production.quantity) AS total_quantity
    FROM production

    INNER JOIN products
        ON products.id = production.product_id

    WHERE production.production_date >= ?
    AND production.production_date < ?

    GROUP BY
        products.id,
        products.product_name,
        products.capacity

    ORDER BY products.capacity
");

$stmt->execute([
    $startDate,
    $endDate
]);

$report = $stmt->fetchAll();

$totalProduction = 0;

foreach ($report as $row) {
    $totalProduction += $row['total_quantity'];
}

include '../includes/header.php';
?>

<div class="table-box">

<form class="search-form" method="GET">

    <select name="month" class="form-control">

        <?php for ($m = 1; $m <= 12; $m++): ?>

            <option
                value="<?= $m ?>"
                <?= $m == $month ? 'selected' : '' ?>
            >
                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
            </option>

        <?php endfor; ?>

    </select>

    <select name="year" class="form-control">

        <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>

            <option
                value="<?= $y ?>"
                <?= $y == $year ? 'selected' : '' ?>
            >
                <?= $y ?>
            </option>

        <?php endfor; ?>

    </select>

    <button class="btn">
        Generate Report
    </button>

</form>

</div>

<div class="cards">

    <div class="card">

        <h3>Total Production</h3>

        <div class="number">
            <?= $totalProduction ?>
        </div>

    </div>

</div>

<div class="table-box">

<h3>
    Production Report -
    <?= date('F', mktime(0, 0, 0, $month, 1)) ?>
    <?= $year ?>
</h3>

<table>

<tr>
    <th>Product</th>
    <th>Capacity</th>
    <th>Total Produced</th>
</tr>

<?php foreach ($report as $row): ?>

<tr>

    <td>
        <?= htmlspecialchars($row['product_name']) ?>
    </td>

    <td>
        <?= $row['capacity'] ?> L
    </td>

    <td>
        <?= $row['total_quantity'] ?>
    </td>

</tr>

<?php endforeach; ?>

<tr>

    <th colspan="2">
        Total
    </th>

    <th>
        <?= $totalProduction ?>
    </th>

</tr>

</table>

</div>

<?php include '../includes/footer.php'; ?>