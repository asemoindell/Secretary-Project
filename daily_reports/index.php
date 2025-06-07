<?php
include '../includes/header.php';
require_once '../includes/db.php';

// Only show reports if admin or secretary has permission
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$can_view_reports = true;
if (isset($_SESSION['secretary_id']) && !isset($_SESSION['admin_id'])) {
    $secretary_permissions = [];
    $stmt_perm = $pdo->prepare('SELECT permissions FROM users WHERE id = ?');
    $stmt_perm->execute([$_SESSION['secretary_id']]);
    $row = $stmt_perm->fetch();
    if ($row && !empty($row['permissions'])) {
        $secretary_permissions = json_decode($row['permissions'], true) ?: [];
    }
    $can_view_reports = in_array('reports', $secretary_permissions) || in_array('show_report', $secretary_permissions);
}

// Fetch all reports
$stmt = $pdo->query('SELECT id, full_name, title, created_at, updated_at, status, document_collected, other, documents, reference_id FROM reports ORDER BY created_at DESC');
$reports = $stmt->fetchAll();

if ($can_view_reports):
?>
<div class="container py-4">
    <h1 class="mb-4 fw-bold text-primary">All Reports</h1>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-info mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Survey Collected</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $survey_collected = array_filter($reports, function($r) {
                            return stripos($r['document_collected'] ?? '', 'survey') !== false;
                        });
                        echo count($survey_collected);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Land Document Collected</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $land_collected = array_filter($reports, function($r) {
                            return stripos($r['document_collected'] ?? '', 'land') !== false;
                        });
                        echo count($land_collected);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">Submitted Document</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $submitted = array_filter($reports, function($r) {
                            return stripos($r['status'] ?? '', 'submitted') !== false;
                        });
                        echo count($submitted);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3">
                <div class="card-body text-center">
                    <h6 class="card-title">New Registration</h6>
                    <p class="card-text fs-4 fw-bold">
                        <?php
                        $today = date('Y-m-d');
                        $new_reg = array_filter($reports, function($r) use ($today) {
                            return isset($r['created_at']) && strpos($r['created_at'], $today) === 0;
                        });
                        echo count($new_reg);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <form method="get" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search by Name</label>
                <input type="text" name="search_name" class="form-control" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Applied For</label>
                <select name="applied_for" class="form-select">
                    <option value="">All</option>
                    <option value="survey" <?= (($_GET['applied_for'] ?? '') === 'survey') ? 'selected' : '' ?>>Survey</option>
                    <option value="land" <?= (($_GET['applied_for'] ?? '') === 'land') ? 'selected' : '' ?>>Land Document</option>
                    <option value="submitted" <?= (($_GET['applied_for'] ?? '') === 'submitted') ? 'selected' : '' ?>>Submitted Document</option>
                    <option value="new" <?= (($_GET['applied_for'] ?? '') === 'new') ? 'selected' : '' ?>>New Registration</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Title</th>
                <th>Created At</th>
                <th>Last Updated</th>
                <th>C of O Status</th>
                <th>Survey Document Status</th>
                <th>Land Processing Status</th>
                <th>Applied For</th>
                <th>Reference ID</th>
                <th>Documents</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $filtered_reports = $reports;
            if (!empty($_GET['search_name'])) {
                $filtered_reports = array_filter($filtered_reports, function($r) {
                    return stripos($r['full_name'], $_GET['search_name']) !== false;
                });
            }
            if (!empty($_GET['applied_for'])) {
                $applied = $_GET['applied_for'];
                $filtered_reports = array_filter($filtered_reports, function($r) use ($applied, $today) {
                    if ($applied === 'survey') return stripos($r['document_collected'] ?? '', 'survey') !== false;
                    if ($applied === 'land') return stripos($r['document_collected'] ?? '', 'land') !== false;
                    if ($applied === 'submitted') return stripos($r['status'] ?? '', 'submitted') !== false;
                    if ($applied === 'new') return isset($r['created_at']) && strpos($r['created_at'], $today) === 0;
                    return true;
                });
            }
            foreach ($filtered_reports as $report): ?>
                <tr>
                    <td><?= $report['id'] ?></td>
                    <td><?= htmlspecialchars($report['full_name']) ?></td>
                    <td><?= htmlspecialchars($report['title']) ?></td>
                    <td><?= $report['created_at'] ?></td>
                    <td><?= $report['updated_at'] ?? $report['created_at'] ?></td>
                    <td><?= htmlspecialchars($report['status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($report['document_collected'] ?? '') ?></td>
                    <td><?= htmlspecialchars($report['other'] ?? '') ?></td>
                    <td><?= htmlspecialchars($report['reference_id'] ?? '-') ?></td>
                    <td>
                        <?php
                        if (stripos($report['document_collected'] ?? '', 'survey') !== false) echo 'Survey';
                        elseif (stripos($report['document_collected'] ?? '', 'land') !== false) echo 'Land Document';
                        elseif (stripos($report['status'] ?? '', 'submitted') !== false) echo 'Submitted Document';
                        elseif (isset($report['created_at']) && strpos($report['created_at'], $today) === 0) echo 'New Registration';
                        else echo '-';
                        ?>
                    </td>
                    <td>
                        <?php
                        $docs = json_decode($report['documents'] ?? '', true);
                        if ($docs && is_array($docs) && count($docs) > 0): ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($docs as $i => $doc):
                                    $filename = basename($doc);
                                    $relativePath = str_replace('..', '..', $doc);
                                ?>
                                    <li>
                                        <a href="<?= htmlspecialchars($relativePath) ?>" target="_blank" download class="btn btn-link btn-sm p-0">View <?= htmlspecialchars($filename) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <span class="text-muted">No Documents</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $report['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="update.php" class="btn btn-info btn-sm">Update Status</a>
                        <a href="delete.php?id=<?= $report['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="container py-4">
    <div class="alert alert-warning mt-4">You do not have permission to view reports. Please contact the administrator.</div>
</div>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>
