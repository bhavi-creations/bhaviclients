<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\submit_work.php 

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
                        <li class="breadcrumb-item active">Submit Work</li>
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

                                <?php if (!empty($task['due_date'])): ?>
                                    <dt class="col-sm-3">Due Date:</dt>
                                    <dd class="col-sm-9">
                                        <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                        <?php
                                        $today = strtotime(date('Y-m-d'));
                                        $dueDate = strtotime($task['due_date']);
                                        $daysLeft = floor(($dueDate - $today) / 86400);
                                        ?>
                                        <?php if ($daysLeft < 0): ?>
                                            <span class="badge badge-danger ml-2">Overdue!</span>
                                        <?php elseif ($daysLeft == 0): ?>
                                            <span class="badge badge-warning ml-2">Due Today!</span>
                                        <?php endif; ?>
                                    </dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>

                    <!-- Submit Work Form Card -->
                    <div class="card card-success shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Submit Your Work</h3>
                        </div>
                        
                        <?= form_open_multipart('my-tasks/store-work/' . $task['id']) ?>

                        <div class="card-body">

                            <!-- Status Selection -->
                            <div class="form-group">
                                <label for="status">Update Status <span class="text-danger">*</span></label>
                                <select id="status" 
                                        name="status" 
                                        class="form-control <?= $hasValidationErrors && $validation->hasError('status') ? 'is-invalid' : '' ?>" 
                                        required>
                                    <option value="">-- Select Status --</option>
                                    <option value="In Progress" <?= old('status') == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= old('status') == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                                <small class="form-text text-muted">
                                    Select "In Progress" if you're still working on it, or "Completed" if you've finished.
                                </small>
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
                                <small class="form-text text-muted">
                                    Explain what you've done, any challenges faced, or additional information.
                                </small>
                            </div>

                            <!-- Current Uploaded Files -->
                            <?php if (!empty($task['employee_files'])): ?>
                                <?php $employeeFiles = json_decode($task['employee_files'], true); ?>
                                <?php if (is_array($employeeFiles) && !empty($employeeFiles)): ?>
                                    <div class="form-group">
                                        <label>Previously Uploaded Files</label>
                                        <div class="alert alert-info">
                                            <p class="mb-2"><strong>You have <?= count($employeeFiles) ?> file(s) already uploaded:</strong></p>
                                            <ul class="mb-0">
                                                <?php foreach ($employeeFiles as $file): ?>
                                                    <li>
                                                        <?= esc(basename($file)) ?>
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                           class="btn btn-xs btn-info ml-2" 
                                                           target="_blank">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Upload Work Files -->
                            <div class="form-group">
                                <label for="work_files">Upload Work Files</label>
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
                                    Upload your completed work files (images, videos, documents, etc.). You can select multiple files.
                                    <?php if (!empty($task['employee_files'])): ?>
                                        <br><strong>Note:</strong> New files will be added to your existing uploads.
                                    <?php endif; ?>
                                </small>
                            </div>

                            <!-- Preview Area -->
                            <div id="filePreview" class="row" style="display: none;">
                                <div class="col-12">
                                    <label>Selected Files Preview:</label>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Submit Work
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
    // Update file input label and show preview
    $('#work_files').on('change', function(e) {
        var files = e.target.files;
        var fileCount = files.length;
        
        if (fileCount > 0) {
            var label = fileCount > 1 ? fileCount + ' files selected' : files[0].name;
            $(this).next('.custom-file-label').text(label);
            
            // Show file preview
            var previewHtml = '';
            for (var i = 0; i < fileCount; i++) {
                var file = files[i];
                var fileSize = (file.size / 1024).toFixed(2); // KB
                var fileSizeDisplay = fileSize > 1024 ? (fileSize / 1024).toFixed(2) + ' MB' : fileSize + ' KB';
                
                previewHtml += `
                    <div class="col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body p-2">
                                <i class="fas fa-file fa-2x text-success mb-1"></i>
                                <p class="mb-0 text-truncate small"><strong>${file.name}</strong></p>
                                <small class="text-muted">${fileSizeDisplay}</small>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            $('#filePreview').html(previewHtml).show();
        } else {
            $(this).next('.custom-file-label').text('Choose files...');
            $('#filePreview').hide();
        }
    });

    // Form validation reminder
    $('form').on('submit', function(e) {
        var status = $('#status').val();
        var files = $('#work_files')[0].files.length;
        var existingFiles = <?= !empty($task['employee_files']) ? 'true' : 'false' ?>;
        
        if (status === 'Completed' && files === 0 && !existingFiles) {
            if (!confirm('You are marking this task as Completed without uploading any files. Are you sure?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
