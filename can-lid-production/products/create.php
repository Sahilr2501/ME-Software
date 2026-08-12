<?php

require_once '../config/database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name =
        trim($_POST['product_name']);

    $type =
        $_POST['product_type'];

    $capacity =
        trim($_POST['capacity']);


    $stmt = $pdo->prepare("

        INSERT INTO products
        (
            product_name,
            product_type,
            capacity
        )

        VALUES
        (?, ?, ?)

    ");


    $stmt->execute([

        $name,
        $type,
        $capacity

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

<title>Add Product</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<?php include '../assets/navbar.php'; ?>


<div class="container py-4">

    <h2>Add Product</h2>


    <form
    method="post"
    class="card card-body">


        <label class="form-label">
            Product Name
        </label>

        <input
        class="form-control mb-3"
        name="product_name"
        required>


        <label class="form-label">
            Product Type
        </label>

        <select
        class="form-select mb-3"
        name="product_type">

            <option value="Can">
                Can
            </option>

            <option value="Lid">
                Lid
            </option>

        </select>


        <label class="form-label">
            Capacity
        </label>

        <input
        class="form-control mb-3"
        name="capacity"
        placeholder="20 L">


        <button
        class="btn btn-primary">

            Save

        </button>

    </form>

</div>

</body>

</html>