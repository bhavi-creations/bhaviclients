<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('user-management') ?>">Users</a></li>
                        <li class="breadcrumb-item active">Create User</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New User</h3>
                </div>
                
                <?= form_open(base_url('user-management/store')) ?>
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role_id" id="role_id" required>
                            <option value="">-- Select Role --</option>
                            <?php foreach ($roles as $role): ?>
                                <?php if (!in_array($role['id'], [2,3])): // Hide Employee and Client ?>
                                    <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                        <?= esc($role['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Client Manager = Manages a specific client</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" value="<?= old('first_name') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" value="<?= old('last_name') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= old('email') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" value="<?= old('username') ?>" required>
                        <small class="text-muted">Used for login</small>
                    </div>

                    <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" value="<?= old('phone') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required>
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>

                    <!-- Client Assignment Section (Only for Client Manager) -->
                    <div id="clientAssignmentSection" style="display: none;">
                        <hr>
                        <h5><i class="fas fa-user-friends"></i> Assign Client</h5>
                        <div class="form-group">
                            <label>Select Client <span class="text-danger">*</span></label>
                            <select class="form-control" name="assigned_client" id="assigned_client">
                                <option value="">-- Select Client --</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>">
                                        <?= esc($client['name']) ?> - <?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create User
                    </button>
                    <a href="<?= base_url('user-management') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </section>
</div>

<script>
// Show/hide client assignment based on selected role
document.getElementById('role_id').addEventListener('change', function() {
    var roleId = this.value;
    var clientSection = document.getElementById('clientAssignmentSection');
    var clientSelect = document.getElementById('assigned_client');
    
    if (roleId == '4') { // Client Manager only
        clientSection.style.display = 'block';
        clientSelect.required = true;
    } else {
        clientSection.style.display = 'none';
        clientSelect.required = false;
    }
});

// On page load
window.addEventListener('load', function() {
    var roleId = document.getElementById('role_id').value;
    if (roleId == '4') {
        document.getElementById('clientAssignmentSection').style.display = 'block';
        document.getElementById('assigned_client').required = true;
    }
});
</script>

<?= $this->endSection() ?>
