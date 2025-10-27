<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <h2><?= esc($title) ?></h2>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?= form_open(base_url('profile/update-password')) ?>
    <div class="form-group">
        <label>Current Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" name="current_password" required>
        <?php if(isset($validation) && $validation->hasError('current_password')): ?>
            <small class="text-danger"><?= $validation->getError('current_password') ?></small>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label>New Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" name="new_password" required>
        <?php if(isset($validation) && $validation->hasError('new_password')): ?>
            <small class="text-danger"><?= $validation->getError('new_password') ?></small>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label>Confirm New Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" name="confirm_password" required>
        <?php if(isset($validation) && $validation->hasError('confirm_password')): ?>
            <small class="text-danger"><?= $validation->getError('confirm_password') ?></small>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Change Password</button>
    <a href="<?= base_url('profile') ?>" class="btn btn-secondary">Cancel</a>
    <?= form_close() ?>
</div>

<?= $this->endSection() ?>
