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

    <?= form_open(base_url('profile/update')) ?>
    <div class="form-group">
        <label>First Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="first_name" value="<?= old('first_name', $user['first_name']) ?>" required>
        <?php if(isset($validation) && $validation->hasError('first_name')): ?>
            <small class="text-danger"><?= $validation->getError('first_name') ?></small>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label>Last Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="last_name" value="<?= old('last_name', $user['last_name']) ?>" required>
        <?php if(isset($validation) && $validation->hasError('last_name')): ?>
            <small class="text-danger"><?= $validation->getError('last_name') ?></small>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label>Username <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="username" value="<?= old('username', $user['username']) ?>" required>
        <?php if(isset($validation) && $validation->hasError('username')): ?>
            <small class="text-danger"><?= $validation->getError('username') ?></small>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label>Phone <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="phone" value="<?= old('phone', $user['phone']) ?>" required>
        <?php if(isset($validation) && $validation->hasError('phone')): ?>
            <small class="text-danger"><?= $validation->getError('phone') ?></small>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="<?= base_url('profile') ?>" class="btn btn-secondary">Cancel</a>
    <?= form_close() ?>
</div>

<?= $this->endSection() ?>
