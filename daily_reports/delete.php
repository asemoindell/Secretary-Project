<?php
include '../includes/header.php';
require_once '../includes/db.php';

$success = '';
$error = '';

// Handle delete action
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Fetch report to get document paths for deletion
    $stmt = $pdo->prepare('SELECT documents FROM reports WHERE id = ?');
    $stmt->execute([$id]);
    $report = $stmt->fetch();
    if ($report) {
        // Delete associated documents
        $docs = json_decode($report['documents'] ?? '', true);
        if ($docs && is_array($docs)) {
            foreach ($docs as $doc) {
                $file = realpath($doc);
                if ($file && strpos($file, realpath('../assets/uploads/reports/')) === 0 && file_exists($file)) {
                    @unlink($file);
                }
            }
        }
        // Delete the report
        $stmt = $pdo->prepare('DELETE FROM reports WHERE id = ?');
        if ($stmt->execute([$id])) {
            $success = 'Report deleted successfully.';
        } else {
            $error = 'Failed to delete report.';
        }
    } else {
        $error = 'Report not found.';
    }
}
// Fetch all reports for display
$stmt = $pdo->query('SELECT id, full_name, title, created_at FROM reports ORDER BY created_at DESC');
$reports = $stmt->fetchAll();
?>
<div class="container py-4">
    <h1 class="mb-4 fw-bold text-danger">Delete Report</h1>
    <?php if ($success): ?>
        <div class="alert alert-success"> <?= $success ?> </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"> <?= $error ?> </div>
    <?php endif; ?>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Title</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?= $report['id'] ?></td>
                    <td><?= htmlspecialchars($report['full_name']) ?></td>
                    <td><?= htmlspecialchars($report['title']) ?></td>
                    <td><?= $report['created_at'] ?></td>
                    <td>
                        <a href="?id=<?= $report['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this report? This cannot be undone.');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="index.php" class="btn btn-secondary mt-3">Back to Reports</a>
</div>
<?php include '../includes/footer.php'; ?>
