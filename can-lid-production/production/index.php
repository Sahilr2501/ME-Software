<?php

require_once '../config/database.php';

$where = [];
$params = [];


/*
|--------------------------------------------------------------------------
| Date Filter
|--------------------------------------------------------------------------
*/

if (!empty($_GET['date'])) {

    $where[] = 'pr.production_date = ?';

    $params[] = $_GET['date'];

}


/*
|--------------------------------------------------------------------------
| Type Filter
|--------------------------------------------------------------------------
*/

if (!empty($_GET['type'])) {

    $where[] = 'p.product_type = ?';

    $params[] = $_GET['type'];

}


/*
|--------------------------------------------------------------------------
| Status Filter
|--------------------------------------------------------------------------
*/

if (!empty($_GET['status'])) {

    $where[] = 'pr.status = ?';

    $params[] = $_GET['status'];

}


$sql = "

SELECT

    pr.*,

    p.product_name,

    p.product_type,

    p.capacity,

    COALESCE(

        (

            SELECT psr.completed_quantity

            FROM production_stage_records psr

            JOIN production_stages ps
                ON ps.id = psr.stage_id

            WHERE psr.production_id = pr.id

            ORDER BY ps.stage_number DESC

            LIMIT 1

        ),

        0

    ) AS finished_quantity


FROM production pr

JOIN products p
    ON p.id = pr.product_id

";


if ($where) {

    $sql .= " WHERE " .
        implode(' AND ', $where);

}


$sql .= "

ORDER BY
    pr.production_date DESC,
    pr.id DESC

";


$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$rows = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Production History</title>

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

    <div
    class="d-flex justify-content-between align-items-center mb-3">

        <h2>Production History</h2>

        <a
        href="create.php"
        class="btn btn-primary">

            + New Production

        </a>

    </div>


    <!-- FILTER -->

    <form
    class="card card-body mb-3"
    method="get">

        <div class="row g-2">

            <div class="col-md-3">

                <label class="form-label">
                    Date
                </label>

                <input
                type="date"
                name="date"
                class="form-control"
                value="<?= htmlspecialchars(
                    $_GET['date'] ?? ''
                ) ?>">

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Product Type
                </label>

                <select
                name="type"
                class="form-select">

                    <option value="">
                        All
                    </option>

                    <option
                    value="Can"
                    <?= (($_GET['type'] ?? '') === 'Can')
                        ? 'selected'
                        : '' ?>>

                        Can

                    </option>

                    <option
                    value="Lid"
                    <?= (($_GET['type'] ?? '') === 'Lid')
                        ? 'selected'
                        : '' ?>>

                        Lid

                    </option>

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Status
                </label>

                <select
                name="status"
                class="form-select">

                    <option value="">
                        All
                    </option>

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
                    value="<?= $status ?>"
                    <?= (($_GET['status'] ?? '') === $status)
                        ? 'selected'
                        : '' ?>>

                        <?= $status ?>

                    </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div
            class="col-md-3 d-flex align-items-end">

                <button
                class="btn btn-dark w-100">

                    Filter

                </button>

            </div>

        </div>

    </form>


    <!-- TABLE -->

    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>ID</th>

                            <th>Date</th>

                            <th>Product</th>

                            <th>Capacity</th>

                            <th>Target</th>

                            <th>Finished</th>

                            <th>Status</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($rows as $row): ?>

                        <tr>

                            <td>
                                <?= $row['id'] ?>
                            </td>

                            <td>
                                <?= date(
                                    'd-m-Y',
                                    strtotime(
                                        $row['production_date']
                                    )
                                ) ?>
                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row['product_name']
                                ) ?>

                                <span class="badge bg-secondary">

                                    <?= htmlspecialchars(
                                        $row['product_type']
                                    ) ?>

                                </span>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['capacity']
                                ) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    $row['target_quantity']
                                ) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    $row['finished_quantity']
                                ) ?>
                            </td>

                            <td>

                                <span
                                class="badge bg-<?=

                                    $row['status'] === 'Completed'
                                    ? 'success'
                                    : (
                                        $row['status'] === 'In Progress'
                                        ? 'warning text-dark'
                                        : 'secondary'
                                    )

                                ?>">

                                    <?= htmlspecialchars(
                                        $row['status']
                                    ) ?>

                                </span>

                            </td>

                            <td class="text-nowrap">

                                <a
                                class="btn btn-sm btn-outline-primary"
                                href="view.php?id=<?= $row['id'] ?>">

                                    View

                                </a>


                                <a
                                class="btn btn-sm btn-outline-secondary"
                                href="edit.php?id=<?= $row['id'] ?>">

                                    Edit

                                </a>


                                <a
                                class="btn btn-sm btn-outline-danger"
                                href="delete.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Delete this production record?')">

                                    Delete

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>


                    <?php if (!$rows): ?>

                        <tr>

                            <td
                            colspan="8"
                            class="text-center py-4">

                                No production records found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>

</html>