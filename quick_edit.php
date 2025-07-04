<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: auth/login.php');
    exit;
}

include 'includes/header.php';
require_once 'includes/db.php';

$action = $_GET['action'] ?? '';
$type = $_GET['type'] ?? '';
$success = '';
$error = '';

// Handle quick updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'quick_update') {
    try {
        switch ($type) {
            case 'company_name':
                $stmt = $pdo->prepare("UPDATE company_info SET company_name = ? WHERE id = 1");
                $stmt->execute([$_POST['value']]);
                $success = 'Company name updated successfully!';
                break;
                
            case 'tagline':
                $stmt = $pdo->prepare("UPDATE company_info SET tagline = ? WHERE id = 1");
                $stmt->execute([$_POST['value']]);
                $success = 'Tagline updated successfully!';
                break;
                
            case 'phone':
                $stmt = $pdo->prepare("UPDATE company_info SET phone = ? WHERE id = 1");
                $stmt->execute([$_POST['value']]);
                $success = 'Phone number updated successfully!';
                break;
                
            case 'email':
                $stmt = $pdo->prepare("UPDATE company_info SET email = ? WHERE id = 1");
                $stmt->execute([$_POST['value']]);
                $success = 'Email updated successfully!';
                break;
        }
    } catch (PDOException $e) {
        $error = 'Error updating: ' . $e->getMessage();
    }
}

// Get current data
$company_info = $pdo->query("SELECT * FROM company_info LIMIT 1")->fetch();
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Quick Website Editor</h2>
        <div>
            <a href="dashboard.php" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <a href="index.php" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt me-2"></i>View Website
            </a>
        </div>
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
        <!-- Quick Edit Forms -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Quick Edit
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Company Name -->
                    <form method="POST" action="?action=quick_update&type=company_name" class="mb-3">
                        <label class="form-label">Company Name</label>
                        <div class="input-group">
                            <input type="text" name="value" class="form-control" 
                                   value="<?= htmlspecialchars($company_info['company_name'] ?? '') ?>" required>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                    <!-- Tagline -->
                    <form method="POST" action="?action=quick_update&type=tagline" class="mb-3">
                        <label class="form-label">Tagline</label>
                        <div class="input-group">
                            <input type="text" name="value" class="form-control" 
                                   value="<?= htmlspecialchars($company_info['tagline'] ?? '') ?>" required>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                    <!-- Phone -->
                    <form method="POST" action="?action=quick_update&type=phone" class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <input type="text" name="value" class="form-control" 
                                   value="<?= htmlspecialchars($company_info['phone'] ?? '') ?>">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                    <!-- Email -->
                    <form method="POST" action="?action=quick_update&type=email">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <input type="text" name="value" class="form-control" 
                                   value="<?= htmlspecialchars($company_info['email'] ?? '') ?>">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Advanced Management -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Advanced Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="cms/company_settings.php" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-building me-2 text-primary"></i>
                                    <strong>Full Company Settings</strong>
                                    <br><small class="text-muted">Complete company information management</small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="cms/hero_slides.php" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-images me-2 text-success"></i>
                                    <strong>Hero Slides</strong>
                                    <br><small class="text-muted">Manage homepage carousel</small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="cms/services_management.php" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tools me-2 text-info"></i>
                                    <strong>Services</strong>
                                    <br><small class="text-muted">Add/edit company services</small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="cms/company_stats.php" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chart-bar me-2 text-warning"></i>
                                    <strong>Statistics</strong>
                                    <br><small class="text-muted">Update company achievements</small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Website Preview -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Live Website Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <iframe src="index.php" width="100%" height="400" class="border rounded"></iframe>
                        </div>
                        <div class="col-md-4">
                            <h6>Current Settings:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Company:</strong> <?= htmlspecialchars($company_info['company_name'] ?? 'Not Set') ?></li>
                                <li><strong>Tagline:</strong> <?= htmlspecialchars($company_info['tagline'] ?? 'Not Set') ?></li>
                                <li><strong>Phone:</strong> <?= htmlspecialchars($company_info['phone'] ?? 'Not Set') ?></li>
                                <li><strong>Email:</strong> <?= htmlspecialchars($company_info['email'] ?? 'Not Set') ?></li>
                            </ul>
                            
                            <hr>
                            
                            <div class="d-grid gap-2">
                                <a href="index.php" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>Open Full Website
                                </a>
                                <a href="cms/cms_dashboard.php" class="btn btn-outline-success">
                                    <i class="fas fa-tachometer-alt me-2"></i>Full CMS Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
