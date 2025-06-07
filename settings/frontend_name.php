<?php
include '../includes/header.php';
require_once '../includes/db.php';

// Only admin can access
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$success = $error = '';
$setting_key = 'frontend_name';

// Fetch current frontend/index name from settings table
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ?");
$stmt->execute([$setting_key]);
$current_name = $stmt->fetchColumn();
if ($current_name === false) $current_name = 'Frontend/Index';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['frontend_name'] ?? '');
    if (!$new_name) {
        $error = "Frontend/Index name cannot be empty.";
    } else {
        // Insert or update the frontend name in settings table
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
        $stmt->execute([$setting_key, $new_name]);
        $success = "Frontend/Index name updated successfully!";
        $current_name = $new_name;
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Change Frontend/Index Name</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3" autocomplete="off">
        <div class="col-md-6">
            <label class="form-label">Frontend/Index Name</label>
            <input type="text" name="frontend_name" class="form-control" value="<?= htmlspecialchars($current_name) ?>" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Name</button>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>