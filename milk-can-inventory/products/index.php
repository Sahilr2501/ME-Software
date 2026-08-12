<?php

require_once '../config/database.php';

$pageTitle = 'Products';

$search = trim($_GET['search'] ?? '');
$capacity = $_GET['capacity'] ?? '';

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

if ($capacity !== '') {
    $sql .= " AND capacity = ?";
    $params[] = $capacity;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="actions" style="margin-bottom:20px;">
    <a href="create.php" class="btn btn-success">
        + Add Product
    </a>
</div>

<form class="search-form" method="GET">

    <input
        type="text"
        name="search"
        class="form-control"
        placeholder="Search product/material"
        value="<?= htmlspecialchars($search) ?>"
    >

    <select name="capacity" class="form-control">

        <option value="">All Capacities</option>

        <option value="20" <?= $capacity == '20' ? 'selected' : '' ?>>
            20 L
        </option>

        <option value="30" <?= $capacity == '30' ? 'selected' : '' ?>>
            30 L
        </option>

        <option value="40" <?= $capacity == '40' ? 'selected' : '' ?>>
            40 L
        </option>

        <option value="50" <?= $capacity == '50' ? 'selected' : '' ?>>
            50 L
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
    <th>Stock</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php foreach ($products as $product): ?>

<tr>

    <td><?= htmlspecialchars($product['product_name']) ?></td>

    <td><?= $product['capacity'] ?> L</td>

    <td><?= htmlspecialchars($product['material']) ?></td>

    <td>

        <?php if ($product['stock_quantity'] <= $product['minimum_stock']): ?>

            <span class="badge badge-danger">
                <?= $product['stock_quantity'] ?>
            </span>

        <?php else: ?>

            <span class="badge badge-success">
                <?= $product['stock_quantity'] ?>
            </span>

        <?php endif; ?>

    </td>

    <td>Active</td>

    <td>

        <div class="actions">

            <a
                href="edit.php?id=<?= $product['id'] ?>"
                class="btn btn-warning"
            >
                Edit
            </a>

            <a
                href="delete.php?id=<?= $product['id'] ?>"
                class="btn btn-danger"
                onclick="return confirm('Delete this product?')"
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