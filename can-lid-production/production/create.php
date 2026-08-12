<?php

require_once '../config/database.php';

$type =
    $_GET['type']
    ?? $_POST['product_type']
    ?? 'Can';


if (!in_array($type, ['Can', 'Lid'], true)) {

    $type = 'Can';

}


$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $date =
        $_POST['production_date']
        ?? date('Y-m-d');

    $productId =
        (int)($_POST['product_id'] ?? 0);

    $target =
        (int)($_POST['target_quantity'] ?? 0);

    $operator =
        trim($_POST['operator_name'] ?? '');

    $remarks =
        trim($_POST['remarks'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | Product
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE id = ?
        AND status = 1
    ");

    $stmt->execute([$productId]);

    $product = $stmt->fetch();


    if (!$product) {

        $errors[] =
            'Please select a valid product.';

    }


    if ($target <= 0) {

        $errors[] =
            'Target quantity must be greater than 0.';

    }


    /*
    |--------------------------------------------------------------------------
    | Create Production
    |--------------------------------------------------------------------------
    */

    if (!$errors) {

        $pdo->beginTransaction();

        try {

            $stmt = $pdo->prepare("

                INSERT INTO production
                (
                    production_date,
                    product_id,
                    target_quantity,
                    operator_name,
                    status,
                    remarks
                )

                VALUES
                (?, ?, ?, ?, 'Pending', ?)

            ");

            $stmt->execute([
                $date,
                $productId,
                $target,
                $operator,
                $remarks
            ]);


            $productionId =
                $pdo->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | Get Stages
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("

                SELECT *
                FROM production_stages

                WHERE product_type = ?

                AND status = 1

                ORDER BY stage_number

            ");

            $stmt->execute([
                $product['product_type']
            ]);

            $stages =
                $stmt->fetchAll();


            /*
            |--------------------------------------------------------------------------
            | Create Stage Records
            |--------------------------------------------------------------------------
            */

            $insert = $pdo->prepare("

                INSERT INTO production_stage_records
                (
                    production_id,
                    stage_id,
                    input_quantity,
                    completed_quantity,
                    rejected_quantity,
                    pending_quantity,
                    operator_name,
                    production_date
                )

                VALUES
                (?, ?, ?, 0, 0, ?, ?, ?)

            ");


            $firstStage = true;


            foreach ($stages as $stage) {

                $input =
                    $firstStage
                    ? $target
                    : 0;


                $insert->execute([
                    $productionId,
                    $stage['id'],
                    $input,
                    $input,
                    $operator,
                    $date
                ]);


                $firstStage = false;

            }


            $pdo->commit();


            header(
                "Location: stages.php?id="
                . $productionId
            );

            exit;


        } catch (Throwable $e) {

            $pdo->rollBack();

            $errors[] =
                $e->getMessage();

        }

    }

}


/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    SELECT *
    FROM products

    WHERE product_type = ?

    AND status = 1

    ORDER BY capacity, product_name

");

$stmt->execute([$type]);

$products =
    $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>New Production</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="../assets/css/style.css">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">

    <h2>New Production</h2>


    <?php foreach ($errors as $error): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endforeach; ?>


    <form
    method="post"
    class="card card-body">

        <div class="row g-3">


            <div class="col-md-4">

                <label class="form-label">
                    Production Date
                </label>

                <input
                type="date"
                name="production_date"
                class="form-control"
                value="<?= htmlspecialchars(
                    $_POST['production_date']
                    ?? date('Y-m-d')
                ) ?>"
                required>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Product Type
                </label>

                <select
                name="product_type"
                class="form-select"
                onchange="location='create.php?type='+this.value">

                    <option
                    value="Can"
                    <?= $type === 'Can'
                        ? 'selected'
                        : '' ?>>

                        Can

                    </option>

                    <option
                    value="Lid"
                    <?= $type === 'Lid'
                        ? 'selected'
                        : '' ?>>

                        Lid

                    </option>

                </select>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Product
                </label>

                <select
                name="product_id"
                class="form-select"
                required>

                    <option value="">
                        Select Product
                    </option>

                    <?php foreach ($products as $product): ?>

                        <option
                        value="<?= $product['id'] ?>"
                        <?= (
                            ($_POST['product_id'] ?? '')
                            == $product['id']
                        )
                        ? 'selected'
                        : '' ?>>

                            <?= htmlspecialchars(
                                $product['product_name']
                            ) ?>

                            -

                            <?= htmlspecialchars(
                                $product['capacity']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Target Quantity
                </label>

                <input
                type="number"
                min="1"
                name="target_quantity"
                class="form-control"
                required>

            </div>


            <div class="col-md-4">

                <label class="form-label">
                    Operator Name
                </label>

                <input
                type="text"
                name="operator_name"
                class="form-control">

            </div>


            <div class="col-md-12">

                <label class="form-label">
                    Remarks
                </label>

                <textarea
                name="remarks"
                class="form-control"
                rows="3"></textarea>

            </div>

        </div>


        <div class="mt-3">

            <button
            class="btn btn-primary">

                Create Production & Enter Stages

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