<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Add more permissions for secretary
$all_permissions = [
    'view_users', 'view_reports', 'new_registration', 'reporting',
    'add_report', 'edit_report', 'update_report', 'delete_report',
    'show_report', 'login_location',
    'create_user', 'edit_user', 'delete_user', 'assign_permissions',
    'view_settings', 'edit_settings', 'view_dashboard', 'export_data',
    'import_data', 'view_statistics', 'manage_documents', 'manage_profile'
];
// Determine user role(s)
$user_role = null;
if (isset($_SESSION['admin_id'])) {
    $user_role = 'admin';
} elseif (isset($_SESSION['secretary_id'])) {
    $user_role = 'secretary';
}
// Fetch secretary permissions if secretary
$secretary_permissions = [];
if ($user_role === 'secretary') {
    require_once '../includes/db.php';
    $stmt = $pdo->prepare('SELECT permissions FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['secretary_id']]);
    $row = $stmt->fetch();
    if ($row && !empty($row['permissions'])) {
        $secretary_permissions = json_decode($row['permissions'], true) ?: [];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <style>
        .side-nav-custom {
            background: #0e0f1e !important;
            color: #fff !important;
        }
        .side-nav-custom .nav-link,
        .side-nav-custom .nav-link:visited {
            color: #fff !important;
        }
        .side-nav-custom .nav-link.active,
        .side-nav-custom .nav-link:hover {
            background: #23244a !important;
            color: #fff !important;
        }
        body.theme-dark {
            background: #181a1b !important;
            color: #fff !important;
        }
        body.theme-light {
            background: #f8f9fa !important;
            color: #212529 !important;
        }
        .theme-toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 2000;
        }
    </style>
    <script>
    // Theme toggle logic
    document.addEventListener('DOMContentLoaded', function() {
        const theme = localStorage.getItem('theme') || 'dark';
        document.body.classList.add('theme-' + theme);

        document.getElementById('themeToggle').addEventListener('click', function() {
            const current = document.body.classList.contains('theme-dark') ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.body.classList.remove('theme-' + current);
            document.body.classList.add('theme-' + next);
            localStorage.setItem('theme', next);
        });
    });
    </script>
</head>
<body class="bg-light">
<button id="themeToggle" class="btn btn-outline-light theme-toggle-btn">Toggle Theme</button>
<?php if ($user_role): ?>
<!-- Sidebar -->
<div class="d-flex flex-column flex-shrink-0 p-3 border-end min-vh-100 side-nav-custom"
     style="width: 220px; position: fixed; top: 0; left: 0; z-index: 1030; overflow-y: auto; max-height: 100vh;">
    <a href="/dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <img src="https://cdn.jsdelivr.net/gh/creativetimofficial/argon-dashboard/assets/img/brand/blue.png" alt="Logo" width="40" class="me-2">
        <span class="fs-4 fw-bold text-primary">Dashboard</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="/project1/dashboard.php" class="nav-link text-dark">Dashboard</a>
        </li>
        <?php if ($user_role === 'admin' || in_array('users', $secretary_permissions)): ?>
        <li>
            <a href="/project1/users/index.php" class="nav-link text-dark">Users</a>
        </li>
        <?php endif; ?>
        <?php if ($user_role === 'admin' || in_array('reports', $secretary_permissions)): ?>
        <li>
            <a href="/project1/daily_reports/index.php" class="nav-link text-dark">Reports</a>
        </li>
        <?php endif; ?>
        <?php if ($user_role === 'admin' || in_array('create_user', $secretary_permissions)): ?>
        <li>
            <a href="/project1/users/create.php" class="nav-link text-dark">New Registration</a>
        </li>
        <?php endif; ?>
        <?php if ($user_role === 'admin' || in_array('add_report', $secretary_permissions)): ?>
        <li>
            <a class="nav-link text-white sidebar-hover" data-bs-toggle="collapse" href="#reportingMenu" role="button" aria-expanded="false" aria-controls="reportingMenu" style="background:#1a237e;">
                Reporting
            </a>
            <div class="collapse ms-3" id="reportingMenu">
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="/project1/daily_reports/create.php" class="nav-link text-dark sidebar-hover">Add Report</a></li>
                    <li class="nav-item"><a href="/project1/daily_reports/edit.php" class="nav-link text-dark sidebar-hover">Edit Report</a></li>
                    <li class="nav-item"><a href="/project1/daily_reports/update.php" class="nav-link text-dark sidebar-hover">Update Report</a></li>
                    <li class="nav-item"><a href="/project1/daily_reports/delete.php" class="nav-link text-dark sidebar-hover">Delete Report</a></li>
                    <li class="nav-item"><a href="/project1/daily_reports/index.php" class="nav-link text-dark sidebar-hover">Show Report</a></li>
                </ul>
            </div>
        </li>
        <?php endif; ?>
        <?php if ($user_role === 'admin' || in_array('delete_report', $secretary_permissions)): ?>
        <li>
            <a href="/project1/daily_reports/delete.php" class="nav-link text-dark">Delete Report</a>
        </li>
        <?php endif; ?>
        <?php if ($user_role === 'admin'): ?>
        <li>
            <a href="/project1/users/assign_permissions.php" class="nav-link text-dark">Assign Secretary Permissions</a>
        </li>
        <?php endif; ?>
        <!-- Basic Setting Menu -->
        <?php if ($user_role === 'admin'): ?>
        <li>
            <a class="nav-link text-white sidebar-hover" data-bs-toggle="collapse" href="#basicSettingMenu" role="button" aria-expanded="false" aria-controls="basicSettingMenu" style="background:#1a237e;">
                Basic Setting
            </a>
            <div class="collapse ms-3" id="basicSettingMenu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="/project1/settings/admin_profile.php" class="nav-link text-dark sidebar-hover">Modify Admin Name</a>
                    </li>
                    <li class="nav-item">
                        <a href="/project1/settings/board_name.php" class="nav-link text-dark sidebar-hover">Change Admin Board Name</a>
                    </li>
                    <li class="nav-item">
                        <a href="/project1/settings/frontend_name.php" class="nav-link text-dark sidebar-hover">Change Frontend/Index Name</a>
                    </li>
                    <li class="nav-item">
                        <a href="/project1/settings/welcome_message.php" class="nav-link text-dark sidebar-hover">Edit Welcome Message</a>
                    </li>
                    <li class="nav-item">
                        <a href="/project1/settings/edit_index.php" class="nav-link text-dark sidebar-hover">Edit Everything on Index</a>
                    </li>
                </ul>
            </div>
        </li>
        <?php endif; ?>
        <li>
            <a href="/project1/auth/logout.php" class="nav-link text-danger">Logout</a>
        </li>
    </ul>
</div>
<!-- End Sidebar -->
<?php endif; ?>
<div class="container mt-4" style="margin-left:<?= $user_role ? '220px' : '0' ?>;">
