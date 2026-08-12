<?php

require_once '../config/database.php';

$pageTitle = 'Stock Management';

$search = trim($_GET['search'] ?? '');
$stockStatus = $_GET['stock_status'] ?? '';

$sql = "
    SELECT *
    FROM products
    WHERE status = 1
";

$params = [];

if ($search !== '') {

    $sql .= "
        AND (
            product_name LIKE ?
            OR material LIKE ?
        )
    ";

    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($stockStatus === 'low') {

    $sql .= "
        AND stock_quantity <= minimum_stock
        AND stock_quantity > 0
    ";

} elseif ($stockStatus === 'out') {

    $sql .= "
        AND stock_quantity = 0
    ";

} elseif ($stockStatus === 'available') {

    $sql .= "
        AND stock_quantity > minimum_stock
    ";
}

$sql .= " ORDER BY capacity ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll();

include '../includes/header.php';
?>

<form class="search-form" method="GET">

    <input
        type="text"
        name="search"
        class="form-control"
        placeholder="Search product"
        value="<?= htmlspecialchars($search) ?>"
    >

    <select name="stock_status" class="form-control">

        <option value="">All Stock</option>

        <option
            value="available"
            <?= $stockStatus === 'available' ? 'selected' : '' ?>
        >
            Available
        </option>

        <option
            value="low"
            <?= $stockStatus === 'low' ? 'selected' : '' ?>
        >
            Low Stock
        </option>

        <option
            value="out"
            <?= $stockStatus === 'out' ? 'selected' : '' ?>
        >
            Out of Stock
        </option>

    </select>

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
    <th>Product</th>
    <th>Capacity</th>
    <th>Material</th>
    <th>Current Stock</th>
    <th>Minimum Stock</th>
    <th>Action</th>
</tr>

<?php foreach ($products as $product): ?>

<tr>

    <td><?= htmlspecialchars($product['product_name']) ?></td>

    <td><?= $product['capacity'] ?> L</td>

    <td><?= htmlspecialchars($product['material']) ?></td>

    <td>

        <?php if ($product['stock_quantity'] == 0): ?>

            <span class="badge badge-danger">
                Out of Stock
            </span>

        <?php elseif ($product['stock_quantity'] <= $product['minimum_stock']): ?>

            <span class="badge badge-danger">
                <?= $product['stock_quantity'] ?>
            </span>

        <?php else: ?>

            <span class="badge badge-success">
                <?= $product['stock_quantity'] ?>
            </span>

        <?php endif; ?>

    </td>

    <td><?= $product['minimum_stock'] ?></td>

    <td>

        <div class="actions">

            <a
                href="stock_in.php?id=<?= $product['id'] ?>"
                class="btn btn-success"
            >
                Stock In
            </a>

            <a
                href="stock_out.php?id=<?= $product['id'] ?>"
                class="btn btn-warning"
            >
                Stock Out
            </a>

        </div>

    </td>

</tr>

<?php endforeach; ?>

</table>

</div>

<?php include '../includes/footer.php'; ?>