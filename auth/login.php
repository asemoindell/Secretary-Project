<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Try admin table first (if you have a separate admins table)
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: ../dashboard.php");
        exit();
    }

    // Try users table for secretary
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        if (isset($user['role']) && $user['role'] === 'secretary') {
            $_SESSION['secretary_id'] = $user['id'];
            $_SESSION['secretary_name'] = $user['fullname'];
            header('Location: ../secretary/secretary_dashboard.php');
            exit();
        }
        // Optionally, allow login for other user roles here
    }
    $error = "Invalid credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="card shadow-lg border-0 w-100" style="max-width: 400px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="https://cdn.jsdelivr.net/gh/creativetimofficial/argon-dashboard/assets/img/brand/blue.png" alt="Logo" width="60" class="mb-2">
                <h3 class="mb-0 fw-bold text-primary">Login</h3>
            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center py-2 mb-3"><?= $error; ?></div>
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
                <button class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
