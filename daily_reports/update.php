<?php
include '../includes/header.php';
require_once '../includes/db.php';

// Fetch all reports for dropdown
$reports = $pdo->query("SELECT id, full_name, status, pause_reason FROM reports ORDER BY created_at DESC")->fetchAll();

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = intval($_POST['report_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $pause_reason = trim($_POST['pause_reason'] ?? '');

    if (!$report_id || !$status) {
        $error = "Please select a report and status.";
    } elseif (in_array($status, ['Paused', 'Terminated']) && !$pause_reason) {
        $error = "Please provide a reason for Paused or Terminated status.";
    } else {
        $stmt = $pdo->prepare("UPDATE reports SET status = ?, pause_reason = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, ($status === 'Paused' || $status === 'Terminated') ? $pause_reason : null, $report_id]);
        $success = "Report updated successfully.";
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Update Report Status</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" id="updateForm" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Select Report</label>
            <select name="report_id" class="form-select" required>
                <option value="">-- Select Report --</option>
                <?php foreach ($reports as $r): ?>
                    <option value="<?= $r['id'] ?>">
                        <?= htmlspecialchars($r['id'] . ' - ' . $r['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" id="statusSelect" class="form-select" required>
                <option value="">-- Select Status --</option>
                <option value="Collected">Collected</option>
                <option value="Processing">Processing</option>
                <option value="Paused">Paused</option>
                <option value="Terminated">Terminated</option>
            </select>
        </div>
        <div class="col-12" id="reasonDiv" style="display:none;">
            <label class="form-label">Reason for Paused/Terminated</label>
            <textarea name="pause_reason" id="pauseReason" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Report</button>
        </div>
    </form>
    <div class="mt-4">
        <h4 class="fw-bold">Reports List</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['id']) ?></td>
                        <td><?= htmlspecialchars($report['full_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($report['status']) ?>
                            <?php if (in_array($report['status'], ['Paused', 'Terminated']) && !empty($report['pause_reason'])): ?>
                                <br><small class="text-danger"><?= htmlspecialchars($report['pause_reason']) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.getElementById('statusSelect').addEventListener('change', function() {
    var val = this.value;
    var reasonDiv = document.getElementById('reasonDiv');
    var pauseReason = document.getElementById('pauseReason');
    if (val === 'Paused' || val === 'Terminated') {
        reasonDiv.style.display = '';
        pauseReason.required = true;
    } else {
        reasonDiv.style.display = 'none';
        pauseReason.required = false;
    }
});
</script>
<?php include '../includes/footer.php'; ?>
