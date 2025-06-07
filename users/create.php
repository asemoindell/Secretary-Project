<?php
include '../includes/header.php';
require_once '../includes/db.php';

$reference_id = '';
$type = '';
$surname = $firstname = $middlename = $local_govt = $country = '';
$errors = [];
$profile_picture_name = '';
$tdp_file_names = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['type'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $local_govt = trim($_POST['local_govt'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $reference_id = trim($_POST['reference_id'] ?? '');
    if (!$reference_id) {
        $reference_id = strtoupper(bin2hex(random_bytes(4)));
    }
    $profile_picture = $_FILES['profile_picture'] ?? null;
    $tdp_file = $_FILES['tdp_file'] ?? null;

    // Validate
    if (!$type) $errors[] = 'Registration type is required.';
    if (!$surname) $errors[] = 'Surname is required.';
    if (!$firstname) $errors[] = 'First name is required.';
    if (!$local_govt) $errors[] = 'Local government is required.';
    if (!$country) $errors[] = 'Country is required.';

    // Handle file uploads
    $upload_dir = '../assets/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if ($profile_picture && $profile_picture['tmp_name']) {
        $profile_picture_name = uniqid() . '_' . basename($profile_picture['name']);
        move_uploaded_file($profile_picture['tmp_name'], $upload_dir . $profile_picture_name);
    }
    if (isset($_FILES['tdp_file']) && is_array($_FILES['tdp_file']['name'])) {
        foreach ($_FILES['tdp_file']['name'] as $i => $name) {
            if (!empty($_FILES['tdp_file']['tmp_name'][$i])) {
                $unique_name = uniqid() . '_' . basename($name);
                move_uploaded_file($_FILES['tdp_file']['tmp_name'][$i], $upload_dir . $unique_name);
                $tdp_file_names[] = $unique_name;
            }
        }
    }

    if (!$errors) {
        $fullname = trim($firstname . ' ' . $middlename . ' ' . $surname);
        $tdp_file_json = json_encode($tdp_file_names);
        $stmt = $pdo->prepare('INSERT INTO users (reference_id, type, surname, firstname, middlename, local_govt, country, fullname, profile_picture, document_file, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$reference_id, $type, $surname, $firstname, $middlename, $local_govt, $country, $fullname, $profile_picture_name, $tdp_file_json]);
        $success = 'Registration successful! Reference ID: ' . $reference_id;
        // Show preview after upload
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">New Registration</h2>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-info mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Total Registrations</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $total_reg = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
                        echo $total_reg;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Consent</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $consent = $pdo->query("SELECT COUNT(*) FROM users WHERE type = 'consent'")->fetchColumn();
                        echo $consent;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Survey</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $survey = $pdo->query("SELECT COUNT(*) FROM users WHERE type = 'survey'")->fetchColumn();
                        echo $survey;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Building Plan</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $building_plan = $pdo->query("SELECT COUNT(*) FROM users WHERE type = 'building_plan'")->fetchColumn();
                        echo $building_plan;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-secondary mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">C of O</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $c_of_o = $pdo->query("SELECT COUNT(*) FROM users WHERE type = 'c_of_o'")->fetchColumn();
                        echo $c_of_o;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-dark mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Land Document</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $land_doc = $pdo->query("SELECT COUNT(*) FROM users WHERE type = 'land_document'")->fetchColumn();
                        echo $land_doc;
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"> <?= $success ?> </div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Registration Type</label>
            <select name="type" class="form-select" required>
                <option value="">Select Type</option>
                <option value="consent" <?= $type==='consent'?'selected':''; ?>>Consent</option>
                <option value="survey" <?= $type==='survey'?'selected':''; ?>>Survey</option>
                <option value="building_plan" <?= $type==='building_plan'?'selected':''; ?>>Building Plan</option>
                <option value="c_of_o" <?= $type==='c_of_o'?'selected':''; ?>>C of O</option>
                <option value="land_document" <?= $type==='land_document'?'selected':''; ?>>Registration of Land Document</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Reference Number</label>
            <input type="text" name="reference_id" class="form-control" value="<?= htmlspecialchars($reference_id); ?>" placeholder="Leave blank for auto-generate">
        </div>
        <div class="mb-3">
            <label class="form-label">Surname</label>
            <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($surname); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($firstname); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middlename" class="form-control" value="<?= htmlspecialchars($middlename); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Local Government</label>
            <input type="text" name="local_govt" class="form-control" value="<?= htmlspecialchars($local_govt); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($country); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Passport Photo</label>
            <input type="file" name="profile_picture" class="form-control" id="profile_picture_input">
            <div id="profile_picture_preview" class="mt-2"></div>
            <?php if ($profile_picture_name): ?>
                <div class="mt-2">
                    <img src="../assets/uploads/<?= htmlspecialchars($profile_picture_name); ?>" alt="Profile Preview" width="80" class="rounded border">
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload TDP/Survey Documents <span class="text-muted">(You can select multiple files)</span></label>
            <input type="file" name="tdp_file[]" class="form-control" id="tdp_file_input" multiple>
            <div id="tdp_file_preview" class="mt-2 d-flex flex-wrap gap-2"></div>
            <?php if ($tdp_file_names): ?>
                <div class="mt-2">
                    <?php foreach ($tdp_file_names as $file_name): ?>
                        <a href="../assets/uploads/<?= htmlspecialchars($file_name); ?>" target="_blank" class="btn btn-link">View Uploaded Document</a>
                        <?php
                        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp','pdf'])): ?>
                            <?php if ($ext === 'pdf'): ?>
                                <embed src="../assets/uploads/<?= htmlspecialchars($file_name); ?>" type="application/pdf" width="100%" height="400px" />
                            <?php else: ?>
                                <img src="../assets/uploads/<?= htmlspecialchars($file_name); ?>" alt="Document Preview" width="120" class="border rounded mt-2">
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <button class="btn btn-primary">Register</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('tdp_file_input');
    const previewDiv = document.getElementById('tdp_file_preview');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            previewDiv.innerHTML = '';
            const files = Array.from(e.target.files);
            if (!files.length) return;
            files.forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase();
                if (["jpg","jpeg","png","gif","bmp","webp"].includes(ext)) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const img = document.createElement('img');
                        img.src = ev.target.result;
                        img.alt = 'Preview';
                        img.width = 80;
                        img.className = 'border rounded mt-2';
                        previewDiv.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else if (ext === 'pdf') {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const embed = document.createElement('embed');
                        embed.src = ev.target.result;
                        embed.type = 'application/pdf';
                        embed.width = 80;
                        embed.height = 100;
                        embed.className = 'border rounded mt-2';
                        previewDiv.appendChild(embed);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const span = document.createElement('span');
                    span.className = 'text-danger';
                    span.textContent = 'Preview not available for this file type.';
                    previewDiv.appendChild(span);
                }
            });
        });
    }
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
<?php include '../includes/footer.php'; ?>

