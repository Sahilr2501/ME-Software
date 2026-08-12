<?php

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    DELETE FROM purchases
    WHERE id = ?
");

$stmt->execute([$id]);

header('Location: index.php');
exit;