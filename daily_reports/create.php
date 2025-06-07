<?php
include '../includes/header.php';
require_once '../includes/db.php';

// Handle form submission
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $edl_registration_number = trim($_POST['edl_registration_number']);
    $properties = trim($_POST['properties']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $land_size = trim($_POST['land_size']);
    $community = trim($_POST['community']);
    $status = trim($_POST['status']);
    $document_collected = trim($_POST['document_collected']);
    $other = trim($_POST['other']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Handle passport upload
    $passport_upload = '';
    if (isset($_FILES['passport_upload']) && $_FILES['passport_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['passport_upload']['name']);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['passport_upload']['tmp_name'], $target)) {
            $passport_upload = $filename;
        }
    }

    // Handle multiple document uploads
    $documents = [];
    if (!empty($_FILES['documents']['name'][0])) {
        $base_dir = '../assets/uploads/reports/';
        if (!is_dir($base_dir)) mkdir($base_dir, 0777, true);
        $person_dir = $base_dir . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $full_name) . '/';
        if (!is_dir($person_dir)) mkdir($person_dir, 0777, true);
        $date_dir = $person_dir . date('Y') . '_' . date('md') . '/';
        if (!is_dir($date_dir)) mkdir($date_dir, 0777, true);
        foreach ($_FILES['documents']['name'] as $i => $doc_name) {
            if ($_FILES['documents']['error'][$i] === UPLOAD_ERR_OK) {
                $safe_name = uniqid() . '_' . basename($doc_name);
                $target = $date_dir . $safe_name;
                if (move_uploaded_file($_FILES['documents']['tmp_name'][$i], $target)) {
                    $documents[] = $target;
                }
            }
        }
    }
    $documents_json = json_encode($documents);

    // Generate unique alphanumeric reference ID
    $reference_id = trim($_POST['reference_id'] ?? '');
    if (!$reference_id) {
        $reference_id = strtoupper(bin2hex(random_bytes(4)));
    }

    if ($full_name && $title && $content) {
        $stmt = $pdo->prepare('INSERT INTO reports (reference_id, full_name, edl_registration_number, properties, phone, address, land_size, passport_upload, community, status, document_collected, other, title, content, documents, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        try {
            $stmt->execute([$reference_id, $full_name, $edl_registration_number, $properties, $phone, $address, $land_size, $passport_upload, $community, $status, $document_collected, $other, $title, $content, $documents_json]);
            $success = 'Report added successfully! Reference ID: ' . $reference_id;
        } catch (PDOException $e) {
            $error = 'Error adding report: ' . $e->getMessage();
        }
    } else {
        $error = 'Full Name, Title, and Content are required.';
    }
}
?>
<div class="container py-4">
    <h1 class="mb-4 fw-bold text-primary">Add Report</h1>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Reference Number</label>
            <input type="text" name="reference_id" class="form-control" value="<?= htmlspecialchars($reference_id ?? '') ?>" placeholder="Leave blank for auto-generate">
        </div>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">EDL / Registration Number</label>
            <input type="text" name="edl_registration_number" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Properties</label>
            <input type="text" name="properties" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Land Size</label>
            <input type="text" name="land_size" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Passport (for easy identification)</label>
            <input type="file" name="passport_upload" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Community</label>
            <input type="text" name="community" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Status (enter 'C of O collected' if applicable)</label>
            <input type="text" name="status" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Document Collected</label>
            <input type="text" name="document_collected" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Other</label>
            <textarea name="other" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Report Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Report Content</label>
            <textarea name="content" class="form-control" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Documents <span class="text-muted">(You can select multiple files)</span></label>
            <input type="file" name="documents[]" class="form-control" id="documents_input" multiple>
            <div id="documents_preview" class="mt-2 d-flex flex-wrap gap-2"></div>
        </div>
        <button class="btn btn-success">Add Report</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Document preview for daily reports
    const docsInput = document.getElementById('documents_input');
    const docsPreview = document.getElementById('documents_preview');
    if (docsInput) {
        docsInput.addEventListener('change', function(e) {
            docsPreview.innerHTML = '';
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
                        docsPreview.appendChild(img);
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
                        docsPreview.appendChild(embed);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const span = document.createElement('span');
                    span.className = 'text-danger';
                    span.textContent = 'Preview not available for this file type.';
                    docsPreview.appendChild(span);
                }
            });
        });
    }
});
</script>
<?php include '../includes/footer.php'; ?>
