<?php

require_once '../config/database.php';


$date =
    $_GET['date']
    ?? date('Y-m-d');


$type =
    $_GET['type']
    ?? '';


$where = [
    'psr.production_date = ?'
];


$params = [
    $date
];


if ($type) {

    $where[] =
        'ps.product_type = ?';

    $params[] =
        $type;

}


$stmt = $pdo->prepare("

SELECT

    ps.product_type,

    ps.stage_number,

    ps.stage_name,

    SUM(
        psr.input_quantity
    ) AS input_qty,

    SUM(
        psr.completed_quantity
    ) AS completed_qty,

    SUM(
        psr.rejected_quantity
    ) AS rejected_qty,

    SUM(
        psr.pending_quantity
    ) AS pending_qty


FROM production_stage_records psr

JOIN production_stages ps
    ON ps.id = psr.stage_id


WHERE

    " . implode(
        ' AND ',
        $where
    ) . "


GROUP BY

    ps.product_type,
    ps.stage_number,
    ps.stage_name


ORDER BY

    ps.product_type,
    ps.stage_number

");


$stmt->execute($params);

$rows =
    $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Stage Report</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">

    <h2>
        Stage Report
    </h2>


    <form
    class="card card-body my-3">

        <div class="row g-2">


            <div class="col-md-4">

                <label>
                    Date
                </label>

                <input
                type="date"
                name="date"
                class="form-control"
                value="<?= htmlspecialchars($date) ?>">

            </div>


            <div class="col-md-4">

                <label>
                    Type
                </label>

                <select
                name="type"
                class="form-select">

                    <option value="">
                        All
                    </option>

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


            <div class="col-md-4 d-flex align-items-end">

                <button
                class="btn btn-dark w-100">

                    View

                </button>

            </div>

        </div>

    </form>


    <div class="card">

        <div class="table-responsive">

            <table class="table mb-0">

                <thead class="table-light">

                    <tr>

                        <th>Type</th>

                        <th>#</th>

                        <th>Stage</th>

                        <th>Input</th>

                        <th>Completed</th>

                        <th>Rejected</th>

                        <th>Pending</th>

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
                            <?= $row['stage_number'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['stage_name']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $row['input_qty']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $row['completed_qty']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $row['rejected_qty']
                            ) ?>
                        </td>

                        <td>
                            <?= number_format(
                                $row['pending_qty']
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