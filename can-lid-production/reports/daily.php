<?php

require_once '../config/database.php';

$date =
    $_GET['date']
    ?? date('Y-m-d');


$stmt = $pdo->prepare("

SELECT

    p.product_type,

    SUM(
        pr.target_quantity
    ) AS target,

    COALESCE(
        SUM(
            CASE
                WHEN ps.stage_number = (
                    SELECT MAX(stage_number)
                    FROM production_stages
                    WHERE product_type =
                        p.product_type
                )

                THEN psr.completed_quantity

                ELSE 0

            END
        ),
        0
    ) AS finished,

    COALESCE(
        SUM(
            psr.rejected_quantity
        ),
        0
    ) AS rejected


FROM production pr

JOIN products p
    ON p.id = pr.product_id

LEFT JOIN production_stage_records psr
    ON psr.production_id = pr.id

LEFT JOIN production_stages ps
    ON ps.id = psr.stage_id


WHERE pr.production_date = ?


GROUP BY
    p.product_type

");


$stmt->execute([$date]);

$rows =
    $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Daily Report</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">


    <div
    class="d-flex justify-content-between">

        <h2>
            Daily Report
        </h2>


        <a
        href="export_excel.php?date=<?= urlencode($date) ?>"
        class="btn btn-success">

            Export Excel

        </a>

    </div>


    <form
    class="card card-body my-3">

        <label class="form-label">
            Date
        </label>


        <div class="d-flex gap-2">

            <input
            type="date"
            name="date"
            class="form-control"
            value="<?= htmlspecialchars($date) ?>">


            <button
            class="btn btn-dark">

                View

            </button>

        </div>

    </form>


    <div class="card">

        <div class="table-responsive">

            <table class="table mb-0">

                <thead class="table-light">

                    <tr>

                        <th>Product</th>

                        <th>Target</th>

                        <th>Finished</th>

                        <th>Rejected</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($rows as $row): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                $row['product_type']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $row['target']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $row['finished']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $row['rejected']
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