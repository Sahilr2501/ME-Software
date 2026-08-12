<?php

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    UPDATE products
    SET status = 0
    WHERE id = ?
");

$stmt->execute([$id]);

header('Location: index.php');
exit;
