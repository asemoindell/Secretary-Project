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

// Handle form submission for adding/editing slides
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
            case 'edit':
                $title = trim($_POST['title']);
                $subtitle = trim($_POST['subtitle']);
                $button_text = trim($_POST['button_text']);
                $button_link = trim($_POST['button_link']);
                $background_gradient = trim($_POST['background_gradient']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $display_order = (int)$_POST['display_order'];

                try {
                    if ($_POST['action'] === 'add') {
                        $stmt = $pdo->prepare("INSERT INTO hero_slides (title, subtitle, button_text, button_link, background_gradient, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $subtitle, $button_text, $button_link, $background_gradient, $is_active, $display_order]);
                        $success = 'Hero slide added successfully!';
                    } else {
                        $id = (int)$_POST['id'];
                        $stmt = $pdo->prepare("UPDATE hero_slides SET title = ?, subtitle = ?, button_text = ?, button_link = ?, background_gradient = ?, is_active = ?, display_order = ? WHERE id = ?");
                        $stmt->execute([$title, $subtitle, $button_text, $button_link, $background_gradient, $is_active, $display_order, $id]);
                        $success = 'Hero slide updated successfully!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error saving hero slide: ' . $e->getMessage();
                }
                break;

            case 'delete':
                $id = (int)$_POST['id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Hero slide deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting hero slide: ' . $e->getMessage();
                }
                break;

            case 'toggle_status':
                $id = (int)$_POST['id'];
                try {
                    $stmt = $pdo->prepare("UPDATE hero_slides SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Hero slide status updated!';
                } catch (PDOException $e) {
                    $error = 'Error updating status: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all hero slides
$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll();

// Get slide for editing if ID is provided
$edit_slide = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_slide = $pdo->prepare("SELECT * FROM hero_slides WHERE id = ?");
    $edit_slide->execute([$edit_id]);
    $edit_slide = $edit_slide->fetch();
}
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
                        <a href="hero_slides.php" class="nav-link active" aria-current="page">
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
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="container-fluid py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 text-primary">Hero Slides Management</h1>
                    <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Preview Slides
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
                                    <i class="fas fa-plus me-2"></i><?= $edit_slide ? 'Edit Slide' : 'Add New Slide' ?>
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="<?= $edit_slide ? 'edit' : 'add' ?>">
                                    <?php if ($edit_slide): ?>
                                        <input type="hidden" name="id" value="<?= $edit_slide['id'] ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" class="form-control" 
                                               value="<?= htmlspecialchars($edit_slide['title'] ?? '') ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Subtitle</label>
                                        <textarea name="subtitle" class="form-control" rows="3"><?= htmlspecialchars($edit_slide['subtitle'] ?? '') ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Button Text</label>
                                        <input type="text" name="button_text" class="form-control" 
                                               value="<?= htmlspecialchars($edit_slide['button_text'] ?? '') ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Button Link</label>
                                        <input type="text" name="button_link" class="form-control" 
                                               value="<?= htmlspecialchars($edit_slide['button_link'] ?? '') ?>"
                                               placeholder="#services, #contact, #about">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Background Gradient</label>
                                        <select name="background_gradient" class="form-control">
                                            <option value="linear-gradient(135deg, #667eea 0%, #764ba2 100%)" 
                                                    <?= ($edit_slide['background_gradient'] ?? '') === 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' ? 'selected' : '' ?>>
                                                Purple Blue
                                            </option>
                                            <option value="linear-gradient(135deg, #2c5530 0%, #4a7c59 100%)" 
                                                    <?= ($edit_slide['background_gradient'] ?? '') === 'linear-gradient(135deg, #2c5530 0%, #4a7c59 100%)' ? 'selected' : '' ?>>
                                                Green
                                            </option>
                                            <option value="linear-gradient(135deg, #8360c3 0%, #2ebf91 100%)" 
                                                    <?= ($edit_slide['background_gradient'] ?? '') === 'linear-gradient(135deg, #8360c3 0%, #2ebf91 100%)' ? 'selected' : '' ?>>
                                                Purple Teal
                                            </option>
                                            <option value="linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%)" 
                                                    <?= ($edit_slide['background_gradient'] ?? '') === 'linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%)' ? 'selected' : '' ?>>
                                                Orange
                                            </option>
                                            <option value="linear-gradient(135deg, #6a11cb 0%, #2575fc 100%)" 
                                                    <?= ($edit_slide['background_gradient'] ?? '') === 'linear-gradient(135deg, #6a11cb 0%, #2575fc 100%)' ? 'selected' : '' ?>>
                                                Blue Purple
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Display Order</label>
                                        <input type="number" name="display_order" class="form-control" 
                                               value="<?= $edit_slide['display_order'] ?? 0 ?>" min="0">
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                               <?= ($edit_slide['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i><?= $edit_slide ? 'Update Slide' : 'Add Slide' ?>
                                    </button>
                                    
                                    <?php if ($edit_slide): ?>
                                        <a href="hero_slides.php" class="btn btn-secondary w-100 mt-2">
                                            <i class="fas fa-times me-2"></i>Cancel Edit
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Slides List -->
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-list me-2"></i>Current Slides (<?= count($slides) ?>)
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($slides)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No slides created yet</h5>
                                        <p class="text-muted">Add your first slide using the form on the left.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Title</th>
                                                    <th>Button Text</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($slides as $slide): ?>
                                                    <tr>
                                                        <td><?= $slide['display_order'] ?></td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($slide['title']) ?></strong>
                                                            <?php if ($slide['subtitle']): ?>
                                                                <br><small class="text-muted"><?= htmlspecialchars(substr($slide['subtitle'], 0, 50)) ?>...</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($slide['button_text']) ?></td>
                                                        <td>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="action" value="toggle_status">
                                                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                                                <button type="submit" class="btn btn-sm <?= $slide['is_active'] ? 'btn-success' : 'btn-secondary' ?>">
                                                                    <?= $slide['is_active'] ? 'Active' : 'Inactive' ?>
                                                                </button>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <a href="hero_slides.php?edit=<?= $slide['id'] ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slide?')">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
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
