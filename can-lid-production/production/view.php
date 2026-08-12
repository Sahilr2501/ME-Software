<?php

require_once '../config/database.php';

$id =
    (int)($_GET['id'] ?? 0);


$stmt = $pdo->prepare("

    SELECT
        pr.*,
        p.product_name,
        p.product_type,
        p.capacity

    FROM production pr

    JOIN products p
        ON p.id = pr.product_id

    WHERE pr.id = ?

");

$stmt->execute([$id]);

$production =
    $stmt->fetch();


if (!$production) {

    die('Production not found.');

}


$stmt = $pdo->prepare("

    SELECT
        psr.*,
        ps.stage_number,
        ps.stage_name

    FROM production_stage_records psr

    JOIN production_stages ps
        ON ps.id = psr.stage_id

    WHERE psr.production_id = ?

    ORDER BY ps.stage_number

");

$stmt->execute([$id]);

$records =
    $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Production Details</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">


    <div class="d-flex justify-content-between mb-3">

        <div>

            <h2>
                Production Details
            </h2>

            <div>

                <?= htmlspecialchars(
                    $production['product_name']
                ) ?>

                |

                <?= htmlspecialchars(
                    $production['product_type']
                ) ?>

                |

                <?= htmlspecialchars(
                    $production['capacity']
                ) ?>

            </div>

        </div>


        <div>

            <a
            href="stages.php?id=<?= $id ?>"
            class="btn btn-primary">

                Edit Stages

            </a>


            <a
            href="index.php"
            class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>


    <div class="row g-3 mb-3">


        <div class="col-md-3">

            <div class="card card-body">

                <b>Date</b>

                <?= date(
                    'd-m-Y',
                    strtotime(
                        $production['production_date']
                    )
                ) ?>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card card-body">

                <b>Target</b>

                <?= number_format(
                    $production['target_quantity']
                ) ?>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card card-body">

                <b>Operator</b>

                <?= htmlspecialchars(
                    $production['operator_name'] ?? ''
                ) ?>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card card-body">

                <b>Status</b>

                <?= htmlspecialchars(
                    $production['status']
                ) ?>

            </div>

        </div>

    </div>


    <div class="card">

        <div class="table-responsive">

            <table class="table mb-0">

                <thead class="table-light">

                    <tr>

                        <th>#</th>

                        <th>Stage</th>

                        <th>Input</th>

                        <th>Completed</th>

                        <th>Rejected</th>

                        <th>Pending</th>

                        <th>Remarks</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($records as $record): ?>

                    <tr>

                        <td>
                            <?= $record['stage_number'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $record['stage_name']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $record['input_quantity']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $record['completed_quantity']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $record['rejected_quantity']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $record['pending_quantity']
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $record['remarks'] ?? ''
                            ) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>

</html>