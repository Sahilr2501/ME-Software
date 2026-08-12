<?php
$pageTitle='Report';
require 'config/database.php';
require 'config/helpers.php';

$received=$pdo->query("
    SELECT 
        COALESCE(SUM(with_ring + without_ring),0) total,
        COALESCE(SUM(with_ring),0) with_ring,
        COALESCE(SUM(without_ring),0) without_ring,
        COALESCE(SUM(without_handle),0) without_handle
    FROM repair_received
")->fetch();
$output=$pdo->query("SELECT COALESCE(SUM(new_handle),0) new_handle, COALESCE(SUM(new_bottom_ring),0) new_bottom_ring, COALESCE(SUM(new_bottom_dish),0) new_bottom_dish, COALESCE(SUM(repairing),0) repairing, COALESCE(SUM(buffing_can),0) buffing_can, COALESCE(SUM(cleaning_can),0) cleaning_can, COALESCE(SUM(total_can),0) total, COALESCE(SUM(total_reject),0) reject FROM repair_completed")->fetch();
$pending=max(0,(int)$received['total']-(int)$output['repairing']);
require 'header.php';
?>
<h3 class="mb-4">Repairing Can Report</h3>
<div class="row g-3">
<div class="col-md-6"><div class="card p-4"><h5>Form 1 - Received</h5>
<table class="table"><tr><td>With Ring</td><td><?=number_format($received['with_ring'])?></td></tr>
<tr><td>Without Ring</td><td><?=number_format($received['without_ring'])?></td></tr>
<tr><td>Without Handle</td><td><?=number_format($received['without_handle'])?></td></tr>
<tr class="table-primary"><th>Total Can</th><th><?=number_format($received['total'])?></th></tr></table></div></div>
<div class="col-md-6"><div class="card p-4"><h5>Form 2 - Processed</h5>
<table class="table"><tr><td>New Handle</td><td><?=number_format($output['new_handle'])?></td></tr>
<tr><td>New Bottom Ring</td><td><?=number_format($output['new_bottom_ring'])?></td></tr>
<tr><td>New Bottom Dish</td><td><?=number_format($output['new_bottom_dish'])?></td></tr>
<tr><td>Repairing</td><td><?=number_format($output['repairing'])?></td></tr>
<tr><td>Buffing Can</td><td><?=number_format($output['buffing_can'])?></td></tr>
<tr><td>Cleaning Can</td><td><?=number_format($output['cleaning_can'])?></td></tr>
<tr class="table-success"><th>Total Can</th><th><?=number_format($output['total'])?></th></tr>
<tr class="table-danger"><th>Total Reject</th><th><?=number_format($output['reject'])?></th></tr></table></div></div>
</div>
<div class="card mt-4 p-4">
<h5>Pending Repairing Stock</h5>
<div class="display-5"><?=number_format($pending)?></div>
<p class="text-muted mb-0">Formula: Form 1 Total Can − Form 2 Repairing.</p>
</div>
<?php require 'footer.php'; ?>
