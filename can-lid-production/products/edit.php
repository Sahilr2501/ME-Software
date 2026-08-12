<?php

require_once '../config/database.php';


$id =
    (int)($_GET['id'] ?? 0);


$stmt = $pdo->prepare("

    SELECT *
    FROM products

    WHERE id = ?

");

$stmt->execute([$id]);

$row =
    $stmt->fetch();


if (!$row) {

    die('Product not found.');

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name =
        trim($_POST['product_name']);

    $capacity =
        trim($_POST['capacity']);

    $status =
        isset($_POST['status'])
        ? 1
        : 0;


    $stmt = $pdo->prepare("

        UPDATE products

        SET
            product_name = ?,
            capacity = ?,
            status = ?

        WHERE id = ?

    ");


    $stmt->execute([

        $name,
        $capacity,
        $status,
        $id

    ]);


    header('Location: index.php');

    exit;

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Edit Product</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">

    <h2>Edit Product</h2>


    <form
    method="post"
    class="card card-body">


        <label class="form-label">
            Product Name
        </label>

        <input
        class="form-control mb-3"
        name="product_name"
        value="<?= htmlspecialchars(
            $row['product_name']
        ) ?>"
        required>


        <label class="form-label">
            Product Type
        </label>

        <input
        class="form-control mb-3"
        value="<?= htmlspecialchars(
            $row['product_type']
        ) ?>"
        disabled>


        <label class="form-label">
            Capacity
        </label>

        <input
        class="form-control mb-3"
        name="capacity"
        value="<?= htmlspecialchars(
            $row['capacity'] ?? ''
        ) ?>">


        <div class="form-check mb-3">

            <input
            class="form-check-input"
            type="checkbox"
            name="status"
            <?= $row['status']
                ? 'checked'
                : '' ?>>

            <label class="form-check-label">

                Active

            </label>

        </div>


        <button
        class="btn btn-primary">

            Save

        </button>

    </form>

</div>

</body>

</html>