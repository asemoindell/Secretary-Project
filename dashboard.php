<?php
session_start();
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_logged_in'])) {
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
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="fw-bold text-primary mb-2 mb-md-0">Admin Dashboard</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-external-link-alt me-2"></i>View Website
            </a>
            <a href="cms/cms_dashboard.php" class="btn btn-success">
                <i class="fas fa-globe me-2"></i>Website CMS
            </a>
        </div>
    </div>
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

    <!-- Website Content Management Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-globe me-2"></i>Website Content Management
                        </h4>
                        <a href="index.php" target="_blank" class="btn btn-light btn-sm">
                            <i class="fas fa-external-link-alt me-2"></i>View Live Website
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Company Information -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-3">
                                        <i class="fas fa-building fa-3x"></i>
                                    </div>
                                    <h5 class="card-title">Company Info</h5>
                                    <p class="card-text">Manage company name, tagline, about content, and contact details</p>
                                    <a href="cms/company_settings.php" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Hero Slides -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <div class="text-success mb-3">
                                        <i class="fas fa-images fa-3x"></i>
                                    </div>
                                    <h5 class="card-title">Hero Slides</h5>
                                    <p class="card-text">Control homepage slider content, backgrounds, and call-to-action buttons</p>
                                    <a href="cms/hero_slides.php" class="btn btn-success">
                                        <i class="fas fa-edit me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Services -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <div class="text-info mb-3">
                                        <i class="fas fa-tools fa-3x"></i>
                                    </div>
                                    <h5 class="card-title">Services</h5>
                                    <p class="card-text">Add, edit, or remove company services with descriptions and features</p>
                                    <a href="cms/services_management.php" class="btn btn-info">
                                        <i class="fas fa-edit me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-3">
                                        <i class="fas fa-chart-bar fa-3x"></i>
                                    </div>
                                    <h5 class="card-title">Statistics</h5>
                                    <p class="card-text">Update company achievements, client numbers, and success metrics</p>
                                    <a href="cms/company_stats.php" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Website Stats -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-chart-line me-2"></i>Website Content Status
                                    </h6>
                                    <div class="row text-center">
                                        <?php
                                        // Get website content stats
                                        try {
                                            $company_info = $pdo->query("SELECT * FROM company_info LIMIT 1")->fetch();
                                            $hero_slides_count = $pdo->query("SELECT COUNT(*) as count FROM hero_slides WHERE is_active = 1")->fetch()['count'];
                                            $services_count = $pdo->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1")->fetch()['count'];
                                            $stats_count = $pdo->query("SELECT COUNT(*) as count FROM company_stats WHERE is_active = 1")->fetch()['count'];
                                        } catch (PDOException $e) {
                                            $company_info = null;
                                            $hero_slides_count = 0;
                                            $services_count = 0;
                                            $stats_count = 0;
                                        }
                                        ?>
                                        <div class="col-md-3 col-6">
                                            <div class="p-2">
                                                <div class="fs-4 fw-bold text-primary"><?= $company_info ? '✓' : '✗' ?></div>
                                                <div class="small">Company Info</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="p-2">
                                                <div class="fs-4 fw-bold text-success"><?= $hero_slides_count ?></div>
                                                <div class="small">Active Slides</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="p-2">
                                                <div class="fs-4 fw-bold text-info"><?= $services_count ?></div>
                                                <div class="small">Active Services</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="p-2">
                                                <div class="fs-4 fw-bold text-warning"><?= $stats_count ?></div>
                                                <div class="small">Statistics</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="cms/cms_dashboard.php" class="btn btn-success btn-lg me-2">
                                <i class="fas fa-tachometer-alt me-2"></i>Full CMS Dashboard
                            </a>
                            <a href="index.php" target="_blank" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-eye me-2"></i>Preview Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Management Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>User Management
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage admin users and permissions</p>
                    <div class="d-grid gap-2">
                        <a href="users/index.php" class="btn btn-primary">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                        <a href="users/create.php" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Add New User
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Reports Management
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage daily reports and documentation</p>
                    <div class="d-grid gap-2">
                        <a href="daily_reports/index.php" class="btn btn-info">
                            <i class="fas fa-list me-2"></i>View All Reports
                        </a>
                        <a href="daily_reports/create.php" class="btn btn-outline-info">
                            <i class="fas fa-plus me-2"></i>Create New Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Removed Users and Reports tables from dashboard as requested -->
</div>
<?php include 'includes/footer.php'; ?>
