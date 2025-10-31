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
                        <li class="breadcrumb-item"><a href="<?= base_url('profile') ?>">Profile</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-key"></i> Change Password</h3>
                        </div>

                        <?= form_open(base_url('profile/update-password')) ?>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?= isset($validation) && $validation->hasError('current_password') ? 'is-invalid' : '' ?>" 
                                       name="current_password" required>
                                <?php if (isset($validation) && $validation->hasError('current_password')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('current_password') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label>New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?= isset($validation) && $validation->hasError('new_password') ? 'is-invalid' : '' ?>" 
                                       name="new_password" required>
                                <?php if (isset($validation) && $validation->hasError('new_password')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('new_password') ?>
                                    </div>
                                <?php else: ?>
                                    <small class="text-muted">Minimum 8 characters required</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?= isset($validation) && $validation->hasError('confirm_password') ? 'is-invalid' : '' ?>" 
                                       name="confirm_password" required>
                                <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('confirm_password') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Password Requirements:</strong>
                                <ul class="mb-0 pl-3">
                                    <li>Minimum 8 characters</li>
                                    <li>New password must match confirmation</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Change Password
                            </button>
                            <a href="<?= base_url('profile') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
