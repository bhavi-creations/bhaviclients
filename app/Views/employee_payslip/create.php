<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee_payslip\create.php 

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
                        <li class="breadcrumb-item"><a href="<?= base_url('employee-payslip') ?>">Payslips</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">

                    <?php if ($session->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $session->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($hasValidationErrors && $validation->getErrors()): ?>
                        <div class="alert alert-warning alert-dismissible fade show">
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
                            <h3 class="card-title">Upload Employee Payslip</h3>
                        </div>
                        
                        <?= form_open_multipart('employee-payslip/store') ?>

                        <div class="card-body">
                            
                            <!-- Employee Selection -->
                            <div class="form-group">
                                <label for="employee_id">Select Employee <span class="text-danger">*</span></label>
                                <select id="employee_id" 
                                        name="employee_id" 
                                        class="form-control select2 <?= $hasValidationErrors && $validation->hasError('employee_id') ? 'is-invalid' : '' ?>" 
                                        required>
                                    <option value="">-- Select Employee --</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= esc($emp['id']) ?>" <?= (old('employee_id') == $emp['id']) ? 'selected' : '' ?>>
                                            <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($hasValidationErrors && $validation->hasError('employee_id')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('employee_id') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Month Selection -->
                            <div class="form-group">
                                <label for="month">Month <span class="text-danger">*</span></label>
                                <input type="month" 
                                       class="form-control <?= $hasValidationErrors && $validation->hasError('month') ? 'is-invalid' : '' ?>" 
                                       id="month" 
                                       name="month" 
                                       value="<?= old('month', date('Y-m')) ?>"
                                       max="<?= date('Y-m') ?>"
                                       required>
                                <small class="form-text text-muted">Select the month for which this payslip belongs</small>
                                <?php if ($hasValidationErrors && $validation->hasError('month')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('month') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- File Upload -->
                            <div class="form-group">
                                <label for="payslip_file">Payslip File (PDF/Image) <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="payslip_file" 
                                           class="custom-file-input <?= $hasValidationErrors && $validation->hasError('payslip_file') ? 'is-invalid' : '' ?>" 
                                           id="payslip_file"
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           required>
                                    <label class="custom-file-label" for="payslip_file">Choose file...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Allowed: PDF, JPG, PNG (Max 5MB)
                                </small>
                                <?php if ($hasValidationErrors && $validation->hasError('payslip_file')): ?>
                                    <div class="invalid-feedback d-block"><?= $validation->getError('payslip_file') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks (Optional)</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="3"
                                          placeholder="Any additional notes or comments..."><?= old('remarks') ?></textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Payslip
                            </button>
                            <a href="<?= base_url('employee-payslip') ?>" class="btn btn-secondary float-right">Cancel</a>
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
            var fileName = e.target.files[0]?.name || 'Choose file...';
            $(this).next('.custom-file-label').text(fileName);
        });
    });
</script>
<?= $this->endSection() ?>
