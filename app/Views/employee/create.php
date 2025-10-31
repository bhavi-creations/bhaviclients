<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\create.php 

$session = \Config\Services::session();
$hasValidationErrors = isset($validation) && is_object($validation);
?>
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('employee') ?>">Employees</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <!-- Flash Messages -->
                    <?php if ($session->getFlashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $session->getFlashdata('message') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($session->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $session->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <!-- Display validation errors -->
                    <?php if ($hasValidationErrors && $validation->getErrors()): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                            <ul class="mb-0">
                                <?php foreach ($validation->getErrors() as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Employee Details</h3>
                        </div>
                        
                        <?= form_open_multipart('employee/store') ?> 

                        <div class="card-body">
                            
                            <!-- Personal Information -->
                            <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_code">Employee Code</label>
                                        <input type="text" 
                                               class="form-control <?= $hasValidationErrors && $validation->hasError('employee_code') ? 'is-invalid' : '' ?>" 
                                               id="employee_code" 
                                               name="employee_code" 
                                               value="<?= old('employee_code') ?>"
                                               placeholder="e.g., EMP001">
                                        <small class="form-text text-muted">Optional - Manual employee code</small>
                                        <?php if ($hasValidationErrors && $validation->hasError('employee_code')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('employee_code') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?= $hasValidationErrors && $validation->hasError('first_name') ? 'is-invalid' : '' ?>" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="<?= old('first_name') ?>" 
                                               required>
                                        <?php if ($hasValidationErrors && $validation->hasError('first_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('first_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?= $hasValidationErrors && $validation->hasError('last_name') ? 'is-invalid' : '' ?>" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="<?= old('last_name') ?>" 
                                               required>
                                        <?php if ($hasValidationErrors && $validation->hasError('last_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('last_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control <?= $hasValidationErrors && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                               id="email" 
                                               name="email" 
                                               value="<?= old('email') ?>" 
                                               required>
                                        <?php if ($hasValidationErrors && $validation->hasError('email')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone (Username/Password) <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?= $hasValidationErrors && $validation->hasError('phone') ? 'is-invalid' : '' ?>" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?= old('phone') ?>" 
                                               required>
                                        <small class="form-text text-muted">Used as username and initial password</small>
                                        <?php if ($hasValidationErrors && $validation->hasError('phone')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('phone') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Parent Information -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-users"></i> Parent/Guardian Information</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent_name">Parent/Guardian Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="parent_name" 
                                               name="parent_name" 
                                               value="<?= old('parent_name') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent_phone">Parent/Guardian Phone</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="parent_phone" 
                                               name="parent_phone" 
                                               value="<?= old('parent_phone') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Employment Details -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase"></i> Employment Details</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department_id">Department <span class="text-danger">*</span></label>
                                        <select id="department_id" 
                                                name="department_id" 
                                                class="form-control select2 <?= $hasValidationErrors && $validation->hasError('department_id') ? 'is-invalid' : '' ?>" 
                                                required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= esc($dept['id']) ?>" <?= (old('department_id') == $dept['id']) ? 'selected' : '' ?>>
                                                    <?= esc($dept['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ($hasValidationErrors && $validation->hasError('department_id')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('department_id') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_joining">Date of Joining</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="date_of_joining" 
                                               name="date_of_joining" 
                                               value="<?= old('date_of_joining', date('Y-m-d')) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Job Role</label>
                                        <p class="form-control-static text-success font-weight-bold">
                                            <i class="fas fa-check-circle"></i> Automatically set to Employee (ID: 2)
                                        </p>
                                        <input type="hidden" name="role_id" value="2">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="initial_salary">Initial Salary (₹)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="initial_salary" 
                                               name="initial_salary" 
                                               step="0.01" 
                                               value="<?= old('initial_salary') ?>"
                                               placeholder="e.g., 25000.00">
                                        <small class="form-text text-muted">Optional - Starting salary</small>
                                    </div>
                                </div>
                            </div>

                            <!-- File Uploads -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-file-upload"></i> Document Uploads</h5>

                            <div class="form-group">
                                <label for="employee_files">Upload Documents (Multiple files allowed)</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="employee_files[]" 
                                           class="custom-file-input" 
                                           id="employee_files"
                                           multiple 
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.xlsx,.xls">
                                    <label class="custom-file-label" for="employee_files">Choose files...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Allowed: PDF, DOC, DOCX, XLS, XLSX, Images (JPG, PNG), ZIP (Optional)
                                </small>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="3"><?= old('remarks') ?></textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Employee
                            </button>
                            <a href="<?= base_url('employee') ?>" class="btn btn-secondary float-right">Cancel</a>
                        </div>
                        
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#department_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Department',
            allowClear: true
        });

        // Update file input label with selected file names
        $('#employee_files').on('change', function(e) {
            var files = e.target.files;
            var label = $(this).next('.custom-file-label');
            
            if (files.length > 0) {
                if (files.length === 1) {
                    label.text(files[0].name);
                } else {
                    label.text(files.length + ' files selected');
                }
            } else {
                label.text('Choose files...');
            }
        });
    });
</script>
<?= $this->endSection() ?>
