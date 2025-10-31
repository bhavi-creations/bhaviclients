<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\task_management\create.php 

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
                        <li class="breadcrumb-item"><a href="<?= base_url('task-management') ?>">Tasks</a></li>
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
                            <h3 class="card-title">Assign Task to Employee</h3>
                        </div>
                        
                        <?= form_open_multipart('task-management/store') ?>

                        <div class="card-body">
                            
                            <!-- Employee Selection -->
                            <div class="form-group">
                                <label for="employee_id">Assign To (Employee) <span class="text-danger">*</span></label>
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

                            <!-- Client Selection -->
                            <div class="form-group">
                                <label for="client_id">Client (Optional)</label>
                                <select id="client_id" 
                                        name="client_id" 
                                        class="form-control select2">
                                    <option value="">-- No Client --</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= esc($client['id']) ?>" <?= (old('client_id') == $client['id']) ? 'selected' : '' ?>>
                                            <?= esc($client['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Task Title -->
                            <div class="form-group">
                                <label for="title">Task Title <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= $hasValidationErrors && $validation->hasError('title') ? 'is-invalid' : '' ?>" 
                                       id="title" 
                                       name="title" 
                                       value="<?= old('title') ?>"
                                       placeholder="e.g., Create landing page for XYZ project" 
                                       required>
                                <?php if ($hasValidationErrors && $validation->hasError('title')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('title') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description">Task Description <span class="text-danger">*</span></label>
                                <textarea class="form-control <?= $hasValidationErrors && $validation->hasError('description') ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Detailed description of the task..."
                                          required><?= old('description') ?></textarea>
                                <?php if ($hasValidationErrors && $validation->hasError('description')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('description') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Admin Remarks -->
                            <div class="form-group">
                                <label for="admin_remarks">Admin Notes/Instructions</label>
                                <textarea class="form-control" 
                                          id="admin_remarks" 
                                          name="admin_remarks" 
                                          rows="3"
                                          placeholder="Additional notes or specific instructions..."><?= old('admin_remarks') ?></textarea>
                            </div>

                            <!-- NEW: Admin Reference Files -->
                            <div class="form-group">
                                <label for="admin_files">Reference Files (Optional)</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="admin_files[]" 
                                           class="custom-file-input" 
                                           id="admin_files"
                                           multiple
                                           accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                    <label class="custom-file-label" for="admin_files">Choose files...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Upload reference materials (images, videos, documents) for the employee. You can select multiple files.
                                </small>
                            </div>

                            <div class="row">
                                <!-- Priority -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Priority <span class="text-danger">*</span></label>
                                        <select name="priority" id="priority" class="form-control" required>
                                            <option value="Low" <?= (old('priority', 'Medium') == 'Low') ? 'selected' : '' ?>>Low</option>
                                            <option value="Medium" <?= (old('priority', 'Medium') == 'Medium') ? 'selected' : '' ?>>Medium</option>
                                            <option value="High" <?= (old('priority', 'Medium') == 'High') ? 'selected' : '' ?>>High</option>
                                            <option value="Urgent" <?= (old('priority', 'Medium') == 'Urgent') ? 'selected' : '' ?>>Urgent</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Due Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="due_date" 
                                               name="due_date" 
                                               value="<?= old('due_date') ?>"
                                               min="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Assign Task
                            </button>
                            <a href="<?= base_url('task-management') ?>" class="btn btn-secondary float-right">Cancel</a>
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
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Select --',
        allowClear: true
    });

    // Update file input label
    $('#admin_files').on('change', function(e) {
        var fileCount = e.target.files.length;
        var label = fileCount > 1 ? fileCount + ' files selected' : (e.target.files[0]?.name || 'Choose files...');
        $(this).next('.custom-file-label').text(label);
    });
});
</script>
<?= $this->endSection() ?>
