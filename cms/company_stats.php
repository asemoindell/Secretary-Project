<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/login.php');
    exit;
}

include '../includes/header.php';
require_once '../includes/db.php';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $title = trim($_POST['title']);
                $value = trim($_POST['value']);
                $icon = trim($_POST['icon']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $display_order = (int)$_POST['display_order'];

                try {
                    if ($_POST['action'] === 'add') {
                        $stmt = $pdo->prepare("INSERT INTO company_stats (title, value, icon, is_active, display_order) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $value, $icon, $is_active, $display_order]);
                        $success = 'Statistic added successfully!';
                    } else {
                        $id = (int)$_POST['id'];
                        $stmt = $pdo->prepare("UPDATE company_stats SET title = ?, value = ?, icon = ?, is_active = ?, display_order = ? WHERE id = ?");
                        $stmt->execute([$title, $value, $icon, $is_active, $display_order, $id]);
                        $success = 'Statistic updated successfully!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error saving statistic: ' . $e->getMessage();
                }
                break;

            case 'delete':
                $id = (int)$_POST['id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM company_stats WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Statistic deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting statistic: ' . $e->getMessage();
                }
                break;

            case 'toggle_status':
                $id = (int)$_POST['id'];
                try {
                    $stmt = $pdo->prepare("UPDATE company_stats SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Statistic status updated!';
                } catch (PDOException $e) {
                    $error = 'Error updating status: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all statistics
$stats = $pdo->query("SELECT * FROM company_stats ORDER BY display_order ASC, id ASC")->fetchAll();

// Get statistic for editing if ID is provided
$edit_stat = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_stat = $pdo->prepare("SELECT * FROM company_stats WHERE id = ?");
    $edit_stat->execute([$edit_id]);
    $edit_stat = $edit_stat->fetch();
}

// Common FontAwesome icons for statistics
$common_icons = [
    'fas fa-users' => 'Users/Clients',
    'fas fa-certificate' => 'Certificates',
    'fas fa-map-marked-alt' => 'Locations',
    'fas fa-clock' => 'Time/Years',
    'fas fa-home' => 'Properties',
    'fas fa-handshake' => 'Deals',
    'fas fa-trophy' => 'Awards',
    'fas fa-star' => 'Reviews',
    'fas fa-chart-line' => 'Growth',
    'fas fa-building' => 'Projects',
    'fas fa-tasks' => 'Completed',
    'fas fa-smile' => 'Satisfaction'
];
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
                        <a href="cms_dashboard.php" class="nav-link text-dark">
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
                        <a href="company_stats.php" class="nav-link active" aria-current="page">
                            <i class="fas fa-chart-bar me-2"></i>Statistics
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="container-fluid py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 text-primary">Company Statistics</h1>
                    <a href="../index.php#about" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Preview Statistics
                    </a>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Add/Edit Form -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-plus me-2"></i><?= $edit_stat ? 'Edit Statistic' : 'Add New Statistic' ?>
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="<?= $edit_stat ? 'edit' : 'add' ?>">
                                    <?php if ($edit_stat): ?>
                                        <input type="hidden" name="id" value="<?= $edit_stat['id'] ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" class="form-control" 
                                               value="<?= htmlspecialchars($edit_stat['title'] ?? '') ?>" 
                                               placeholder="e.g., Happy Clients" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Value</label>
                                        <input type="text" name="value" class="form-control" 
                                               value="<?= htmlspecialchars($edit_stat['value'] ?? '') ?>" 
                                               placeholder="e.g., 500+, 10 Years" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Icon</label>
                                        <select name="icon" class="form-control" required>
                                            <option value="">Select an icon...</option>
                                            <?php foreach ($common_icons as $icon_class => $icon_name): ?>
                                                <option value="<?= $icon_class ?>" 
                                                        <?= ($edit_stat['icon'] ?? '') === $icon_class ? 'selected' : '' ?>>
                                                    <?= $icon_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Display Order</label>
                                        <input type="number" name="display_order" class="form-control" 
                                               value="<?= $edit_stat['display_order'] ?? 0 ?>" min="0">
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                               <?= ($edit_stat['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i><?= $edit_stat ? 'Update Statistic' : 'Add Statistic' ?>
                                    </button>
                                    
                                    <?php if ($edit_stat): ?>
                                        <a href="company_stats.php" class="btn btn-secondary w-100 mt-2">
                                            <i class="fas fa-times me-2"></i>Cancel Edit
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div class="card shadow mt-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-eye me-2"></i>Preview
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="feature-box-preview p-3 bg-light rounded">
                                    <i class="fas fa-chart-bar fa-3x text-primary mb-2"></i>
                                    <h4>500+</h4>
                                    <p class="mb-0">Happy Clients</p>
                                </div>
                                <small class="text-muted mt-2 d-block">This is how your statistic will appear on the website</small>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics List -->
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-list me-2"></i>Current Statistics (<?= count($stats) ?>)
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($stats)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No statistics created yet</h5>
                                        <p class="text-muted">Add your first statistic using the form on the left.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($stats as $stat): ?>
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card h-100 <?= $stat['is_active'] ? 'border-primary' : 'border-secondary' ?>">
                                                    <div class="card-body text-center">
                                                        <div class="d-flex justify-content-end mb-2">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item" href="company_stats.php?edit=<?= $stat['id'] ?>">
                                                                            <i class="fas fa-edit me-2"></i>Edit
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <form method="POST" class="d-inline">
                                                                            <input type="hidden" name="action" value="toggle_status">
                                                                            <input type="hidden" name="id" value="<?= $stat['id'] ?>">
                                                                            <button type="submit" class="dropdown-item">
                                                                                <i class="fas fa-toggle-<?= $stat['is_active'] ? 'off' : 'on' ?> me-2"></i>
                                                                                <?= $stat['is_active'] ? 'Deactivate' : 'Activate' ?>
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this statistic?')">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?= $stat['id'] ?>">
                                                                            <button type="submit" class="dropdown-item text-danger">
                                                                                <i class="fas fa-trash me-2"></i>Delete
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                        <i class="<?= htmlspecialchars($stat['icon']) ?> fa-3x <?= $stat['is_active'] ? 'text-primary' : 'text-muted' ?> mb-3"></i>
                                                        <h4 class="<?= $stat['is_active'] ? 'text-dark' : 'text-muted' ?>"><?= htmlspecialchars($stat['value']) ?></h4>
                                                        <p class="<?= $stat['is_active'] ? 'text-dark' : 'text-muted' ?> mb-2"><?= htmlspecialchars($stat['title']) ?></p>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                                            <small class="text-muted">Order: <?= $stat['display_order'] ?></small>
                                                            <span class="badge <?= $stat['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                                <?= $stat['is_active'] ? 'Active' : 'Inactive' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Website Preview -->
                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h6 class="mb-3"><i class="fas fa-eye me-2"></i>Website Preview</h6>
                                        <div class="row">
                                            <?php foreach (array_filter($stats, function($s) { return $s['is_active']; }) as $stat): ?>
                                                <div class="col-md-3 col-6 mb-3">
                                                    <div class="text-center p-3 bg-white rounded shadow-sm">
                                                        <i class="<?= htmlspecialchars($stat['icon']) ?> fa-2x text-primary mb-2"></i>
                                                        <h6 class="mb-0"><?= htmlspecialchars($stat['value']) ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($stat['title']) ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
