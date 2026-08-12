<?php
require_once __DIR__ . '/config/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle ?? 'Repairing Can Management') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f6f9}.navbar-brand{font-weight:700}.card{border:0;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.stat{font-size:28px;font-weight:700}.table th{white-space:nowrap}
</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
<div class="container">
<a class="navbar-brand" href="index.php">Repairing Can</a>
<div>
<a class="btn btn-sm btn-outline-light me-1" href="form1.php">Form 1</a>
<a class="btn btn-sm btn-outline-light me-1" href="form2.php">Form 2</a>
<a class="btn btn-sm btn-outline-light" href="report.php">Report</a>
</div>
</div>
</nav>
<div class="container pb-5">
