<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\task_management\edit.php 

$session = \Config\Services::session();
$validation = $validation ?? \Config\Services::validation();

function get_value($field, $task_data, $default = '') {
    return old($field) !== null ? old($field) : ($task_data[$field] ?? $default);
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
                        <li class="breadcrumb-item"><a href="<?= base_url('task-management') ?>">Tasks</a></li>
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
                            <h3 class="card-title">Edit Task</h3>
                        </div>

                        <?= form_open_multipart(base_url('task-management/update/' . $task['id'])) ?>
                        <div class="card-body">

                            <!-- Employee Selection -->
                            <div class="form-group">
                                <label for="employee_id">Assign To (Employee) <span class="text-danger">*</span></label>
                                <select id="employee_id" 
                                        name="employee_id" 
                                        class="form-control select2 <?= $validation->hasError('employee_id') ? 'is-invalid' : '' ?>" 
                                        required>
                                    <option value="">-- Select Employee --</option>
                                    <?php 
                                    $currentEmployeeId = get_value('employee_id', $task);
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

                            <!-- Client Selection -->
                            <div class="form-group">
                                <label for="client_id">Client (Optional)</label>
                                <select id="client_id" name="client_id" class="form-control select2">
                                    <option value="">-- No Client --</option>
                                    <?php 
                                    $currentClientId = get_value('client_id', $task);
                                    foreach ($clients as $client): 
                                    ?>
                                        <option value="<?= esc($client['id']) ?>" <?= $currentClientId == $client['id'] ? 'selected' : '' ?>>
                                            <?= esc($client['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Task Title -->
                            <div class="form-group">
                                <label for="title">Task Title <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>" 
                                       id="title" 
                                       name="title" 
                                       value="<?= esc(get_value('title', $task)) ?>"
                                       required>
                                <?php if ($validation->hasError('title')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('title') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description">Task Description <span class="text-danger">*</span></label>
                                <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          required><?= esc(get_value('description', $task)) ?></textarea>
                                <?php if ($validation->hasError('description')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('description') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Admin Remarks -->
                            <div class="form-group">
                                <label for="admin_remarks">Admin Notes/Instructions</label>
                                <textarea class="form-control" 
                                          id="admin_remarks" 
                                          name="admin_remarks" 
                                          rows="3"><?= esc(get_value('admin_remarks', $task)) ?></textarea>
                            </div>

                            <!-- Current Admin Reference Files -->
                            <?php if (!empty($task['admin_files'])): ?>
                                <?php $adminFiles = json_decode($task['admin_files'], true); ?>
                                <?php if (is_array($adminFiles) && !empty($adminFiles)): ?>
                                    <div class="form-group">
                                        <label>Current Reference Files</label>
                                        <div class="row">
                                            <?php foreach ($adminFiles as $index => $file): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card">
                                                        <div class="card-body p-2 text-center">
                                                            <i class="fas fa-file fa-2x text-info mb-1"></i>
                                                            <p class="mb-1 text-truncate small"><?= esc($file) ?></p>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                                   class="btn btn-info btn-sm" 
                                                                   target="_blank" 
                                                                   title="View">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button type="button" 
                                                                        class="btn btn-danger btn-sm" 
                                                                        onclick="removeFile('<?= $index ?>')" 
                                                                        title="Remove">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <input type="hidden" 
                                                                   name="existing_files[]" 
                                                                   value="<?= esc($file) ?>" 
                                                                   id="file_<?= $index ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Add New Admin Reference Files -->
                            <div class="form-group">
                                <label for="admin_files">Add More Reference Files (Optional)</label>
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
                                    Upload additional reference materials for the employee.
                                </small>
                            </div>

                            <div class="row">
                                <!-- Priority -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="priority">Priority <span class="text-danger">*</span></label>
                                        <select name="priority" id="priority" class="form-control" required>
                                            <?php $currentPriority = get_value('priority', $task, 'Medium'); ?>
                                            <option value="Low" <?= $currentPriority == 'Low' ? 'selected' : '' ?>>Low</option>
                                            <option value="Medium" <?= $currentPriority == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="High" <?= $currentPriority == 'High' ? 'selected' : '' ?>>High</option>
                                            <option value="Urgent" <?= $currentPriority == 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control" required>
                                            <?php $currentStatus = get_value('status', $task, 'Pending'); ?>
                                            <option value="Pending" <?= $currentStatus == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="In Progress" <?= $currentStatus == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="Completed" <?= $currentStatus == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Review" <?= $currentStatus == 'Review' ? 'selected' : '' ?>>Review</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Due Date -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="due_date" 
                                               name="due_date" 
                                               value="<?= esc(get_value('due_date', $task)) ?>">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Task
                            </button>
                            <a href="<?= base_url('task-management') ?>" class="btn btn-default float-right">Cancel</a>
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

function removeFile(index) {
    if (confirm('Are you sure you want to remove this file?')) {
        document.getElementById('file_' + index).remove();
        event.target.closest('.col-md-3').remove();
    }
}
</script>
<?= $this->endSection() ?>
