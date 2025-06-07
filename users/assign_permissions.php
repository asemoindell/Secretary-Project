<?php
require_once '../includes/db.php';

// Only admin can access
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// All possible permissions (updated as requested)
$all_permissions = [
    'view_users'      => 'View Users',
    'view_reports'    => 'View Reports',
    'new_registration'=> 'New Registration',
    'reporting'       => 'Reporting Menu',
    'add_report'      => 'Add Report',
    'edit_report'     => 'Edit Report',
    'update_report'   => 'Update Report',
    'delete_report'   => 'Delete Report',
    'show_report'     => 'Show Report',
    'login_location'  => 'View Login Location',
    'all_permissions' => 'All Permissions (Except Assign Permissions)',
    'view_assign_permissions' => 'View Assign Permissions'
];

// Handle permission update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['secretary_id'])) {
    $secretary_id = intval($_POST['secretary_id']);
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    $permissions_json = json_encode($permissions);
    $stmt = $pdo->prepare('UPDATE users SET permissions = ? WHERE id = ?');
    $stmt->execute([$permissions_json, $secretary_id]);
    header('Location: assign_permissions.php?success=1');
    exit();
}

// Fetch all secretaries
$stmt = $pdo->query("SELECT id, surname, firstname, middlename, permissions FROM users WHERE role = 'secretary' ORDER BY surname, firstname");
$secretaries = $stmt->fetchAll();
include '../includes/header.php';
?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">Assign Secretary Permissions</h2>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Permissions updated successfully.</div>
    <?php endif; ?>
    <a href="create_admin.php" class="btn btn-success mb-3">Add New User</a>
    <form method="post" class="mb-4">
        <div class="mb-3">
            <label for="secretary_id" class="form-label">Select Secretary</label>
            <select name="secretary_id" id="secretary_id" class="form-select" required>
                <option value="">-- Select Secretary --</option>
                <?php foreach ($secretaries as $sec): ?>
                    <option value="<?= $sec['id'] ?>" <?= isset($_POST['secretary_id']) && $_POST['secretary_id'] == $sec['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sec['surname'] . ' ' . $sec['firstname'] . ' ' . $sec['middlename']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Assign Permissions</label>
            <div class="row">
                <?php foreach ($all_permissions as $perm): ?>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $perm ?>" id="perm_<?= $perm ?>"
                                <?= (isset($selected_permissions) && in_array($perm, $selected_permissions)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="perm_<?= $perm ?>">
                                <?= ucwords(str_replace('_', ' ', $perm)) ?>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" id="clearBtn">Clear</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($secretaries as $sec): 
                $current_permissions = [];
                if (!empty($sec['permissions'])) {
                    $current_permissions = json_decode($sec['permissions'], true) ?: [];
                }
                $fullname = trim($sec['surname'] . ' ' . $sec['firstname'] . ' ' . $sec['middlename']);
            ?>
                <tr>
                    <td><?= htmlspecialchars($fullname) ?></td>
                    <td>
                        <form method="post" class="d-flex flex-wrap align-items-center gap-2">
                            <input type="hidden" name="secretary_id" value="<?= $sec['id'] ?>">
                            <?php foreach ($all_permissions as $perm_key => $perm_label): ?>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $perm_key ?>"
                                        id="perm_<?= $sec['id'] ?>_<?= $perm_key ?>"
                                        <?= in_array($perm_key, $current_permissions) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm_<?= $sec['id'] ?>_<?= $perm_key ?>">
                                        <?= htmlspecialchars($perm_label) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm view-perms-btn" data-perms='<?= json_encode($current_permissions) ?>' data-name='<?= htmlspecialchars($fullname) ?>'>View</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.getElementById('clearBtn').addEventListener('click', function() {
    document.querySelectorAll('input[type=checkbox][name="permissions[]"]').forEach(cb => cb.checked = false);
    document.getElementById('secretary_id').selectedIndex = 0;
});
// View permissions modal/alert
const permLabels = <?php echo json_encode($all_permissions); ?>;
document.querySelectorAll('.view-perms-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const perms = JSON.parse(this.getAttribute('data-perms'));
        const name = this.getAttribute('data-name');
        let msg = `<strong>Permissions for ${name}:</strong><ul>`;
        if (perms.length === 0) {
            msg += '<li><em>No permissions assigned.</em></li>';
        } else {
            perms.forEach(p => {
                msg += `<li>${permLabels[p] || p}</li>`;
            });
        }
        msg += '</ul>';
        const modal = document.createElement('div');
        modal.innerHTML = `<div class='modal fade' id='permModal' tabindex='-1'><div class='modal-dialog'><div class='modal-content'><div class='modal-header bg-warning'><h5 class='modal-title'>Permissions</h5><button type='button' class='btn-close' data-bs-dismiss='modal'></button></div><div class='modal-body' style='background:#0e0f1e !important; color:#fff;'>${msg}</div><div class='modal-footer'><button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button></div></div></div></div>`;
        document.body.appendChild(modal);
        var myModal = new bootstrap.Modal(modal.querySelector('.modal'));
        myModal.show();
        modal.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => btn.addEventListener('click', () => modal.remove()));
        modal.querySelector('.modal').addEventListener('hidden.bs.modal', () => modal.remove());
    });
});
</script>
<?php include '../includes/footer.php'; ?>
