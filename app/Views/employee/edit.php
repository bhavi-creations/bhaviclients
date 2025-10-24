<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\edit.php 

// Get services for session and validation
$session = \Config\Services::session();
// $validation is passed from the controller, check if it's available
$validation = $validation ?? \Config\Services::validation();

// Helper function to get the current value, prioritizing old input on validation failure
function get_value($field, $employee_data, $default = '') {
    return old($field) !== null ? old($field) : ($employee_data[$field] ?? $default);
}
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Employee: <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('employee') ?>">Employees</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">

                    <!-- Flash Messages -->
                    <?php if ($session->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $session->getFlashdata('success') ?>
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
                    <?php if ($validation->getErrors()): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Validation Error!</strong> Please correct the highlighted errors below.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Editing Employee ID: <?= esc($employee['id']) ?></h3>
                        </div>

                        <?= form_open(base_url('employee/update/' . $employee['id'])) ?>
                        <div class="card-body">

                            <!-- Personal Information -->
                            <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information (Affects User Account)</h5>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= $validation->hasError('first_name') ? 'is-invalid' : '' ?>" 
                                           id="first_name" 
                                           name="first_name" 
                                           placeholder="Enter first name" 
                                           value="<?= esc(get_value('first_name', $employee)) ?>">
                                    <?php if ($validation->hasError('first_name')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('first_name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= $validation->hasError('last_name') ? 'is-invalid' : '' ?>" 
                                           id="last_name" 
                                           name="last_name" 
                                           placeholder="Enter last name" 
                                           value="<?= esc(get_value('last_name', $employee)) ?>">
                                    <?php if ($validation->hasError('last_name')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('last_name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control <?= $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Enter email" 
                                           value="<?= esc(get_value('email', $employee)) ?>">
                                    <?php if ($validation->hasError('email')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('email') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= $validation->hasError('phone') ? 'is-invalid' : '' ?>" 
                                           id="phone" 
                                           name="phone" 
                                           placeholder="Enter phone number" 
                                           value="<?= esc(get_value('phone', $employee)) ?>">
                                    <?php if ($validation->hasError('phone')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('phone') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Employment Details -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase"></i> Employment Details</h5>

                            <div class="form-row">
                                <div class="form-group col-md-6"> 
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select class="form-control custom-select <?= $validation->hasError('department_id') ? 'is-invalid' : '' ?>" 
                                            id="department_id" 
                                            name="department_id" required>
                                        <option value="">Select Department</option>
                                        <?php 
                                        $currentDeptId = get_value('department_id', $employee);
                                        foreach ($departments as $department): ?>
                                            <option value="<?= esc($department['id']) ?>" <?= $currentDeptId == $department['id'] ? 'selected' : '' ?>>
                                                <?= esc($department['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($validation->hasError('department_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('department_id') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
 
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                            <a href="<?= base_url('employee') ?>" class="btn btn-default float-right">Cancel</a>
                        </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
