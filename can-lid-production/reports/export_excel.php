<?php

require_once '../config/database.php';


$date =
    $_GET['date']
    ?? date('Y-m-d');


$type =
    $_GET['type']
    ?? '';


$where = [
    'psr.production_date = ?'
];


$params = [
    $date
];


if ($type) {

    $where[] =
        'p.product_type = ?';

    $params[] =
        $type;

}


$sql = "

SELECT

    psr.production_date,

    p.product_name,

    p.product_type,

    p.capacity,

    ps.stage_number,

    ps.stage_name,

    psr.input_quantity,

    psr.completed_quantity,

    psr.rejected_quantity,

    psr.pending_quantity,

    psr.operator_name,

    psr.remarks


FROM production_stage_records psr

JOIN production pr
    ON pr.id = psr.production_id

JOIN products p
    ON p.id = pr.product_id

JOIN production_stages ps
    ON ps.id = psr.stage_id


WHERE

    " . implode(
        ' AND ',
        $where
    ) . "


ORDER BY

    p.product_type,

    ps.stage_number,

    psr.production_date

";


$stmt =
    $pdo->prepare($sql);


$stmt->execute($params);


$rows =
    $stmt->fetchAll();


$filename =
    'production_report_'
    . $date
    . '.xls';


header(
    'Content-Type: application/vnd.ms-excel; charset=UTF-8'
);

header(
    'Content-Disposition: attachment; filename="'
    . $filename
    . '"'
);


echo "\xEF\xBB\xBF";

?>

<table border="1">

<tr>

    <th>Date</th>

    <th>Product</th>

    <th>Type</th>

    <th>Capacity</th>

    <th>Stage No.</th>

    <th>Stage</th>

    <th>Input</th>

    <th>Completed</th>

    <th>Rejected</th>

    <th>Pending</th>

    <th>Operator</th>

    <th>Remarks</th>

</tr>


<?php foreach ($rows as $row): ?>

<tr>

    <td>
        <?= htmlspecialchars(
            $row['production_date']
        ) ?>
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
            $row['capacity']
        ) ?>
    </td>

    <td>
        <?= $row['stage_number'] ?>
    </td>

    <td>
        <?= htmlspecialchars(
            $row['stage_name']
        ) ?>
    </td>

    <td>
        <?= $row['input_quantity'] ?>
    </td>

    <td>
        <?= $row['completed_quantity'] ?>
    </td>

    <td>
        <?= $row['rejected_quantity'] ?>
    </td>

    <td>
        <?= $row['pending_quantity'] ?>
    </td>

    <td>
        <?= htmlspecialchars(
            $row['operator_name']
        ) ?>
    </td>

    <td>
        <?= htmlspecialchars(
            $row['remarks']
        ) ?>
    </td>

</tr>

<?php endforeach; ?>

</table>