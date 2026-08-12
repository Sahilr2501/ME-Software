<?php
$pageTitle='Form 2 - Repair Output';
require 'config/database.php';
require 'config/helpers.php';

$received=(int)$pdo->query("SELECT COALESCE(SUM(total_can),0) FROM repair_received")->fetchColumn();
$done=(int)$pdo->query("SELECT COALESCE(SUM(repairing),0) FROM repair_completed")->fetchColumn();
$pending=max(0,$received-$done);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $date=$_POST['repair_date'] ?? date('Y-m-d');
    $vals=[];
    foreach(['new_handle','new_bottom_ring','new_bottom_dish','repairing','buffing_can','cleaning_can','total_reject'] as $k) {
        $vals[$k]=(int)($_POST[$k]??0);
    }
    if ($vals['repairing']>$pending) {
        $error="Repairing cannot be more than pending repairing cans ($pending).";
    } elseif (min($vals)<0) {
        $error="Can quantities cannot be negative.";
    } else {
        $total=$vals['new_handle']+$vals['new_bottom_ring']+$vals['new_bottom_dish']+
               $vals['repairing']+$vals['buffing_can']+$vals['cleaning_can'];
        $st=$pdo->prepare("INSERT INTO repair_completed
        (repair_date,new_handle,new_bottom_ring,new_bottom_dish,repairing,buffing_can,cleaning_can,total_can,total_reject)
        VALUES (?,?,?,?,?,?,?,?,?)");
        $st->execute([$date,$vals['new_handle'],$vals['new_bottom_ring'],$vals['new_bottom_dish'],
            $vals['repairing'],$vals['buffing_can'],$vals['cleaning_can'],$total,$vals['total_reject']]);
        redirect('form2.php?saved=1');
    }
}
$rows=$pdo->query("SELECT * FROM repair_completed ORDER BY id DESC LIMIT 50")->fetchAll();
require 'header.php';
?>
<div class="card p-4">
<h3>Form 2 - Repairing / Processing</h3>
<div class="alert alert-warning">Pending Repairing Cans: <strong><?=number_format($pending)?></strong></div>
<?php if(isset($_GET['saved'])): ?><div class="alert alert-success">Entry saved successfully.</div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
<form method="post" class="row g-3">
<div class="col-md-3"><label class="form-label">Date</label><input type="date" name="repair_date" class="form-control" value="<?=e($_POST['repair_date']??date('Y-m-d'))?>" required></div>
<div class="col-md-3"><label class="form-label">New Handle</label><input id="new_handle" type="number" min="0" name="new_handle" class="form-control" value="0" oninput="calculateForm2()"></div>
<div class="col-md-3"><label class="form-label">New Bottom Ring</label><input id="new_bottom_ring" type="number" min="0" name="new_bottom_ring" class="form-control" value="0" oninput="calculateForm2()"></div>
<div class="col-md-3"><label class="form-label">New Bottom Dish</label><input id="new_bottom_dish" type="number" min="0" name="new_bottom_dish" class="form-control" value="0" oninput="calculateForm2()"></div>
<div class="col-md-3"><label class="form-label">Repairing</label><input id="repairing" type="number" min="0" max="<?=$pending?>" name="repairing" class="form-control" value="0" oninput="calculateForm2()"></div>
<div class="col-md-3"><label class="form-label">Buffing Can</label><input id="buffing_can" type="number" min="0" name="buffing_can" class="form-control" value="0" oninput="calculateForm2()"></div>
<div class="col-md-3"><label class="form-label">Cleaning Can</label><input id="cleaning_can" type="number" min="0" name="cleaning_can" class="form-control" value="0" oninput="calculateForm2()"></div>
<div class="col-md-3"><label class="form-label">Total Can</label><input id="total_can" type="number" class="form-control" value="0" readonly></div>
<div class="col-md-3"><label class="form-label">Total Reject</label><input type="number" min="0" name="total_reject" class="form-control" value="0"></div>
<div class="col-12"><button class="btn btn-success">Save Form 2</button></div>
</form>
</div>
<div class="card mt-4 p-4">
<h5>Recent Entries</h5>
<div class="table-responsive"><table class="table table-bordered table-sm">
<thead><tr><th>Date</th><th>New Handle</th><th>Bottom Ring</th><th>Bottom Dish</th><th>Repairing</th><th>Buffing</th><th>Cleaning</th><th>Total</th><th>Reject</th></tr></thead>
<tbody><?php foreach($rows as $r): ?><tr>
<td><?=e($r['repair_date'])?></td><td><?=e($r['new_handle'])?></td><td><?=e($r['new_bottom_ring'])?></td><td><?=e($r['new_bottom_dish'])?></td><td><?=e($r['repairing'])?></td><td><?=e($r['buffing_can'])?></td><td><?=e($r['cleaning_can'])?></td><td><strong><?=e($r['total_can'])?></strong></td><td><?=e($r['total_reject'])?></td>
</tr><?php endforeach; ?></tbody></table></div>
</div>
<?php require 'footer.php'; ?>
