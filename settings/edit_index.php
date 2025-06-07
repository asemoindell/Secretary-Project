<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/header.php';
require_once '../includes/db.php';

// Only admin can access
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$success = $error = '';
$setting_key = 'index_content';

// Fetch current index content from settings table
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ?");
$stmt->execute([$setting_key]);
$current_content = $stmt->fetchColumn();
if ($current_content === false) $current_content = '<h1>Welcome to the Home Page</h1>';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_content = trim($_POST['index_content'] ?? '');
    if (!$new_content) {
        $error = "Index content cannot be empty.";
    } else {
        // Insert or update the index content in settings table
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
        $stmt->execute([$setting_key, $new_content]);
        $success = "Index content updated successfully!";
        $current_content = $new_content;
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Edit Everything on Index Page</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3" autocomplete="off">
        <div class="col-md-12">
            <label class="form-label">Index Page Content (HTML allowed)</label>
            <textarea name="index_content" class="form-control" rows="10" required><?= htmlspecialchars($current_content) ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Index Page</button>
        </div>
    </form>
</div>
<?php
$stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ?");
$stmt->execute(['index_content']);
$index_content = $stmt->fetchColumn();
echo $index_content ? $index_content : '<h1>Welcome to the Home Page</h1>';
include '../includes/footer.php';