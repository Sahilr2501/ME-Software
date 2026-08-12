<?php

require_once '../config/database.php';

$today = date('Y-m-d');

/*
|--------------------------------------------------------------------------
| Today's Target
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        COALESCE(SUM(
            CASE
                WHEN p.product_type = 'Can'
                THEN pr.target_quantity
                ELSE 0
            END
        ), 0) AS can_target,

        COALESCE(SUM(
            CASE
                WHEN p.product_type = 'Lid'
                THEN pr.target_quantity
                ELSE 0
            END
        ), 0) AS lid_target

    FROM production pr

    JOIN products p
        ON p.id = pr.product_id

    WHERE pr.production_date = ?
");

$stmt->execute([$today]);

$targets = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Finished Quantity
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        p.product_type,

        COALESCE(
            SUM(
                CASE
                    WHEN ps.stage_number = (
                        SELECT MAX(stage_number)
                        FROM production_stages
                        WHERE product_type = p.product_type
                    )
                    THEN psr.completed_quantity
                    ELSE 0
                END
            ),
            0
        ) AS finished

    FROM production pr

    JOIN products p
        ON p.id = pr.product_id

    LEFT JOIN production_stage_records psr
        ON psr.production_id = pr.id

    LEFT JOIN production_stages ps
        ON ps.id = psr.stage_id

    WHERE pr.production_date = ?

    GROUP BY p.product_type
");

$stmt->execute([$today]);

$finished = [
    'Can' => 0,
    'Lid' => 0
];

foreach ($stmt->fetchAll() as $row) {

    $finished[$row['product_type']] =
        (int)$row['finished'];

}


/*
|--------------------------------------------------------------------------
| Rejected
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(rejected_quantity), 0) AS rejected

    FROM production_stage_records

    WHERE production_date = ?
");

$stmt->execute([$today]);

$rejected =
    (int)$stmt->fetch()['rejected'];


/*
|--------------------------------------------------------------------------
| Production Records
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total

    FROM production

    WHERE production_date = ?
");

$stmt->execute([$today]);

$totalProductions =
    (int)$stmt->fetch()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Dashboard - Can & Lid Production</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
      href="../assets/css/style.css">

</head>

<body>

<?php include '../assets/navbar.php'; ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>Production Dashboard</h2>

            <div class="text-muted">

                <?= date('d-m-Y') ?>

            </div>

        </div>

        <a href="../production/create.php"
           class="btn btn-primary">

            + New Production

        </a>

    </div>


    <div class="row g-3">

        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Can Target
                    </div>

                    <h3>
                        <?= number_format(
                            (int)$targets['can_target']
                        ) ?>
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Lid Target
                    </div>

                    <h3>
                        <?= number_format(
                            (int)$targets['lid_target']
                        ) ?>
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Finished Can
                    </div>

                    <h3>
                        <?= number_format(
                            $finished['Can']
                        ) ?>
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Finished Lid
                    </div>

                    <h3>
                        <?= number_format(
                            $finished['Lid']
                        ) ?>
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Today's Records
                    </div>

                    <h3>
                        <?= number_format(
                            $totalProductions
                        ) ?>
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Today's Rejected
                    </div>

                    <h3>
                        <?= number_format($rejected) ?>
                    </h3>

                </div>

            </div>

        </div>

    </div>


    <div class="card mt-4">

        <div class="card-body">

            <h5>Quick Actions</h5>

            <div class="d-flex gap-2 flex-wrap">

                <a
                class="btn btn-outline-primary"
                href="../production/create.php?type=Can">

                    New Can Production

                </a>


                <a
                class="btn btn-outline-secondary"
                href="../production/create.php?type=Lid">

                    New Lid Production

                </a>


                <a
                class="btn btn-outline-dark"
                href="../production/index.php">

                    Production History

                </a>


                <a
                class="btn btn-outline-success"
                href="../reports/daily.php">

                    Daily Report

                </a>


                <a
                class="btn btn-outline-info"
                href="../products/index.php">

                    Products

                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>