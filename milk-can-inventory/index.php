<?php

require_once 'config/database.php';

$pageTitle = 'Dashboard';

$totalProducts = $pdo
    ->query("SELECT COUNT(*) FROM products WHERE status = 1")
    ->fetchColumn();

$totalStock = $pdo
    ->query("SELECT COALESCE(SUM(stock_quantity), 0) FROM products WHERE status = 1")
    ->fetchColumn();

$lowStock = $pdo
    ->query("
        SELECT COUNT(*)
        FROM products
        WHERE status = 1
        AND stock_quantity <= minimum_stock
    ")
    ->fetchColumn();

$currentMonthProduction = $pdo
    ->query("
        SELECT COALESCE(SUM(quantity), 0)
        FROM production
        WHERE MONTH(production_date) = MONTH(CURDATE())
        AND YEAR(production_date) = YEAR(CURDATE())
    ")
    ->fetchColumn();

$recentProduction = $pdo->query("
    SELECT
        production.*,
        products.product_name,
        products.capacity
    FROM production
    INNER JOIN products
        ON products.id = production.product_id
    ORDER BY production.id DESC
    LIMIT 5
")->fetchAll();

$recentPurchases = $pdo->query("
    SELECT *
    FROM purchases
    ORDER BY id DESC
    LIMIT 5
")->fetchAll();

include 'includes/header.php';
?>

<div class="cards">

    <div class="card">
        <h3>Total Products</h3>
        <div class="number"><?= $totalProducts ?></div>
    </div>

    <div class="card">
        <h3>Current Stock</h3>
        <div class="number"><?= $totalStock ?></div>
    </div>

    <div class="card">
        <h3>This Month Production</h3>
        <div class="number"><?= $currentMonthProduction ?></div>
    </div>

    <div class="card">
        <h3>Low Stock Products</h3>
        <div class="number text-danger"><?= $lowStock ?></div>
    </div>

</div>

<div class="table-box">

    <h3>Recent Production</h3>

    <table>

        <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Capacity</th>
            <th>Quantity</th>
        </tr>

        <?php foreach ($recentProduction as $row): ?>

        <tr>
            <td><?= htmlspecialchars($row['production_date']) ?></td>

            <td>
                <?= htmlspecialchars($row['product_name']) ?>
            </td>

            <td>
                <?= htmlspecialchars($row['capacity']) ?> L
            </td>

            <td>
                <?= htmlspecialchars($row['quantity']) ?>
            </td>
        </tr>

        <?php endforeach; ?>

    </table>

</div>

<div class="table-box">

    <h3>Recent Purchases</h3>

    <table>

        <tr>
            <th>Date</th>
            <th>Supplier</th>
            <th>Invoice</th>
            <th>Total</th>
        </tr>

        <?php foreach ($recentPurchases as $row): ?>

        <tr>
            <td><?= htmlspecialchars($row['purchase_date']) ?></td>

            <td><?= htmlspecialchars($row['supplier_name']) ?></td>

            <td><?= htmlspecialchars($row['invoice_no'] ?? '') ?></td>

            <td>
                ₹<?= number_format($row['total_amount'], 2) ?>
            </td>
        </tr>

        <?php endforeach; ?>

    </table>

</div>

<?php include 'includes/footer.php'; ?>