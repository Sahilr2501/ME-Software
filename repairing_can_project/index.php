<?php
$pageTitle='Dashboard';
require 'config/database.php';
require 'config/helpers.php';

$received = (int)$pdo->query("
    SELECT COALESCE(SUM(with_ring + without_ring), 0)
    FROM repair_received
")->fetchColumn();
$completedRepairing = (int)$pdo->query("SELECT COALESCE(SUM(repairing),0) FROM repair_completed")->fetchColumn();
$available = max(0, $received - $completedRepairing);
$completed = (int)$pdo->query("SELECT COALESCE(SUM(total_can),0) FROM repair_completed")->fetchColumn();
$reject = (int)$pdo->query("SELECT COALESCE(SUM(total_reject),0) FROM repair_completed")->fetchColumn();

require 'header.php';
?>
<h3 class="mb-4">Repairing Can Dashboard</h3>
<div class="row g-3">
<?php foreach([
 ['Total Received', $received, 'primary'],
 ['Repairing Completed', $completedRepairing, 'success'],
 ['Pending Repairing', $available, 'warning'],
 ['Total Processed', $completed, 'info'],
 ['Total Reject', $reject, 'danger']
] as $s): ?>
<div class="col-md">
<div class="card p-3"><div class="text-muted"><?=e($s[0])?></div><div class="stat text-<?=$s[2]?>"><?=number_format($s[1])?></div></div>
</div>
<?php endforeach; ?>
</div>

<?php require 'footer.php'; ?>
