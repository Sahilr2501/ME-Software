<?php

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT *
        FROM sale_items
        WHERE sale_id = ?
    ");

    $stmt->execute([$id]);

    $items = $stmt->fetchAll();

    if (!$items) {
        throw new Exception('Sale not found.');
    }

    foreach ($items as $item) {

        // Restore stock
        $stmt = $pdo->prepare("
            UPDATE products
            SET stock_quantity = stock_quantity + ?
            WHERE id = ?
        ");

        $stmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);

        // Record stock reversal
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
            VALUES (?, 'IN', ?, 'SALE_DELETE', ?, ?)
        ");

        $stmt->execute([
            $item['product_id'],
            $item['quantity'],
            $id,
            'Sale deleted - stock restored'
        ]);
    }

    // Delete sale
    $stmt = $pdo->prepare("
        DELETE FROM sales
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $pdo->commit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

header('Location: index.php');
exit;