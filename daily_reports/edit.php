<?php
include '../includes/header.php';
require_once '../includes/db.php';

$success = '';
$error = '';
$report = null;
$timestamp = '';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare('SELECT * FROM reports WHERE id = ?');
    $stmt->execute([$id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($report) {
        $timestamp = $report['updated_at'] ?? $report['created_at'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $fields = [
        'full_name', 'edl_registration_number', 'properties', 'phone', 'address',
        'land_size', 'passport_upload', 'community', 'status', 'document_collected', 'other', 'title', 'content'
    ];
    $values = [];
    foreach ($fields as $field) {
        $values[$field] = trim($_POST[$field] ?? '');
    }
    // Handle passport upload
    $passport_upload = $report['passport_upload'] ?? '';
    if (isset($_FILES['passport_upload']) && $_FILES['passport_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['passport_upload']['name']);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['passport_upload']['tmp_name'], $target)) {
            $passport_upload = $filename;
        }
    }
    $stmt = $pdo->prepare('UPDATE reports SET full_name=?, edl_registration_number=?, properties=?, phone=?, address=?, land_size=?, passport_upload=?, community=?, status=?, document_collected=?, other=?, title=?, content=?, updated_at=NOW() WHERE id=?');
    try {
        $stmt->execute([
            $values['full_name'], $values['edl_registration_number'], $values['properties'], $values['phone'],
            $values['address'], $values['land_size'], $passport_upload, $values['community'],
            $values['status'], $values['document_collected'], $values['other'], $values['title'], $values['content'], $id
        ]);
        $success = 'Report updated successfully!';
        // Refresh report data
        $stmt = $pdo->prepare('SELECT * FROM reports WHERE id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        $timestamp = $report['updated_at'] ?? $report['created_at'];
    } catch (PDOException $e) {
        $error = 'Error updating report: ' . $e->getMessage();
    }
}
?>
<div class="container py-4">
    <h1 class="mb-4 fw-bold text-primary">Edit Report</h1>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($report): ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $report['id'] ?>">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($report['full_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">EDL / Registration Number</label>
            <input type="text" name="edl_registration_number" class="form-control" value="<?= htmlspecialchars($report['edl_registration_number']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Properties</label>
            <input type="text" name="properties" class="form-control" value="<?= htmlspecialchars($report['properties']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($report['phone']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($report['address']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Land Size</label>
            <input type="text" name="land_size" class="form-control" value="<?= htmlspecialchars($report['land_size']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Passport (for easy identification)</label>
            <input type="file" name="passport_upload" class="form-control">
            <?php if ($report['passport_upload']): ?>
                <div class="mt-2"><img src="../assets/uploads/<?= htmlspecialchars($report['passport_upload']) ?>" width="60"></div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Community</label>
            <input type="text" name="community" class="form-control" value="<?= htmlspecialchars($report['community']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Status (enter 'C of O collected' if applicable)</label>
            <input type="text" name="status" class="form-control" value="<?= htmlspecialchars($report['status']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Document Collected</label>
            <input type="text" name="document_collected" class="form-control" value="<?= htmlspecialchars($report['document_collected']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Other</label>
            <textarea name="other" class="form-control"><?= htmlspecialchars($report['other']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Report Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($report['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Report Content</label>
            <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($report['content']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Last Updated</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($timestamp) ?>" readonly>
        </div>
        <button class="btn btn-primary">Update Report</button>
        <a href="index.php" class="btn btn-secondary">Back to Reports</a>
    </form>
    <?php else: ?>
        <div class="alert alert-warning">Report not found.</div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
