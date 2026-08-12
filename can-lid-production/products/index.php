<?php

require_once '../config/database.php';

$rows =
    $pdo->query("

        SELECT *
        FROM products

        ORDER BY
            product_type,
            capacity,
            product_name

    ")->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Products</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">


    <div
    class="d-flex justify-content-between mb-3">

        <h2>Products</h2>


        <a
        href="create.php"
        class="btn btn-primary">

            + Add Product

        </a>

    </div>


    <div class="card">

        <div class="table-responsive">

            <table class="table mb-0">

                <thead class="table-light">

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Type</th>

                        <th>Capacity</th>

                        <th>Status</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($rows as $row): ?>

                    <tr>

                        <td>
                            <?= $row['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['product_name']
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['product_type']
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['capacity'] ?? ''
                            ) ?>
                        </td>

                        <td>

                            <?=
                                $row['status']
                                ? 'Active'
                                : 'Inactive'
                            ?>

                        </td>

                        <td>

                            <a
                            class="btn btn-sm btn-outline-secondary"
                            href="edit.php?id=<?= $row['id'] ?>">

                                Edit

                            </a>

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