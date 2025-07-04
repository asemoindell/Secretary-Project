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
                $description = trim($_POST['description']);
                $icon = trim($_POST['icon']);
                $features = array_filter(array_map('trim', $_POST['features']));
                $features_json = json_encode($features);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $display_order = (int)$_POST['display_order'];

                try {
                    if ($_POST['action'] === 'add') {
                        $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, features, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $description, $icon, $features_json, $is_active, $display_order]);
                        $success = 'Service added successfully!';
                    } else {
                        $id = (int)$_POST['id'];
                        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, features = ?, is_active = ?, display_order = ? WHERE id = ?");
                        $stmt->execute([$title, $description, $icon, $features_json, $is_active, $display_order, $id]);
                        $success = 'Service updated successfully!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error saving service: ' . $e->getMessage();
                }
                break;

            case 'delete':
                $id = (int)$_POST['id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Service deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting service: ' . $e->getMessage();
                }
                break;

            case 'toggle_status':
                $id = (int)$_POST['id'];
                try {
                    $stmt = $pdo->prepare("UPDATE services SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Service status updated!';
                } catch (PDOException $e) {
                    $error = 'Error updating status: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all services
$services = $pdo->query("SELECT * FROM services ORDER BY display_order ASC, id ASC")->fetchAll();

// Get service for editing if ID is provided
$edit_service = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_service = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $edit_service->execute([$edit_id]);
    $edit_service = $edit_service->fetch();
}

// Common FontAwesome icons for services
$common_icons = [
    'fas fa-home' => 'Home',
    'fas fa-file-certificate' => 'Certificate',
    'fas fa-handshake' => 'Handshake',
    'fas fa-search' => 'Search',
    'fas fa-calculator' => 'Calculator',
    'fas fa-cog' => 'Settings',
    'fas fa-tools' => 'Tools',
    'fas fa-users' => 'Users',
    'fas fa-map-marked-alt' => 'Map',
    'fas fa-clipboard-list' => 'List',
    'fas fa-shield-alt' => 'Shield',
    'fas fa-chart-line' => 'Chart',
    'fas fa-key' => 'Key',
    'fas fa-building' => 'Building'
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
                        <a href="services_management.php" class="nav-link active" aria-current="page">
                            <i class="fas fa-tools me-2"></i>Services
                        </a>
                    </li>
                    <li>
                        <a href="company_stats.php" class="nav-link text-dark">
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
                    <h1 class="h2 text-primary">Services Management</h1>
                    <a href="../index.php#services" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Preview Services
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
                    <div class="col-lg-5 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-plus me-2"></i><?= $edit_service ? 'Edit Service' : 'Add New Service' ?>
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="<?= $edit_service ? 'edit' : 'add' ?>">
                                    <?php if ($edit_service): ?>
                                        <input type="hidden" name="id" value="<?= $edit_service['id'] ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label">Service Title</label>
                                        <input type="text" name="title" class="form-control" 
                                               value="<?= htmlspecialchars($edit_service['title'] ?? '') ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($edit_service['description'] ?? '') ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Icon</label>
                                        <select name="icon" class="form-control" required>
                                            <?php foreach ($common_icons as $icon_class => $icon_name): ?>
                                                <option value="<?= $icon_class ?>" 
                                                        <?= ($edit_service['icon'] ?? '') === $icon_class ? 'selected' : '' ?>>
                                                    <?= $icon_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Service Features</label>
                                        <div id="features-container">
                                            <?php 
                                            $features = $edit_service ? json_decode($edit_service['features'], true) : [''];
                                            if (empty($features)) $features = [''];
                                            foreach ($features as $index => $feature): 
                                            ?>
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text"><i class="fas fa-check text-success"></i></span>
                                                    <input type="text" name="features[]" class="form-control" 
                                                           value="<?= htmlspecialchars($feature) ?>" placeholder="Feature description">
                                                    <button type="button" class="btn btn-outline-danger remove-feature" onclick="removeFeature(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFeature()">
                                            <i class="fas fa-plus me-2"></i>Add Feature
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Display Order</label>
                                                <input type="number" name="display_order" class="form-control" 
                                                       value="<?= $edit_service['display_order'] ?? 0 ?>" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check mt-4">
                                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                                           <?= ($edit_service['is_active'] ?? 1) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="is_active">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i><?= $edit_service ? 'Update Service' : 'Add Service' ?>
                                    </button>
                                    
                                    <?php if ($edit_service): ?>
                                        <a href="services_management.php" class="btn btn-secondary w-100 mt-2">
                                            <i class="fas fa-times me-2"></i>Cancel Edit
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="col-lg-7">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-list me-2"></i>Current Services (<?= count($services) ?>)
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($services)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No services created yet</h5>
                                        <p class="text-muted">Add your first service using the form on the left.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($services as $service): ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100 <?= $service['is_active'] ? '' : 'bg-light' ?>">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div class="text-center">
                                                                <i class="<?= htmlspecialchars($service['icon']) ?> fa-2x text-primary mb-2"></i>
                                                            </div>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item" href="services_management.php?edit=<?= $service['id'] ?>">
                                                                            <i class="fas fa-edit me-2"></i>Edit
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <form method="POST" class="d-inline">
                                                                            <input type="hidden" name="action" value="toggle_status">
                                                                            <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                                                            <button type="submit" class="dropdown-item">
                                                                                <i class="fas fa-toggle-<?= $service['is_active'] ? 'off' : 'on' ?> me-2"></i>
                                                                                <?= $service['is_active'] ? 'Deactivate' : 'Activate' ?>
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this service?')">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                                                            <button type="submit" class="dropdown-item text-danger">
                                                                                <i class="fas fa-trash me-2"></i>Delete
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                        <h6 class="card-title"><?= htmlspecialchars($service['title']) ?></h6>
                                                        <p class="card-text small"><?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...</p>
                                                        
                                                        <?php 
                                                        $features = json_decode($service['features'], true);
                                                        if ($features && count($features) > 0):
                                                        ?>
                                                            <ul class="list-unstyled small">
                                                                <?php foreach (array_slice($features, 0, 2) as $feature): ?>
                                                                    <li><i class="fas fa-check text-success me-1"></i><?= htmlspecialchars($feature) ?></li>
                                                                <?php endforeach; ?>
                                                                <?php if (count($features) > 2): ?>
                                                                    <li class="text-muted">+<?= count($features) - 2 ?> more...</li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                        
                                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                                            <small class="text-muted">Order: <?= $service['display_order'] ?></small>
                                                            <span class="badge <?= $service['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                                <?= $service['is_active'] ? 'Active' : 'Inactive' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
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
function addFeature() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <span class="input-group-text"><i class="fas fa-check text-success"></i></span>
        <input type="text" name="features[]" class="form-control" placeholder="Feature description">
        <button type="button" class="btn btn-outline-danger remove-feature" onclick="removeFeature(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeFeature(button) {
    const container = document.getElementById('features-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

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
