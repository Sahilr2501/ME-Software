<?php

require_once '../config/database.php';

$id =
    (int)(
        $_GET['id']
        ?? $_POST['production_id']
        ?? 0
    );


if ($id <= 0) {

    die('Invalid production ID.');

}


/*
|--------------------------------------------------------------------------
| Production
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Stage Records
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $completed =
        $_POST['completed'] ?? [];

    $rejected =
        $_POST['rejected'] ?? [];

    $remarks =
        $_POST['remarks'] ?? [];

    $operator =
        trim($_POST['operator_name'] ?? '');


    $pdo->beginTransaction();

    try {

        $available =
            (int)$production['target_quantity'];


        foreach ($records as $record) {

            $recordId =
                (int)$record['id'];


            $complete =
                max(
                    0,
                    (int)(
                        $completed[$recordId] ?? 0
                    )
                );


            $reject =
                max(
                    0,
                    (int)(
                        $rejected[$recordId] ?? 0
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */

            if (
                $complete + $reject
                > $available
            ) {

                throw new Exception(
                    "Stage "
                    . $record['stage_number']
                    . " - "
                    . $record['stage_name']
                    . ": Completed + Rejected cannot exceed "
                    . $available
                );

            }


            $pending =
                $available
                - $complete
                - $reject;


            /*
            |--------------------------------------------------------------------------
            | Update
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("

                UPDATE production_stage_records

                SET

                    input_quantity = ?,

                    completed_quantity = ?,

                    rejected_quantity = ?,

                    pending_quantity = ?,

                    operator_name = ?,

                    remarks = ?

                WHERE id = ?

                AND production_id = ?

            ");


            $stmt->execute([

                $available,

                $complete,

                $reject,

                $pending,

                $operator,

                trim(
                    $remarks[$recordId] ?? ''
                ),

                $recordId,

                $id

            ]);


            /*
            |--------------------------------------------------------------------------
            | Next Stage Input
            |--------------------------------------------------------------------------
            */

            $available =
                $complete;

        }


        /*
        |--------------------------------------------------------------------------
        | Production Status
        |--------------------------------------------------------------------------
        */

        $last =
            end($records);


        $lastCompleted =
            (int)(
                $_POST['completed'][
                    $last['id']
                ] ?? 0
            );


        $lastRejected =
            (int)(
                $_POST['rejected'][
                    $last['id']
                ] ?? 0
            );


        $lastPending =
            $production['target_quantity']
            - $lastCompleted
            - $lastRejected;


        if (
            $lastCompleted > 0
            && $lastPending == 0
        ) {

            $status = 'Completed';

        } elseif ($lastCompleted > 0) {

            $status = 'In Progress';

        } else {

            $status = 'Pending';

        }


        $stmt = $pdo->prepare("

            UPDATE production

            SET
                status = ?,
                operator_name = ?

            WHERE id = ?

        ");

        $stmt->execute([
            $status,
            $operator ?: $production['operator_name'],
            $id
        ]);


        $pdo->commit();


        header(
            "Location: stages.php?id="
            . $id
            . "&saved=1"
        );

        exit;


    } catch (Throwable $e) {

        $pdo->rollBack();

        $error =
            $e->getMessage();

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Production Stages</title>

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

        <div>

            <h2>
                <?= htmlspecialchars(
                    $production['product_name']
                ) ?>

                - Production Stages
            </h2>

            <div class="text-muted">

                <?= htmlspecialchars(
                    $production['product_type']
                ) ?>

                |

                <?= htmlspecialchars(
                    $production['capacity']
                ) ?>

                |

                Target:

                <?= number_format(
                    $production['target_quantity']
                ) ?>

            </div>

        </div>


        <a
        href="view.php?id=<?= $id ?>"
        class="btn btn-outline-primary">

            View

        </a>

    </div>


    <?php if (isset($_GET['saved'])): ?>

        <div class="alert alert-success">

            Production stages saved successfully.

        </div>

    <?php endif; ?>


    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <form method="post">

        <input
        type="hidden"
        name="production_id"
        value="<?= $id ?>">


        <div class="card card-body mb-3">

            <label class="form-label">
                Operator Name
            </label>

            <input
            class="form-control"
            name="operator_name"
            value="<?= htmlspecialchars(
                $production['operator_name'] ?? ''
            ) ?>">

        </div>


        <div class="card">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

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

                    <?php

                    $available =
                        $production['target_quantity'];

                    foreach ($records as $record):

                        $recordId =
                            $record['id'];

                    ?>

                    <tr>

                        <td>

                            <?= $record['stage_number'] ?>

                        </td>


                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $record['stage_name']
                                ) ?>

                            </strong>

                        </td>


                        <td>

                            <input
                            class="form-control"
                            id="input<?= $recordId ?>"
                            value="<?= $available ?>"
                            readonly>

                        </td>


                        <td>

                            <input
                            type="number"
                            min="0"
                            class="form-control qty-completed"
                            data-input="<?= $recordId ?>"
                            name="completed[<?= $recordId ?>]"
                            value="<?= $record['completed_quantity'] ?>">

                        </td>


                        <td>

                            <input
                            type="number"
                            min="0"
                            class="form-control qty-rejected"
                            data-input="<?= $recordId ?>"
                            name="rejected[<?= $recordId ?>]"
                            value="<?= $record['rejected_quantity'] ?>">

                        </td>


                        <td>

                            <input
                            class="form-control pending-field"
                            id="pending<?= $recordId ?>"
                            value="<?= $record['pending_quantity'] ?>"
                            readonly>

                        </td>


                        <td>

                            <input
                            class="form-control"
                            name="remarks[<?= $recordId ?>]"
                            value="<?= htmlspecialchars(
                                $record['remarks'] ?? ''
                            ) ?>">

                        </td>

                    </tr>

                    <?php

                    $available =
                        (int)$record['completed_quantity'];

                    endforeach;

                    ?>

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-3">

            <button
            class="btn btn-primary">

                Save Production

            </button>


            <a
            class="btn btn-secondary ms-2"
            href="index.php">

                Back

            </a>

        </div>

    </form>

</div>


<script>

document.querySelectorAll(
    '.qty-completed'
).forEach(function(element) {

    element.addEventListener(
        'input',
        function() {

            calculatePending(
                this.dataset.input
            );

        }
    );

});


document.querySelectorAll(
    '.qty-rejected'
).forEach(function(element) {

    element.addEventListener(
        'input',
        function() {

            calculatePending(
                this.dataset.input
            );

        }
    );

});


function calculatePending(id)
{

    const input =
        document.getElementById(
            'input' + id
        );

    const completed =
        document.querySelector(
            '.qty-completed[data-input="' + id + '"]'
        );

    const rejected =
        document.querySelector(
            '.qty-rejected[data-input="' + id + '"]'
        );

    const pending =
        document.getElementById(
            'pending' + id
        );


    const value =
        Number(input.value)
        - Number(completed.value || 0)
        - Number(rejected.value || 0);


    pending.value =
        Math.max(0, value);

}

</script>

</body>

</html>