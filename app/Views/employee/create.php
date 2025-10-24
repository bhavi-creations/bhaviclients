<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\create.php 

// Helper function to check if a session flash data exists
$session = \Config\Services::session();
// $validation is passed from the controller in $data['validation']
// We'll use the variable $validation passed from the controller, 
// which is a Validator instance only if validation failed.
$hasValidationErrors = isset($validation) && is_object($validation);
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
                        <li class="breadcrumb-item"><a href="<?= base_url('employee') ?>">Employees</a></li>
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
                <div class="col-md-8 offset-md-2">
                    <!-- Session Flash Messages -->
                    <?php if ($session->getFlashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $session->getFlashdata('message') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if ($session->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $session->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Employee Details</h3>
                        </div>
                        
                        <!-- Use form_open() helper for POST method and CSRF protection -->
                        <?= form_open('employee/store') ?> 

                        <div class="card-body">
                            
                            <!-- Personal Information -->
                            <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h5>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= $hasValidationErrors && $validation->hasError('first_name') ? 'is-invalid' : '' ?>" 
                                        id="first_name" name="first_name" value="<?= old('first_name') ?>" required>
                                    <?php if ($hasValidationErrors && $validation->hasError('first_name')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('first_name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= $hasValidationErrors && $validation->hasError('last_name') ? 'is-invalid' : '' ?>" 
                                        id="last_name" name="last_name" value="<?= old('last_name') ?>" required>
                                    <?php if ($hasValidationErrors && $validation->hasError('last_name')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('last_name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= $hasValidationErrors && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                        id="email" name="email" value="<?= old('email') ?>" required>
                                    <?php if ($hasValidationErrors && $validation->hasError('email')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('email') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">Phone (Username/Password) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= $hasValidationErrors && $validation->hasError('phone') ? 'is-invalid' : '' ?>" 
                                        id="phone" name="phone" value="<?= old('phone') ?>" required>
                                    <?php if ($hasValidationErrors && $validation->hasError('phone')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('phone') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                           
                            
                            
                            <!-- Employment Details -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase"></i> Employment Details</h5>
                            
                            <div class="form-row">
                                <!-- Department (Using Select2) -->
                                <div class="form-group col-md-6">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select id="department_id" name="department_id" 
                                        class="form-control custom-select select2 <?= $hasValidationErrors && $validation->hasError('department_id') ? 'is-invalid' : '' ?>" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['id']) ?>" 
                                                <?= (old('department_id') == $dept['id']) ? 'selected' : '' ?>>
                                                <?= esc($dept['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($hasValidationErrors && $validation->hasError('department_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('department_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Role (Now automatically set to Employee - Role ID 2) -->
                                <div class="form-group col-md-6">
                                    <label>Job Role</label>
                                    <p class="form-control-static text-success font-weight-bold">
                                        <i class="fas fa-check-circle"></i> Automatically set to Employee (ID: 2)
                                    </p>
                                    <!-- HIDDEN input to pass the fixed role_id (2 for Employee) to the controller -->
                                    <input type="hidden" name="role_id" value="2">
                                    <?php if ($hasValidationErrors && $validation->hasError('role_id')): ?>
                                        <div class="invalid-feedback" style="display: block;">
                                            <?= $validation->getError('role_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- The Date of Joining field has been removed as requested -->

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Employee</button>
                            <a href="<?= base_url('employee') ?>" class="btn btn-secondary float-right">Cancel</a>
                        </div>
                        
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->  
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2 explicitly for Department field only
        $('#department_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Department',
            allowClear: true
        });

        // The initialization for role_id and date_of_joining has been removed.
    });
</script>
<?= $this->endSection() ?>
