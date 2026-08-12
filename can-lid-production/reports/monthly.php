<?php

require_once '../config/database.php';


/*
|--------------------------------------------------------------------------
| Selected Month
|--------------------------------------------------------------------------
*/

$month = $_GET['month'] ?? date('Y-m');


/*
|--------------------------------------------------------------------------
| Validate Month
|--------------------------------------------------------------------------
*/

if (!preg_match('/^\d{4}-\d{2}$/', $month)) {

    $month = date('Y-m');

}


/*
|--------------------------------------------------------------------------
| Start & End Date
|--------------------------------------------------------------------------
*/

$start = $month . '-01';

$end = date(
    'Y-m-t',
    strtotime($start)
);


/*
|--------------------------------------------------------------------------
| Monthly Production Query
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

    SELECT

        p.product_type,

        COALESCE(
            SUM(pr.target_quantity),
            0
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

    INNER JOIN products p
        ON p.id = pr.product_id

    LEFT JOIN production_stage_records psr
        ON psr.production_id = pr.id

    LEFT JOIN production_stages ps
        ON ps.id = psr.stage_id

    WHERE

        pr.production_date
        BETWEEN ?
        AND ?

    GROUP BY

        p.product_type

    ORDER BY

        p.product_type

");


$stmt->execute([
    $start,
    $end
]);


$rows = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Calculate Totals
|--------------------------------------------------------------------------
*/

$totalTarget = 0;

$totalFinished = 0;

$totalRejected = 0;


foreach ($rows as $row) {

    $totalTarget += (float) $row['target'];

    $totalFinished += (float) $row['finished'];

    $totalRejected += (float) $row['rejected'];

}


/*
|--------------------------------------------------------------------------
| Completion Percentage
|--------------------------------------------------------------------------
*/

$completionPercentage = 0;

if ($totalTarget > 0) {

    $completionPercentage =
        ($totalFinished / $totalTarget) * 100;

}


/*
|--------------------------------------------------------------------------
| Rejection Percentage
|--------------------------------------------------------------------------
*/

$rejectionPercentage = 0;

if ($totalFinished > 0) {

    $rejectionPercentage =
        ($totalRejected / $totalFinished) * 100;

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Monthly Production Report
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <style>

        body {
            background: #f5f6fa;
        }

        .report-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .summary-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            min-height: 140px;
        }

        .summary-title {
            font-size: 15px;
        }

        .summary-number {
            font-size: 30px;
            font-weight: bold;
            margin-top: 8px;
        }

        .summary-small {
            font-size: 13px;
            opacity: 0.9;
        }

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .report-card {
                box-shadow: none;
            }

        }

    </style>

</head>


<body>


<?php

/*
|--------------------------------------------------------------------------
| Navbar
|--------------------------------------------------------------------------
|
| If you have assets/navbar.php, it will be loaded.
|
*/

if (file_exists('../assets/navbar.php')) {

    include '../assets/navbar.php';

}

?>


<div class="container py-4">


    <!-- Header -->

    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >

        <div>

            <h2 class="mb-1">
                Monthly Production Report
            </h2>

            <p class="text-muted mb-0">

                <?= htmlspecialchars(
                    date('F Y', strtotime($start))
                ) ?>

            </p>

        </div>


        <div class="no-print">

            <button
                type="button"
                onclick="window.print()"
                class="btn btn-secondary"
            >
                Print
            </button>

        </div>

    </div>



    <!-- Month Selection -->

    <form
        method="GET"
        class="card report-card card-body mb-4 no-print"
    >

        <label class="form-label fw-bold">

            Select Month

        </label>


        <div class="row g-2">

            <div class="col-md-5">

                <input
                    type="month"
                    name="month"
                    class="form-control"
                    value="<?= htmlspecialchars($month) ?>"
                    required
                >

            </div>


            <div class="col-md-auto">

                <button
                    type="submit"
                    class="btn btn-dark"
                >

                    View Report

                </button>

            </div>


            <div class="col-md-auto">

                <a
                    href="export_excel.php?month=<?= urlencode($month) ?>"
                    class="btn btn-success"
                >

                    Download Excel

                </a>

            </div>

        </div>

    </form>



    <!-- Summary Cards -->

    <div class="row g-3 mb-4">


        <!-- Target -->

        <div class="col-md-3">

            <div class="summary-card bg-primary">

                <div class="summary-title">

                    Total Target

                </div>


                <div class="summary-number">

                    <?= number_format($totalTarget) ?>

                </div>


                <div class="summary-small">

                    Units

                </div>

            </div>

        </div>



        <!-- Finished -->

        <div class="col-md-3">

            <div class="summary-card bg-success">

                <div class="summary-title">

                    Total Finished

                </div>


                <div class="summary-number">

                    <?= number_format($totalFinished) ?>

                </div>


                <div class="summary-small">

                    Units

                </div>

            </div>

        </div>



        <!-- Rejected -->

        <div class="col-md-3">

            <div class="summary-card bg-danger">

                <div class="summary-title">

                    Total Rejected

                </div>


                <div class="summary-number">

                    <?= number_format($totalRejected) ?>

                </div>


                <div class="summary-small">

                    Units

                </div>

            </div>

        </div>



        <!-- Completion -->

        <div class="col-md-3">

            <div class="summary-card bg-dark">

                <div class="summary-title">

                    Completion

                </div>


                <div class="summary-number">

                    <?= number_format(
                        $completionPercentage,
                        2
                    ) ?>%

                </div>


                <div class="summary-small">

                    Rejection:
                    <?= number_format(
                        $rejectionPercentage,
                        2
                    ) ?>%

                </div>

            </div>

        </div>

    </div>



    <!-- Monthly Table -->

    <div class="card report-card">


        <div class="card-header bg-white">

            <div
                class="d-flex justify-content-between align-items-center"
            >

                <h5 class="mb-0">

                    Monthly Production

                </h5>


                <span class="text-muted">

                    <?= htmlspecialchars(
                        date('F Y', strtotime($start))
                    ) ?>

                </span>

            </div>

        </div>



        <div class="table-responsive">


            <table
                class="table table-bordered table-hover mb-0"
            >

                <thead class="table-dark">

                    <tr>

                        <th width="60">
                            #
                        </th>

                        <th>
                            Product
                        </th>

                        <th class="text-end">
                            Target
                        </th>

                        <th class="text-end">
                            Finished
                        </th>

                        <th class="text-end">
                            Rejected
                        </th>

                        <th class="text-end">
                            Pending
                        </th>

                        <th class="text-end">
                            Completion %
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if (count($rows) > 0): ?>


                    <?php foreach (
                        $rows as $index => $row
                    ): ?>


                        <?php

                        $target =
                            (float) $row['target'];

                        $finished =
                            (float) $row['finished'];

                        $rejected =
                            (float) $row['rejected'];

                        $pending =
                            max(
                                0,
                                $target - $finished
                            );


                        $percentage = 0;

                        if ($target > 0) {

                            $percentage =
                                ($finished / $target)
                                * 100;

                        }

                        ?>


                        <tr>


                            <td>

                                <?= $index + 1 ?>

                            </td>


                            <td>

                                <?php

                                $type =
                                    strtolower(
                                        $row['product_type']
                                    );

                                ?>


                                <?php if ($type === 'can'): ?>

                                    <span
                                        class="badge bg-primary"
                                    >
                                        Can
                                    </span>

                                <?php elseif (
                                    $type === 'lid'
                                ): ?>

                                    <span
                                        class="badge bg-warning text-dark"
                                    >
                                        Lid
                                    </span>

                                <?php else: ?>

                                    <?= htmlspecialchars(
                                        $row['product_type']
                                    ) ?>

                                <?php endif; ?>

                            </td>


                            <td class="text-end">

                                <?= number_format(
                                    $target
                                ) ?>

                            </td>


                            <td
                                class="text-end fw-bold text-success"
                            >

                                <?= number_format(
                                    $finished
                                ) ?>

                            </td>


                            <td
                                class="text-end text-danger"
                            >

                                <?= number_format(
                                    $rejected
                                ) ?>

                            </td>


                            <td class="text-end">

                                <?= number_format(
                                    $pending
                                ) ?>

                            </td>


                            <td class="text-end">

                                <?= number_format(
                                    $percentage,
                                    2
                                ) ?>%

                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-5"
                        >

                            <div
                                class="text-muted"
                            >

                                No production data found
                                for this month.

                            </div>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>



                <?php if (count($rows) > 0): ?>


                    <tfoot class="table-light">

                        <tr>

                            <th
                                colspan="2"
                                class="text-end"
                            >

                                TOTAL

                            </th>


                            <th class="text-end">

                                <?= number_format(
                                    $totalTarget
                                ) ?>

                            </th>


                            <th
                                class="text-end text-success"
                            >

                                <?= number_format(
                                    $totalFinished
                                ) ?>

                            </th>


                            <th
                                class="text-end text-danger"
                            >

                                <?= number_format(
                                    $totalRejected
                                ) ?>

                            </th>


                            <th class="text-end">

                                <?= number_format(
                                    max(
                                        0,
                                        $totalTarget
                                        -
                                        $totalFinished
                                    )
                                ) ?>

                            </th>


                            <th class="text-end">

                                <?= number_format(
                                    $completionPercentage,
                                    2
                                ) ?>%

                            </th>

                        </tr>

                    </tfoot>


                <?php endif; ?>


            </table>

        </div>

    </div>



    <!-- Report Information -->

    <div class="card report-card mt-4">


        <div class="card-body">


            <h5>
                Report Information
            </h5>


            <div class="row">


                <div class="col-md-4">

                    <strong>
                        Month:
                    </strong>

                    <?= htmlspecialchars(
                        date(
                            'F Y',
                            strtotime($start)
                        )
                    ) ?>

                </div>


                <div class="col-md-4">

                    <strong>
                        From:
                    </strong>

                    <?= date(
                        'd-m-Y',
                        strtotime($start)
                    ) ?>

                </div>


                <div class="col-md-4">

                    <strong>
                        To:
                    </strong>

                    <?= date(
                        'd-m-Y',
                        strtotime($end)
                    ) ?>

                </div>

            </div>

        </div>

    </div>


</div>


</body>

</html>