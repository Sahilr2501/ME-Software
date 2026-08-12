<?php

require_once '../config/database.php';

$id =
    (int)($_GET['id'] ?? 0);


$stmt = $pdo->prepare("

    SELECT
        pr.*,
        p.product_type

    FROM production pr

    JOIN products p
        ON p.id = pr.product_id

    WHERE pr.id = ?

");

$stmt->execute([$id]);

$row =
    $stmt->fetch();


if (!$row) {

    die('Production not found.');

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $date =
        $_POST['production_date'];

    $target =
        (int)$_POST['target_quantity'];

    $operator =
        trim($_POST['operator_name']);

    $status =
        $_POST['status'];

    $remarks =
        trim($_POST['remarks']);


    $stmt = $pdo->prepare("

        UPDATE production

        SET
            production_date = ?,
            target_quantity = ?,
            operator_name = ?,
            status = ?,
            remarks = ?

        WHERE id = ?

    ");


    $stmt->execute([

        $date,
        $target,
        $operator,
        $status,
        $remarks,
        $id

    ]);


    header(
        "Location: stages.php?id="
        . $id
    );

    exit;

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Edit Production</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">

    <h2>Edit Production</h2>


    <form
    method="post"
    class="card card-body">

        <div class="row g-3">


            <div class="col-md-4">

                <label class="form-label">
                    Date
                </label>

                <input
                type="date"
                class="form-control"
                name="production_date"
                value="<?= htmlspecialchars(
                    $row['production_date']
                ) ?>"
                required>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Target Quantity
                </label>

                <input
                type="number"
                min="1"
                class="form-control"
                name="target_quantity"
                value="<?= $row['target_quantity'] ?>"
                required>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Operator
                </label>

                <input
                class="form-control"
                name="operator_name"
                value="<?= htmlspecialchars(
                    $row['operator_name'] ?? ''
                ) ?>">

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Status
                </label>

                <select
                name="status"
                class="form-select">

                    <?php

                    $statuses = [
                        'Pending',
                        'In Progress',
                        'Completed',
                        'Hold',
                        'Cancelled'
                    ];

                    foreach ($statuses as $status):

                    ?>

                    <option
                    <?= $row['status'] === $status
                        ? 'selected'
                        : '' ?>>

                        <?= $status ?>

                    </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-12">

                <label class="form-label">
                    Remarks
                </label>

                <textarea
                class="form-control"
                name="remarks"
                rows="3"><?= htmlspecialchars(
                    $row['remarks'] ?? ''
                ) ?></textarea>

            </div>

        </div>


        <div class="mt-3">

            <button
            class="btn btn-primary">

                Save

            </button>


            <a
            href="index.php"
            class="btn btn-secondary ms-2">

                Cancel

            </a>

        </div>

    </form>

</div>

</body>

</html>