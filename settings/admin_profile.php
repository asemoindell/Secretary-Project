<?php
include '../includes/header.php';
require_once '../includes/db.php';

// Only admin can access
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$success = $error = '';
$admin_id = $_SESSION['admin_id'];

// Fetch current admin name
$stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$current_name = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['fullname'] ?? '');
    if (!$new_name) {
        $error = "Name cannot be empty.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET fullname = ? WHERE id = ?");
        $stmt->execute([$new_name, $admin_id]);
        $success = "Admin name updated successfully!";
        $current_name = $new_name;
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Modify Admin Name</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3" autocomplete="off">
        <div class="col-md-6">
            <label class="form-label">Admin Name</label>
            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($current_name) ?>" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Name</button>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>