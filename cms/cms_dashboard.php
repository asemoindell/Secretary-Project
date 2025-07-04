<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/header.php';
require_once '../includes/db.php';

// Get website statistics
$stats = $pdo->query("SELECT COUNT(*) as total_slides FROM hero_slides WHERE is_active = 1")->fetch();
$services_count = $pdo->query("SELECT COUNT(*) as total_services FROM services WHERE is_active = 1")->fetch();
$company_info = $pdo->query("SELECT * FROM company_info LIMIT 1")->fetch();
?>

<div class="container-fluid">
    <!-- Mobile Navigation Toggle -->
    <button class="mobile-nav-toggle d-md-none" id="mobileNavToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Mobile Sidebar Overlay -->
    <div class="cms-sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            <div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end min-vh-100 cms-sidebar" id="sidebar">
                <a href="cms_dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <i class="fas fa-globe me-2"></i>
                    <span class="fs-4">Website CMS</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="cms_dashboard.php" class="nav-link active" aria-current="page">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="company_settings.php" class="nav-link text-dark">
                            <i class="fas fa-building me-2"></i>Company Info
                        </a>
                    </li>
                    <li>
                        <a href="hero_slides.php" class="nav-link text-dark">
                            <i class="fas fa-images me-2"></i>Hero Slides
                        </a>
                    </li>
                    <li>
                        <a href="services_management.php" class="nav-link text-dark">
                            <i class="fas fa-tools me-2"></i>Services
                        </a>
                    </li>
                    <li>
                        <a href="company_stats.php" class="nav-link text-dark">
                            <i class="fas fa-chart-bar me-2"></i>Statistics
                        </a>
                    </li>
                    <li>
                        <hr>
                    </li>
                    <li>
                        <a href="../daily_reports/index.php" class="nav-link text-dark">
                            <i class="fas fa-file-alt me-2"></i>Reports System
                        </a>
                    </li>
                    <li>
                        <a href="../users/index.php" class="nav-link text-dark">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                    </li>
                    <li>
                        <a href="../index.php" class="nav-link text-dark" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <strong>Admin</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="../settings/admin_profile.php">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../auth/logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="container-fluid py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 text-primary">Website Content Management</h1>
                    <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Preview Website
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Hero Slides</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_slides'] ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-images fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Services</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $services_count['total_services'] ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Company Name</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= htmlspecialchars($company_info['company_name'] ?? 'Not Set') ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-building fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Last Updated</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            <?= $company_info ? date('M d, Y', strtotime($company_info['updated_at'])) : 'Never' ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-cog me-2"></i>Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <a href="company_settings.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-building me-2 text-primary"></i>
                                            <strong>Company Information</strong>
                                            <br><small class="text-muted">Update company details, contact info, and social links</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                    <a href="hero_slides.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-images me-2 text-success"></i>
                                            <strong>Hero Slides</strong>
                                            <br><small class="text-muted">Manage homepage slider content and images</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                    <a href="services_management.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-tools me-2 text-info"></i>
                                            <strong>Services</strong>
                                            <br><small class="text-muted">Add, edit, or remove company services</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                    <a href="company_stats.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-chart-bar me-2 text-warning"></i>
                                            <strong>Statistics</strong>
                                            <br><small class="text-muted">Update company achievements and numbers</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Website Preview
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="../assets/uploads/68457d6271809_6842c17014c99_logo.png" 
                                         alt="Website Preview" 
                                         class="img-fluid rounded mb-3" 
                                         style="max-height: 200px;">
                                    <p class="text-muted">Your website is live and accessible to visitors.</p>
                                    <a href="../index.php" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="fas fa-external-link-alt me-2"></i>View Live Website
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Changes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history me-2"></i>Getting Started
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-lightbulb me-2"></i>Welcome to your Website CMS!</h5>
                            <p class="mb-2">Your website is now fully admin-driven. Here's how to get started:</p>
                            <ol class="mb-0">
                                <li><strong>Company Info:</strong> Update your company name, contact details, and about section</li>
                                <li><strong>Hero Slides:</strong> Customize the homepage slider with your own content</li>
                                <li><strong>Services:</strong> Add or modify the services you offer</li>
                                <li><strong>Statistics:</strong> Update the achievement numbers displayed on your website</li>
                            </ol>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #007bff !important;
}
.border-left-success {
    border-left: 0.25rem solid #28a745 !important;
}
.border-left-info {
    border-left: 0.25rem solid #17a2b8 !important;
}
.border-left-warning {
    border-left: 0.25rem solid #ffc107 !important;
}
.text-gray-800 {
    color: #343a40 !important;
}
.text-gray-300 {
    color: #6c757d !important;
}
.text-xs {
    font-size: 0.75rem;
}
</style>

<script>
// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileNavToggle = document.getElementById('mobileNavToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (mobileNavToggle) {
        mobileNavToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
