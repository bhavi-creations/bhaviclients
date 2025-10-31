<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee_payslip\edit.php 

$session = \Config\Services::session();
$validation = $validation ?? \Config\Services::validation();

function get_value($field, $payslip_data, $default = '') {
    return old($field) !== null ? old($field) : ($payslip_data[$field] ?? $default);
}
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
                        <li class="breadcrumb-item"><a href="<?= base_url('employee-payslip') ?>">Payslips</a></li>
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

                    <?php if ($validation->getErrors()): ?>
                        <div class="alert alert-warning alert-dismissible fade show">
                            <strong>Validation Error!</strong> Please correct the errors below.
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Edit Payslip</h3>
                        </div>

                        <?= form_open_multipart(base_url('employee-payslip/update/' . $payslip['id'])) ?>
                        <div class="card-body">

                            <!-- Employee Selection -->
                            <div class="form-group">
                                <label for="employee_id">Select Employee <span class="text-danger">*</span></label>
                                <select id="employee_id" 
                                        name="employee_id" 
                                        class="form-control select2 <?= $validation->hasError('employee_id') ? 'is-invalid' : '' ?>" 
                                        required>
                                    <option value="">-- Select Employee --</option>
                                    <?php 
                                    $currentEmployeeId = get_value('employee_id', $payslip);
                                    foreach ($employees as $emp): 
                                    ?>
                                        <option value="<?= esc($emp['id']) ?>" <?= $currentEmployeeId == $emp['id'] ? 'selected' : '' ?>>
                                            <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($validation->hasError('employee_id')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('employee_id') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Month Selection -->
                            <div class="form-group">
                                <label for="month">Month <span class="text-danger">*</span></label>
                                <input type="month" 
                                       class="form-control <?= $validation->hasError('month') ? 'is-invalid' : '' ?>" 
                                       id="month" 
                                       name="month" 
                                       value="<?= esc(get_value('month', $payslip)) ?>"
                                       max="<?= date('Y-m') ?>"
                                       required>
                                <?php if ($validation->hasError('month')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('month') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Current File Info -->
                            <div class="form-group">
                                <label>Current File</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-file"></i> <?= esc($payslip['payslip_file']) ?>
                                </div>
                            </div>

                            <!-- File Upload (Optional) -->
                            <div class="form-group">
                                <label for="payslip_file">Replace Payslip File (Optional)</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="payslip_file" 
                                           class="custom-file-input <?= $validation->hasError('payslip_file') ? 'is-invalid' : '' ?>" 
                                           id="payslip_file"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="payslip_file">Choose new file...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Leave empty to keep current file. Allowed: PDF, JPG, PNG (Max 5MB)
                                </small>
                                <?php if ($validation->hasError('payslip_file')): ?>
                                    <div class="invalid-feedback d-block"><?= $validation->getError('payslip_file') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks (Optional)</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="3"><?= esc(get_value('remarks', $payslip)) ?></textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Payslip
                            </button>
                            <a href="<?= base_url('employee-payslip/employee/' . $payslip['employee_id']) ?>" class="btn btn-default float-right">Cancel</a>
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
        // Initialize Select2
        $('#employee_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: '-- Select Employee --',
            allowClear: true
        });

        // Update file input label
        $('#payslip_file').on('change', function(e) {
            var fileName = e.target.files[0]?.name || 'Choose new file...';
            $(this).next('.custom-file-label').text(fileName);
        });
    });
</script>
<?= $this->endSection() ?>
