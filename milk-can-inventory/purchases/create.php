 <?php

require_once '../config/database.php';

$pageTitle = 'Add Purchase';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $supplierName = trim($_POST['supplier_name'] ?? '');
    $invoiceNo = trim($_POST['invoice_no'] ?? '');
    $purchaseDate = $_POST['purchase_date'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    $itemNames = $_POST['item_name'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $units = $_POST['unit'] ?? [];
    $rates = $_POST['rate'] ?? [];

    if ($supplierName === '' || $purchaseDate === '') {

        $error = 'Supplier and purchase date are required.';

    } elseif (count($itemNames) === 0) {

        $error = 'Please add at least one purchase item.';

    } else {

        try {

            $pdo->beginTransaction();

            $totalAmount = 0;

            for ($i = 0; $i < count($itemNames); $i++) {

                $qty = (float)$quantities[$i];
                $rate = (float)$rates[$i];

                $totalAmount += $qty * $rate;
            }

            $stmt = $pdo->prepare("
                INSERT INTO purchases
                (
                    supplier_name,
                    invoice_no,
                    purchase_date,
                    total_amount,
                    notes
                )
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $supplierName,
                $invoiceNo,
                $purchaseDate,
                $totalAmount,
                $notes
            ]);

            $purchaseId = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("
                INSERT INTO purchase_items
                (
                    purchase_id,
                    item_name,
                    quantity,
                    unit,
                    rate,
                    amount
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            for ($i = 0; $i < count($itemNames); $i++) {

                $itemName = trim($itemNames[$i]);
                $qty = (float)$quantities[$i];
                $unit = trim($units[$i]);
                $rate = (float)$rates[$i];

                if ($itemName === '' || $qty <= 0) {
                    continue;
                }

                $amount = $qty * $rate;

                $stmtItem->execute([
                    $purchaseId,
                    $itemName,
                    $qty,
                    $unit,
                    $rate,
                    $amount
                ]);
            }

            $pdo->commit();

            header('Location: index.php');
            exit;

        } catch (Exception $e) {

            $pdo->rollBack();

            $error = 'Unable to save purchase.';
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

    <div class="grid-2">

        <div class="form-group">

            <label>Supplier Name *</label>

            <input
                type="text"
                name="supplier_name"
                class="form-control"
                required
            >

        </div>

        <div class="form-group">

            <label>Invoice Number</label>

            <input
                type="text"
                name="invoice_no"
                class="form-control"
            >

        </div>

    </div>

    <div class="form-group">

        <label>Purchase Date *</label>

        <input
            type="date"
            name="purchase_date"
            class="form-control"
            value="<?= date('Y-m-d') ?>"
            required
        >

    </div>

    <h3>Purchase Items</h3>

    <div id="items">

        <div class="grid-2 item-row">

            <div class="form-group">

                <label>Item Name</label>

                <input
                    type="text"
                    name="item_name[]"
                    class="form-control"
                    placeholder="Stainless Steel Sheet"
                    required
                >

            </div>

            <div class="form-group">

                <label>Quantity</label>

                <input
                    type="number"
                    step="0.01"
                    name="quantity[]"
                    class="form-control"
                    required
                >

            </div>

            <div class="form-group">

                <label>Unit</label>

                <input
                    type="text"
                    name="unit[]"
                    class="form-control"
                    value="pcs"
                >

            </div>

            <div class="form-group">

                <label>Rate</label>

                <input
                    type="number"
                    step="0.01"
                    name="rate[]"
                    class="form-control"
                    value="0"
                >

            </div>

        </div>

    </div>

    <button
        type="button"
        class="btn"
        onclick="addItem()"
    >
        + Add Another Item
    </button>

    <br><br>

    <div class="form-group">

        <label>Notes</label>

        <textarea
            name="notes"
            class="form-control"
            rows="4"
        ></textarea>

    </div>

    <button class="btn btn-success">
        Save Purchase
    </button>

    <a href="index.php" class="btn btn-secondary">
        Cancel
    </a>

</form>

</div>

<script>

function addItem() {

    const items = document.getElementById('items');

    const row = document.createElement('div');

    row.className = 'grid-2 item-row';

    row.innerHTML = `
        <div class="form-group">
            <label>Item Name</label>
            <input
                type="text"
                name="item_name[]"
                class="form-control"
                required
            >
        </div>

        <div class="form-group">
            <label>Quantity</label>
            <input
                type="number"
                step="0.01"
                name="quantity[]"
                class="form-control"
                required
            >
        </div>

        <div class="form-group">
            <label>Unit</label>
            <input
                type="text"
                name="unit[]"
                class="form-control"
                value="pcs"
            >
        </div>

        <div class="form-group">
            <label>Rate</label>
            <input
                type="number"
                step="0.01"
                name="rate[]"
                class="form-control"
                value="0"
            >
        </div>
    `;

    items.appendChild(row);
}

</script>

<?php include '../includes/footer.php'; ?>