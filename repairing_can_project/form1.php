<?php
$pageTitle='Form 1 - Receive Can';
require 'config/database.php';
require 'config/helpers.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $date=$_POST['repair_date'] ?? date('Y-m-d');
    $challa=trim($_POST['challa_num'] ?? '');
    $with=(int)($_POST['with_ring'] ?? 0);
    $without=(int)($_POST['without_ring'] ?? 0);
    $handle=(int)($_POST['without_handle'] ?? 0);
    $total=$with+$without+$handle;

    if ($challa==='') $error='Challa Num is required.';
    elseif (min($with,$without,$handle)<0) $error='Can quantities cannot be negative.';
    else {
        $st=$pdo->prepare("INSERT INTO repair_received
        (repair_date,challa_num,with_ring,without_ring,without_handle,total_can)
        VALUES (?,?,?,?,?,?)");
        $st->execute([$date,$challa,$with,$without,$handle,$total]);
        redirect('form1.php?saved=1');
    }
}
$rows=$pdo->query("SELECT * FROM repair_received ORDER BY id DESC LIMIT 50")->fetchAll();
require 'header.php';
?>
<div class="card p-4">  
<h3>Can Received for Repairing</h3>
<?php if(isset($_GET['saved'])): ?><div class="alert alert-success">Entry saved successfully.</div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
<form method="post" class="row g-3">
<div class="col-md-3"><label class="form-label">Date</label><input type="date" name="repair_date" class="form-control" value="<?=e($_POST['repair_date']??date('Y-m-d'))?>" required></div>
<div class="col-md-3"><label class="form-label">Challa Num</label><input type="text" name="challa_num" class="form-control" required></div>
<div class="col-md-2"><label class="form-label">With Ring</label><input id="with_ring" type="number" min="0" name="with_ring" class="form-control" value="0" oninput="calculateForm1()"></div>
<div class="col-md-2"><label class="form-label">Without Ring</label><input id="without_ring" type="number" min="0" name="without_ring" class="form-control" value="0" oninput="calculateForm1()"></div>
<div class="col-md-2"><label class="form-label">Without Handle</label><input id="without_handle" type="number" min="0" name="without_handle" class="form-control" value="0" oninput="calculateForm1()"></div>
<div class="col-md-3"><label class="form-label">Total Can</label><input id="total_can" type="number" class="form-control" value="0" readonly></div>
<div class="col-12"><button class="btn btn-primary">Save Form 1</button></div>
</form>
</div>
<div class="card mt-4 p-4">
<h5>Recent Entries</h5>
<div class="table-responsive"><table class="table table-bordered table-sm">
<thead><tr><th>Date</th><th>Challa</th><th>With Ring</th><th>Without Ring</th><th>Without Handle</th><th>Total</th></tr></thead>
<tbody><?php foreach($rows as $r): ?><tr>
<td><?=e($r['repair_date'])?></td><td><?=e($r['challa_num'])?></td><td><?=e($r['with_ring'])?></td><td><?=e($r['without_ring'])?></td><td><?=e($r['without_handle'])?></td><td><strong><?=e($r['total_can'])?></strong></td>
</tr><?php endforeach; ?></tbody></table></div>
</div>
<?php require 'footer.php'; ?>
