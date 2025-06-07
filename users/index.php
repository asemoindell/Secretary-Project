<?php
include '../includes/header.php';
require_once '../includes/db.php';

// Fetch all users
$users = $pdo->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();

// Fetch all reference IDs for merging
$ref_stmt = $pdo->query('SELECT DISTINCT reference_id, full_name FROM reports WHERE reference_id IS NOT NULL AND reference_id != "" ORDER BY full_name');
$ref_ids = $ref_stmt->fetchAll();

// Handle merge action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['merge_from'], $_POST['merge_to'])) {
    $merge_from = trim($_POST['merge_from']);
    $merge_to = trim($_POST['merge_to']);
    if ($merge_from && $merge_to && $merge_from !== $merge_to) {
        // Merge all documents from merge_from to merge_to
        $stmt = $pdo->prepare('SELECT id, documents FROM reports WHERE reference_id = ?');
        $stmt->execute([$merge_from]);
        $from_reports = $stmt->fetchAll();
        $stmt = $pdo->prepare('SELECT id, documents FROM reports WHERE reference_id = ?');
        $stmt->execute([$merge_to]);
        $to_reports = $stmt->fetchAll();
        // Collect all documents
        $all_docs = [];
        foreach ($from_reports as $fr) {
            $docs = json_decode($fr['documents'] ?? '', true);
            if ($docs && is_array($docs)) $all_docs = array_merge($all_docs, $docs);
        }
        foreach ($to_reports as $tr) {
            $docs = json_decode($tr['documents'] ?? '', true);
            if ($docs && is_array($docs)) $all_docs = array_merge($all_docs, $docs);
        }
        $all_docs = array_unique($all_docs);
        // Update all reports with merge_to reference_id to have merged documents
        $stmt = $pdo->prepare('UPDATE reports SET documents = ? WHERE reference_id = ?');
        $stmt->execute([json_encode($all_docs), $merge_to]);
        // Optionally, update all reports with merge_from to merge_to
        $stmt = $pdo->prepare('UPDATE reports SET reference_id = ? WHERE reference_id = ?');
        $stmt->execute([$merge_to, $merge_from]);
        $success = 'Merged all documents from ' . htmlspecialchars($merge_from) . ' to ' . htmlspecialchars($merge_to);
    } else {
        $error = 'Invalid merge selection.';
    }
}
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Users</h2>
    <a href="create.php" class="btn btn-success mb-3">Add New User</a>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Profile Picture</th>
                    <th>Surname</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Document</th>
                    <th>Type</th>
                    <th>Reference ID(s)</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $i => $user): ?>
                    <tr>
                        <td><?= $i + 1; ?></td>
                        <td>
                            <?php if ($user['profile_picture']): ?>
                                <img src="../assets/uploads/<?= htmlspecialchars($user['profile_picture']); ?>" alt="Profile" width="40" height="40" class="rounded-circle border">
                            <?php else: ?>
                                <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user['surname']); ?></td>
                        <td><?= htmlspecialchars($user['firstname']); ?></td>
                        <td><?= htmlspecialchars($user['middlename']); ?></td>
                        <td>
                            <?php if (!empty($user['document_file'])): ?>
                                <a href="../assets/uploads/<?= htmlspecialchars($user['document_file']); ?>" target="_blank" class="btn btn-link btn-sm">View Document</a>
                                <?php
                                $ext = strtolower(pathinfo($user['document_file'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])): ?>
                                    <div class="mt-2"><img src="../assets/uploads/<?= htmlspecialchars($user['document_file']); ?>" alt="Document Preview" width="80" class="border rounded"></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">No Document</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user['type']); ?></td>
                        <td>
                            <?php
                            $refs = array_filter($ref_ids, function($r) use ($user) {
                                return strtolower(trim($r['full_name'])) === strtolower(trim($user['surname'] . ' ' . $user['firstname'] . ' ' . $user['middlename']));
                            });
                            if ($refs) {
                                foreach ($refs as $ref) {
                                    echo '<span class="badge bg-secondary">' . htmlspecialchars($ref['reference_id']) . '</span> ';
                                }
                            } else {
                                echo '<span class="text-muted">-</span>';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h5>Merge Documents by Reference ID</h5>
        <?php if (!empty($success)): ?><div class="alert alert-success"> <?= $success ?> </div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger"> <?= $error ?> </div><?php endif; ?>
        <form method="POST" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Merge From (Reference ID)</label>
                <select name="merge_from" class="form-select" required>
                    <option value="">Select Reference ID</option>
                    <?php foreach ($ref_ids as $ref): ?>
                        <option value="<?= htmlspecialchars($ref['reference_id']) ?>"><?= htmlspecialchars($ref['reference_id']) ?> (<?= htmlspecialchars($ref['full_name']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Merge To (Reference ID)</label>
                <select name="merge_to" class="form-select" required>
                    <option value="">Select Reference ID</option>
                    <?php foreach ($ref_ids as $ref): ?>
                        <option value="<?= htmlspecialchars($ref['reference_id']) ?>"><?= htmlspecialchars($ref['reference_id']) ?> (<?= htmlspecialchars($ref['full_name']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Merge</button>
            </div>
        </form>
    </div>

    <h2 class="mb-4 fw-bold text-primary">User List</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Reference ID</th>
                <th>Full Name</th>
                <th>Type</th>
                <th>Documents</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td>
                    <?php
                    // Always show the user's own reference_id
                    echo '<span class="badge bg-primary">' . htmlspecialchars($user['reference_id']) . '</span> ';
                    // Also show any associated report reference IDs (if different)
                    $refs = array_filter($ref_ids, function($r) use ($user) {
                        return strtolower(trim($r['full_name'])) === strtolower(trim($user['surname'] . ' ' . $user['firstname'] . ' ' . $user['middlename'])) && $r['reference_id'] !== $user['reference_id'];
                    });
                    if ($refs) {
                        foreach ($refs as $ref) {
                            echo '<span class="badge bg-secondary">' . htmlspecialchars($ref['reference_id']) . '</span> ';
                        }
                    }
                    ?>
                </td>
                <td><?= htmlspecialchars($user['fullname']) ?></td>
                <td><?= htmlspecialchars($user['type'] ?? '') ?></td>
                <td>
                    <?php
                    // Merge and display all document files for this user
                    if (!empty($user['document_file'])) {
                        $docs = json_decode($user['document_file'], true);
                        if (is_array($docs)) {
                            foreach ($docs as $doc) {
                                $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                $url = (strpos($doc, '../') === 0) ? substr($doc, 2) : $doc;
                                echo '<a href="' . htmlspecialchars($url) . '" target="_blank">View</a> ';
                                if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])) {
                                    echo '<img src="' . htmlspecialchars($url) . '" alt="Doc" width="40" class="border rounded ms-1"> ';
                                } elseif ($ext === 'pdf') {
                                    echo '<span class="badge bg-secondary ms-1">PDF</span> ';
                                }
                            }
                        } else {
                            // Single file fallback
                            $ext = strtolower(pathinfo($user['document_file'], PATHINFO_EXTENSION));
                            $url = (strpos($user['document_file'], '../') === 0) ? substr($user['document_file'], 2) : $user['document_file'];
                            echo '<a href="' . htmlspecialchars($url) . '" target="_blank">View</a> ';
                            if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])) {
                                echo '<img src="' . htmlspecialchars($url) . '" alt="Doc" width="40" class="border rounded ms-1"> ';
                            } elseif ($ext === 'pdf') {
                                echo '<span class="badge bg-secondary ms-1">PDF</span> ';
                            }
                        }
                    } else {
                        echo '<span class="text-muted">No documents</span>';
                    }
                    ?>
                </td>
                <td>
                    <!-- ...existing actions... -->
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
