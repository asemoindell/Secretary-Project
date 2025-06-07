<?php
session_start();

// Only allow access if secretary is logged in
if (!isset($_SESSION['secretary_id'])) {
    header('Location: /project1/secretary/login.php');
    exit();
}

include '../includes/header.php';
require_once '../includes/db.php';

$secretary_name = $_SESSION['secretary_name'] ?? 'Secretary';
$profile_picture = '';

// Fetch secretary profile picture if available
$stmt = $pdo->prepare('SELECT profile_picture FROM users WHERE id = ?');
$stmt->execute([$_SESSION['secretary_id']]);
$user = $stmt->fetch();
if ($user && $user['profile_picture']) {
    $profile_picture = $user['profile_picture'];
}

// Fetch stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_reports = $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();
$reports_cofo_collected = $pdo->query("SELECT COUNT(*) FROM reports WHERE status LIKE '%C of O collected%'")->fetchColumn();
$total_secretaries = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'secretary'")->fetchColumn();
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-info shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">C of O Collected</h5>
                    <p class="card-text fs-4 fw-bold"><?= $reports_cofo_collected; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-primary shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text fs-4 fw-bold"><?= $total_users; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Reports</h5>
                    <p class="card-text fs-4 fw-bold"><?= $total_reports; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Secretaries</h5>
                    <p class="card-text fs-4 fw-bold"><?= $total_secretaries; ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- ...rest of your HTML... -->
</div>
<?php include '../includes/footer.php'; ?>