<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: auth/login.php");
    exit();
}
include 'includes/header.php';
require_once 'includes/db.php';

// Fetch all users
$users = $pdo->query('SELECT id, username, firstname, middlename FROM users ORDER BY id DESC')->fetchAll();
// Fetch all reports
$reports = $pdo->query('SELECT * FROM reports ORDER BY created_at DESC')->fetchAll();

// Fetch stats
$total_users = count($users);
$total_reports = count($reports);
$today = date('m-d-Y');
$users_today = 0;
$reports_today = 0;
$reports_updated_today = 0;
$reports_document_collected = 0;
$reports_cofo_collected = 0;
$unique_edl_numbers = [];
foreach ($users as $user) {
    if (isset($user['created_at']) && date('m-d-Y', strtotime($user['created_at'])) === $today) {
        $users_today++;
    }
}
foreach ($reports as $report) {
    if (isset($report['created_at']) && date('m-d-Y', strtotime($report['created_at'])) === $today) {
        $reports_today++;
    }
    if (isset($report['updated_at']) && date('m-d-Y', strtotime($report['updated_at'])) === $today) {
        $reports_updated_today++;
    }
    if (isset($report['status']) && stripos($report['status'], 'C of O collected') !== false) {
        $reports_cofo_collected++;
    }
    if (isset($report['document_collected']) && trim($report['document_collected']) !== '') {
        $reports_document_collected++;
    }
    if (isset($report['edl_registration_number']) && trim($report['edl_registration_number']) !== '') {
        $unique_edl_numbers[$report['edl_registration_number']] = true;
    }
}
$num_unique_edl_numbers = count($unique_edl_numbers);
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Admin Dashboard</h2>
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card text-bg-primary shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text fs-4 fw-bold"><?= $total_users; ?></p>
                    <div class="small">Today: <?= $users_today; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-success shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Reports</h5>
                    <p class="card-text fs-4 fw-bold"><?= $total_reports; ?></p>
                    <div class="small">Today: <?= $reports_today; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-warning shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Reports Updated Today</h5>
                    <p class="card-text fs-4 fw-bold"><?= $reports_updated_today; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-secondary shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Document/Survey Collected</h5>
                    <p class="card-text fs-4 fw-bold"><?= $reports_document_collected; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-dark shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">EDL Reg. Numbers</h5>
                    <p class="card-text fs-4 fw-bold"><?= $num_unique_edl_numbers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-info shadow h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">C of O Collected</h5>
                    <p class="card-text fs-4 fw-bold"><?= $reports_cofo_collected; ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Removed Users and Reports tables from dashboard as requested -->
</div>
<?php include 'includes/footer.php'; ?>
