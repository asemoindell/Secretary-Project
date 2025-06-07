<?php
include '../includes/header.php';
require_once '../includes/db.php';

// Only admin can access
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$success = $error = '';
$setting_key = 'welcome_message';

// Fetch current welcome message from settings table
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ?");
$stmt->execute([$setting_key]);
$current_message = $stmt->fetchColumn();
if ($current_message === false) $current_message = 'Welcome to demo HRM';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_message = trim($_POST['welcome_message'] ?? '');
    if (!$new_message) {
        $error = "Welcome message cannot be empty.";
    } else {
        // Insert or update the welcome message in settings table
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
        $stmt->execute([$setting_key, $new_message]);
        $success = "Welcome message updated successfully!";
        $current_message = $new_message;
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Edit Welcome Message</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3" autocomplete="off">
        <div class="col-md-8">
            <label class="form-label">Welcome Message</label>
            <textarea name="welcome_message" class="form-control" rows="3" required><?= htmlspecialchars($current_message) ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Message</button>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>