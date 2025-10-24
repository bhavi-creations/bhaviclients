<?php 
// Get services
$validation = \Config\Services::validation();
?>
<!-- Load Layout Template -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('roles') ?>">Roles</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <!-- Card for Create Role Form -->
                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">New Role Details</h3>
                        </div>
                        
                        <!-- Form start, submits to roles/store -->
                        <?= form_open(base_url('roles/store')) ?>
                            <div class="card-body">
                                
                                <!-- Role Name -->
                                <div class="form-group">
                                    <label for="name">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                        id="name" name="name" placeholder="Enter role name (e.g., Admin, Viewer)" 
                                        value="<?= old('name') ?>">
                                    <?php if ($validation->hasError('name')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>" 
                                        id="description" name="description" rows="3" 
                                        placeholder="Optional description of the role's permissions or duties"><?= old('description') ?></textarea>
                                    <?php if ($validation->hasError('description')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('description') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Role</button>
                                <a href="<?= base_url('roles') ?>" class="btn btn-default float-right">Cancel</a>
                            </div>
                        <?= form_close() ?>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<?= $this->endSection() ?>
