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
    $company_name = trim($_POST['company_name']);
    $tagline = trim($_POST['tagline']);
    $about_title = trim($_POST['about_title']);
    $about_content = trim($_POST['about_content']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $working_hours = trim($_POST['working_hours']);
    $facebook_url = trim($_POST['facebook_url']);
    $twitter_url = trim($_POST['twitter_url']);
    $instagram_url = trim($_POST['instagram_url']);
    $linkedin_url = trim($_POST['linkedin_url']);

    try {
        // Check if company info exists
        $existing = $pdo->query("SELECT id FROM company_info LIMIT 1")->fetch();
        
        if ($existing) {
            // Update existing record
            $stmt = $pdo->prepare("UPDATE company_info SET 
                company_name = ?, tagline = ?, about_title = ?, about_content = ?, 
                phone = ?, email = ?, address = ?, working_hours = ?, 
                facebook_url = ?, twitter_url = ?, instagram_url = ?, linkedin_url = ?
                WHERE id = ?");
            $stmt->execute([
                $company_name, $tagline, $about_title, $about_content,
                $phone, $email, $address, $working_hours,
                $facebook_url, $twitter_url, $instagram_url, $linkedin_url,
                $existing['id']
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("INSERT INTO company_info 
                (company_name, tagline, about_title, about_content, phone, email, address, working_hours, facebook_url, twitter_url, instagram_url, linkedin_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $company_name, $tagline, $about_title, $about_content,
                $phone, $email, $address, $working_hours,
                $facebook_url, $twitter_url, $instagram_url, $linkedin_url
            ]);
        }
        
        $success = 'Company information updated successfully!';
    } catch (PDOException $e) {
        $error = 'Error updating company information: ' . $e->getMessage();
    }
}

// Get current company info
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
                        <a href="cms_dashboard.php" class="nav-link text-dark">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="company_settings.php" class="nav-link active" aria-current="page">
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
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="container-fluid py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 text-primary">Company Information</h1>
                    <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Preview Changes
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

                <form method="POST" action="">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-info-circle me-2"></i>Basic Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" name="company_name" class="form-control" 
                                                   value="<?= htmlspecialchars($company_info['company_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tagline</label>
                                            <input type="text" name="tagline" class="form-control" 
                                                   value="<?= htmlspecialchars($company_info['tagline'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">About Section Title</label>
                                        <input type="text" name="about_title" class="form-control" 
                                               value="<?= htmlspecialchars($company_info['about_title'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">About Content</label>
                                        <textarea name="about_content" class="form-control" rows="8" required><?= htmlspecialchars($company_info['about_content'] ?? '') ?></textarea>
                                        <div class="form-text">Use \n\n for new paragraphs</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-phone me-2"></i>Contact Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone Numbers</label>
                                            <input type="text" name="phone" class="form-control" 
                                                   value="<?= htmlspecialchars($company_info['phone'] ?? '') ?>"
                                                   placeholder="+234 901 234 5678, +234 802 345 6789">
                                            <div class="form-text">Separate multiple numbers with commas</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Addresses</label>
                                            <input type="text" name="email" class="form-control" 
                                                   value="<?= htmlspecialchars($company_info['email'] ?? '') ?>"
                                                   placeholder="info@company.com, sales@company.com">
                                            <div class="form-text">Separate multiple emails with commas</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($company_info['address'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Working Hours</label>
                                        <textarea name="working_hours" class="form-control" rows="3"><?= htmlspecialchars($company_info['working_hours'] ?? '') ?></textarea>
                                        <div class="form-text">Use line breaks for different days</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-share-alt me-2"></i>Social Media Links
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fab fa-facebook text-primary me-2"></i>Facebook URL
                                        </label>
                                        <input type="url" name="facebook_url" class="form-control" 
                                               value="<?= htmlspecialchars($company_info['facebook_url'] ?? '') ?>"
                                               placeholder="https://facebook.com/yourpage">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fab fa-twitter text-info me-2"></i>Twitter URL
                                        </label>
                                        <input type="url" name="twitter_url" class="form-control" 
                                               value="<?= htmlspecialchars($company_info['twitter_url'] ?? '') ?>"
                                               placeholder="https://twitter.com/yourhandle">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fab fa-instagram text-danger me-2"></i>Instagram URL
                                        </label>
                                        <input type="url" name="instagram_url" class="form-control" 
                                               value="<?= htmlspecialchars($company_info['instagram_url'] ?? '') ?>"
                                               placeholder="https://instagram.com/yourhandle">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fab fa-linkedin text-primary me-2"></i>LinkedIn URL
                                        </label>
                                        <input type="url" name="linkedin_url" class="form-control" 
                                               value="<?= htmlspecialchars($company_info['linkedin_url'] ?? '') ?>"
                                               placeholder="https://linkedin.com/company/yourcompany">
                                    </div>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="card shadow">
                                <div class="card-body text-center">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                    <div class="mt-2">
                                        <small class="text-muted">Changes will be visible immediately on your website</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
