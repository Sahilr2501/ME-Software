<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Milk Can Inventory';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?> - Milk Can Inventory</title>

    <link rel="stylesheet" href="/Me Software/milk-can-inventory/assets/css/style.css">
</head>

<body>

<div class="layout">

<?php include __DIR__ . '/sidebar.php'; ?>

<div class="main-content">

<header class="topbar">
    <h2><?= htmlspecialchars($pageTitle) ?></h2>
</header>

<main class="container">