<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\edit_task_work.php 

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
                        <li class="breadcrumb-item"><a href="<?= base_url('employee-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('my-tasks') ?>">My Tasks</a></li>
                        <li class="breadcrumb-item active">Edit Work</li>
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

                    <!-- Task Summary Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Task Summary</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Task:</dt>
                                <dd class="col-sm-9"><strong><?= esc($task['title']) ?></strong></dd>

                                <dt class="col-sm-3">Description:</dt>
                                <dd class="col-sm-9"><?= esc(substr($task['description'], 0, 150)) . (strlen($task['description']) > 150 ? '...' : '') ?></dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Edit Work Form Card -->
                    <div class="card card-warning shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Edit Your Work Submission</h3>
                        </div>
                        
                        <?= form_open_multipart('my-tasks/update-work/' . $task['id']) ?>

                        <div class="card-body">

                            <!-- Status Selection -->
                            <div class="form-group">
                                <label for="status">Update Status <span class="text-danger">*</span></label>
                                <select id="status" 
                                        name="status" 
                                        class="form-control <?= $hasValidationErrors && $validation->hasError('status') ? 'is-invalid' : '' ?>" 
                                        required>
                                    <option value="">-- Select Status --</option>
                                    <option value="Pending" <?= old('status', $task['status']) == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= old('status', $task['status']) == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= old('status', $task['status']) == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                                <?php if ($hasValidationErrors && $validation->hasError('status')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Employee Remarks/Notes -->
                            <div class="form-group">
                                <label for="employee_remarks">My Notes / Comments</label>
                                <textarea class="form-control" 
                                          id="employee_remarks" 
                                          name="employee_remarks" 
                                          rows="5"
                                          placeholder="Add any notes, comments, or details about your work..."><?= old('employee_remarks', $task['employee_remarks'] ?? '') ?></textarea>
                            </div>

                            <!-- Current Uploaded Files -->
                            <?php if (!empty($task['employee_files'])): ?>
                                <?php $employeeFiles = json_decode($task['employee_files'], true); ?>
                                <?php if (is_array($employeeFiles) && !empty($employeeFiles)): ?>
                                    <div class="form-group">
                                        <label>Current Files (Select files to keep)</label>
                                        <div class="row">
                                            <?php foreach ($employeeFiles as $index => $file): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" 
                                                               class="custom-control-input" 
                                                               id="keepFile<?= $index ?>" 
                                                               name="keep_files[]" 
                                                               value="<?= $index ?>" 
                                                               checked>
                                                        <label class="custom-control-label" for="keepFile<?= $index ?>">
                                                            <i class="fas fa-file mr-1"></i>
                                                            <?= esc(basename($file)) ?>
                                                            <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                               class="btn btn-xs btn-info ml-2" 
                                                               target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
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

                            <!-- Upload New Work Files -->
                            <div class="form-group">
                                <label for="work_files">Upload Additional Files</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="work_files[]" 
                                           class="custom-file-input" 
                                           id="work_files"
                                           multiple
                                           accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                    <label class="custom-file-label" for="work_files">Choose files...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Upload additional work files if needed
                                </small>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Work
                            </button>
                            <a href="<?= base_url('my-tasks/view/' . $task['id']) ?>" class="btn btn-secondary float-right">Cancel</a>
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
    // Update file input label
    $('#work_files').on('change', function(e) {
        var files = e.target.files;
        var fileCount = files.length;
        
        if (fileCount > 0) {
            var label = fileCount > 1 ? fileCount + ' files selected' : files[0].name;
            $(this).next('.custom-file-label').text(label);
        } else {
            $(this).next('.custom-file-label').text('Choose files...');
        }
    });
});
</script>
<?= $this->endSection() ?>
    