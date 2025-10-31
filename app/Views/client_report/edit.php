<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_report\edit.php 

$session = \Config\Services::session();
$validation = $validation ?? \Config\Services::validation();

function get_value($field, $report_data, $default = '') {
    return old($field) !== null ? old($field) : ($report_data[$field] ?? $default);
}
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Report: <?= esc($report['title']) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client-report') ?>">Client Reports</a></li>
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
                            <strong>Validation Error!</strong> Please correct the highlighted errors below.
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Editing Report ID: <?= esc($report['id']) ?></h3>
                        </div>

                        <?= form_open_multipart(base_url('client-report/update/' . $report['id'])) ?>
                        <div class="card-body">

                            <!-- Client Selection -->
                            <div class="form-group">
                                <label for="client_id">Client <span class="text-danger">*</span></label>
                                <select id="client_id" 
                                        name="client_id" 
                                        class="form-control select2 <?= $validation->hasError('client_id') ? 'is-invalid' : '' ?>" 
                                        required>
                                    <option value="">-- Select Client --</option>
                                    <?php 
                                    $currentClientId = get_value('client_id', $report);
                                    foreach ($clients as $client): ?>
                                        <option value="<?= esc($client['id']) ?>" <?= $currentClientId == $client['id'] ? 'selected' : '' ?>>
                                            <?= esc($client['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($validation->hasError('client_id')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('client_id') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Report Title -->
                            <div class="form-group">
                                <label for="title">Report Title <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>" 
                                       id="title" 
                                       name="title" 
                                       value="<?= esc(get_value('title', $report)) ?>" 
                                       required>
                                <?php if ($validation->hasError('title')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('title') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Report Date -->
                            <div class="form-group">
                                <label for="report_date">Report Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control <?= $validation->hasError('report_date') ? 'is-invalid' : '' ?>" 
                                       id="report_date" 
                                       name="report_date" 
                                       value="<?= esc(get_value('report_date', $report)) ?>" 
                                       required>
                                <?php if ($validation->hasError('report_date')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('report_date') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Existing Files Display -->
                            <?php if (!empty($files)): ?>
                                <div class="form-group">
                                    <label>Current Files</label>
                                    <div class="alert alert-info">
                                        <strong><?= count($files) ?> file(s) uploaded</strong>
                                        <br><small>You can view/delete files from the View page</small>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Upload Additional Files -->
                            <div class="form-group">
                                <label for="report_files">Upload Additional Files (Optional)</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="report_files[]" 
                                           class="custom-file-input" 
                                           id="report_files"
                                           multiple 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                                    <label class="custom-file-label" for="report_files">Choose files...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Add more files (existing files will not be affected)
                                </small>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="4"><?= esc(get_value('remarks', $report)) ?></textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Report
                            </button>
                            <a href="<?= base_url('client-report') ?>" class="btn btn-default float-right">Cancel</a>
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
        $('#client_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: '-- Select Client --',
            allowClear: true
        });

        // Update file input label
        $('#report_files').on('change', function(e) {
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
