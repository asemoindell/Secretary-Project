<?php
include '../includes/header.php';
require_once '../includes/db.php';

$all_permissions = [
    'view_users', 'view_reports', 'new_registration', 'reporting',
    'add_report', 'edit_report', 'update_report', 'delete_report',
    'show_report', 'login_location'
];

// Add variables for multiple TDP/Survey document upload
$tdp_file_names = [];
$profile_picture_name = '';

$success = $error = '';
$errors = [];
$upload_dir = __DIR__ . '/../assets/uploads/';
$upload_dir_web = '../assets/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $permissions = json_encode($all_permissions);

    $reference_id = uniqid('ref_');
    $surname = isset($_POST['surname']) ? trim($_POST['surname']) : '';
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $middlename = isset($_POST['middlename']) ? trim($_POST['middlename']) : '';
    $local_govt = isset($_POST['local_govt']) ? trim($_POST['local_govt']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';

    $profile_picture = $_FILES['profile_picture'] ?? null;
    $tdp_file = $_FILES['tdp_file'] ?? null;

    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $error = "Username already exists.";
    } else {
        // Handle profile picture upload
        if ($profile_picture && $profile_picture['tmp_name']) {
            $profile_picture_name = uniqid() . '_' . basename($profile_picture['name']);
            if (!move_uploaded_file($profile_picture['tmp_name'], $upload_dir . $profile_picture_name)) {
                $errors[] = 'Failed to upload profile picture.';
            }
        }
        // Handle multiple TDP/Survey document uploads
        if (isset($_FILES['tdp_file']) && is_array($_FILES['tdp_file']['name'])) {
            foreach ($_FILES['tdp_file']['name'] as $i => $name) {
                if (!empty($_FILES['tdp_file']['tmp_name'][$i])) {
                    $unique_name = uniqid() . '_' . basename($name);
                    if (!move_uploaded_file($_FILES['tdp_file']['tmp_name'][$i], $upload_dir . $unique_name)) {
                        $errors[] = 'Failed to upload document: ' . htmlspecialchars($name);
                    } else {
                        $tdp_file_names[] = $unique_name;
                    }
                }
            }
        }
        if (!$errors) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $fullname = trim($firstname . ' ' . $middlename . ' ' . $surname);
            $tdp_file_json = json_encode($tdp_file_names);
            $stmt = $pdo->prepare('INSERT INTO users (reference_id, surname, firstname, middlename, local_govt, country, fullname, username, password, profile_picture, document_file, role, type, permissions, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$reference_id, $surname, $firstname, $middlename, $local_govt, $country, $fullname, $username, $hashed_password, $profile_picture_name, $tdp_file_json, $role, $role, $permissions]);
            $success = 'Admin registration successful! Reference ID: ' . $reference_id;
        }
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Add New User</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3" enctype="multipart/form-data">
        <div class="col-md-4">
            <label class="form-label">Surname</label>
            <input type="text" name="surname" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" name="firstname" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middlename" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Local Govt</label>
            <input type="text" name="local_govt" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="Ade" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password</label>
            <input type="text" name="password" class="form-control" value="ade@123" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="secretary">Secretary</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Passport Photo</label>
            <input type="file" name="profile_picture" class="form-control" id="profile_picture_input">
            <div id="profile_picture_preview" class="mt-2"></div>
            <?php if ($profile_picture_name): ?>
                <div class="mt-2">
                    <img src="<?= $upload_dir_web . htmlspecialchars($profile_picture_name); ?>" alt="Profile Preview" width="80" class="rounded border">
                </div>
            <?php endif; ?>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Create User</button>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Passport photo preview
    const passportInput = document.getElementById('profile_picture_input');
    const passportPreview = document.getElementById('profile_picture_preview');
    if (passportInput) {
        passportInput.addEventListener('change', function(e) {
            passportPreview.innerHTML = '';
            const file = e.target.files[0];
            if (!file) return;
            const ext = file.name.split('.').pop().toLowerCase();
            if (["jpg","jpeg","png","gif","bmp","webp"].includes(ext)) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    passportPreview.innerHTML = `<img src='${ev.target.result}' alt='Preview' width='80' class='rounded border mt-2'>`;
                };
                reader.readAsDataURL(file);
            } else {
                passportPreview.innerHTML = '<span class="text-danger">Preview not available for this file type.</span>';
            }
        });
    }
});
</script>