<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Connect to the database and use the users table for authentication
require_once '../includes/db.php';

$errors = [];
$profile_picture = '';
$fullname = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';

    if (!$username) $errors[] = 'Username is required.';
    if (!$password) $errors[] = 'Password is required.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $profile_picture = $user['profile_picture'];
            $fullname = $user['fullname'];
            // Redirect based on user role
            if (isset($user['role'])) {
                switch ($user['role']) {
                    case 'admin':
                        header('Location: admin_dashboard.php');
                        exit();
                    case 'secretary':
                        header('Location: secretary_dashboard.php');
                        exit();
                    // Add more roles and destinations as needed
                    default:
                        $errors[] = 'Access denied: unknown role.';
                }
            } else {
                $errors[] = 'Access denied: no role assigned.';
            }
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}
include '../includes/header.php';
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Login</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary">Login</button>
        <a href="../index.php" class="btn btn-link">Back to Home / Login</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>