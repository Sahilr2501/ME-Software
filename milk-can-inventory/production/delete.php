<?php

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT *
        FROM production
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $production = $stmt->fetch();

    if (!$production) {
        throw new Exception('Production not found.');
    }

    $stmt = $pdo->prepare("
        UPDATE products
        SET stock_quantity = stock_quantity - ?
        WHERE id = ?
        AND stock_quantity >= ?
    ");

    $stmt->execute([
        $production['quantity'],
        $production['product_id'],
        $production['quantity']
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Unable to reverse stock.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO stock_movements
        (
            product_id,
            movement_type,
            quantity,
            reference_type,
            reference_id,
            notes
        )
        VALUES (?, 'OUT', ?, 'PRODUCTION_DELETE', ?, ?)
    ");

    $stmt->execute([
        $production['product_id'],
        $production['quantity'],
        $production['id'],
        'Production record deleted'
    ]);

    $stmt = $pdo->prepare("
        DELETE FROM production
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $pdo->commit();

} catch (Exception $e) {

    $pdo->rollBack();
}

header('Location: index.php');
exit;