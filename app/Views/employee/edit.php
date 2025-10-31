<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\edit.php 

$session = \Config\Services::session();
$validation = $validation ?? \Config\Services::validation();

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
                <div class="col-md-10 offset-md-1">

                    <!-- Flash Messages -->
                    <?php if ($session->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $session->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($session->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $session->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Editing Employee ID: <?= esc($employee['id']) ?></h3>
                        </div>

                        <?= form_open_multipart(base_url('employee/update/' . $employee['id'])) ?>
                        <div class="card-body">

                            <!-- Personal Information -->
                            <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_code">Employee Code</label>
                                        <input type="text" 
                                               class="form-control <?= $validation->hasError('employee_code') ? 'is-invalid' : '' ?>" 
                                               id="employee_code" 
                                               name="employee_code" 
                                               value="<?= esc(get_value('employee_code', $employee)) ?>">
                                        <?php if ($validation->hasError('employee_code')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('employee_code') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="active" <?= get_value('status', $employee) == 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= get_value('status', $employee) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?= $validation->hasError('first_name') ? 'is-invalid' : '' ?>" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="<?= esc(get_value('first_name', $employee)) ?>">
                                        <?php if ($validation->hasError('first_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('first_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?= $validation->hasError('last_name') ? 'is-invalid' : '' ?>" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="<?= esc(get_value('last_name', $employee)) ?>">
                                        <?php if ($validation->hasError('last_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('last_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control <?= $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                               id="email" 
                                               name="email" 
                                               value="<?= esc(get_value('email', $employee)) ?>">
                                        <?php if ($validation->hasError('email')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control <?= $validation->hasError('phone') ? 'is-invalid' : '' ?>" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?= esc(get_value('phone', $employee)) ?>">
                                        <?php if ($validation->hasError('phone')): ?>
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
                                               value="<?= esc(get_value('parent_name', $employee)) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parent_phone">Parent/Guardian Phone</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="parent_phone" 
                                               name="parent_phone" 
                                               value="<?= esc(get_value('parent_phone', $employee)) ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Employment Details -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase"></i> Employment Details</h5>

                            <div class="row">
                                <div class="col-md-6"> 
                                    <div class="form-group">
                                        <label for="department_id">Department <span class="text-danger">*</span></label>
                                        <select class="form-control <?= $validation->hasError('department_id') ? 'is-invalid' : '' ?>" 
                                                id="department_id" 
                                                name="department_id" 
                                                required>
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
                                               value="<?= esc(get_value('date_of_joining', $employee)) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role_id">Job Role <span class="text-danger">*</span></label>
                                        <select class="form-control" id="role_id" name="role_id" required>
                                            <?php 
                                            $currentRoleId = get_value('role_id', $employee);
                                            foreach ($roles as $role): ?>
                                                <option value="<?= esc($role['id']) ?>" <?= $currentRoleId == $role['id'] ? 'selected' : '' ?>>
                                                    <?= esc($role['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Salary Information Section -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-money-bill-wave"></i> Salary Information</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="salary_amount">Update Salary (₹)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="salary_amount" 
                                               name="salary_amount" 
                                               step="0.01" 
                                               value="<?= old('salary_amount') ?>"
                                               placeholder="Enter new salary amount">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Leave empty to keep current salary unchanged
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="increment_type">Increment Type</label>
                                        <select class="form-control" id="increment_type" name="increment_type">
                                            <option value="">-- Select Type --</option>
                                            <option value="increment" <?= old('increment_type') == 'increment' ? 'selected' : '' ?>>Regular Increment</option>
                                            <option value="promotion" <?= old('increment_type') == 'promotion' ? 'selected' : '' ?>>Promotion</option>
                                            <option value="annual_review" <?= old('increment_type') == 'annual_review' ? 'selected' : '' ?>>Annual Review</option>
                                            <option value="adjustment" <?= old('increment_type') == 'adjustment' ? 'selected' : '' ?>>Salary Adjustment</option>
                                        </select>
                                        <small class="form-text text-muted">Required if updating salary</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="effective_date">Effective Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="effective_date" 
                                               name="effective_date" 
                                               value="<?= old('effective_date', date('Y-m-d')) ?>">
                                        <small class="form-text text-muted">When this salary becomes effective</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="salary_reason">Reason for Salary Change</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="salary_reason" 
                                               name="salary_reason" 
                                               value="<?= old('salary_reason') ?>"
                                               placeholder="e.g., Annual increment, Performance bonus">
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($latestSalary)): ?>
                                <div class="alert alert-info">
                                    <strong><i class="fas fa-info-circle"></i> Current Salary:</strong> 
                                    ₹<?= number_format($latestSalary['salary_amount'], 2) ?> 
                                    (Effective from <?= date('M d, Y', strtotime($latestSalary['effective_date'])) ?>)
                                </div>
                            <?php endif; ?>

                            <!-- File Uploads Section -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-file-upload"></i> Document Management</h5>

                            <!-- Current Uploaded Files -->
                            <?php if (!empty($employee['file_uploads'])): ?>
                                <?php $uploadedFiles = json_decode($employee['file_uploads'], true); ?>
                                <?php if (is_array($uploadedFiles) && !empty($uploadedFiles)): ?>
                                    <div class="form-group">
                                        <label>Current Documents (Select files to keep)</label>
                                        <div class="row">
                                            <?php foreach ($uploadedFiles as $index => $file): ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" 
                                                               class="custom-control-input" 
                                                               id="keepFile<?= $index ?>" 
                                                               name="keep_files[]" 
                                                               value="<?= $index ?>" 
                                                               checked>
                                                        <label class="custom-control-label" for="keepFile<?= $index ?>">
                                                            <i class="fas fa-file mr-1"></i>
                                                            <?= esc(strlen($file) > 30 ? substr(basename($file), 0, 30) . '...' : basename($file)) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Uncheck files you want to remove
                                        </small>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Upload New Files -->
                            <div class="form-group">
                                <label for="employee_files">Upload Additional Documents (Optional)</label>
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
                                    Allowed: PDF, DOC, DOCX, XLS, XLSX, Images (JPG, PNG), ZIP
                                    <?php if (!empty($employee['file_uploads'])): ?>
                                        <br><strong>Note:</strong> New files will be added to existing documents.
                                    <?php endif; ?>
                                </small>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="3"><?= esc(get_value('remarks', $employee)) ?></textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
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

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
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
