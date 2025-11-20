<?php
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
                        <li class="breadcrumb-item active">Add Task</li>
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

                    <div class="card card-success shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-plus-circle mr-2"></i>Create a Task / Work Log</h3>
                        </div>

                        <?= form_open_multipart('my-tasks/store') ?>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Select Client <span class="text-danger">*</span></label>
                                        <select name="client_id" id="client_id"
                                                class="form-control <?= $hasValidationErrors && $validation->hasError('client_id') ? 'is-invalid' : '' ?>"
                                                required>
                                            <option value="">-- Choose Client --</option>
                                            <?php if (!empty($clients)): ?>
                                                <?php foreach ($clients as $client): ?>
                                                    <option value="<?= $client['id'] ?>"
                                                        <?= old('client_id') == $client['id'] ? 'selected' : '' ?>>
                                                        <?= esc($client['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php if ($hasValidationErrors && $validation->hasError('client_id')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('client_id') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Priority <span class="text-danger">*</span></label>
                                        <select name="priority" id="priority"
                                                class="form-control <?= $hasValidationErrors && $validation->hasError('priority') ? 'is-invalid' : '' ?>"
                                                required>
                                            <option value="Low" <?= old('priority') == 'Low' ? 'selected' : '' ?>>Low</option>
                                            <option value="Medium" <?= old('priority', 'Medium') == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="High" <?= old('priority') == 'High' ? 'selected' : '' ?>>High</option>
                                            <option value="Urgent" <?= old('priority') == 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                                        </select>
                                        <?php if ($hasValidationErrors && $validation->hasError('priority')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('priority') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title">Task Title <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="<?= old('title') ?>"
                                       class="form-control <?= $hasValidationErrors && $validation->hasError('title') ? 'is-invalid' : '' ?>"
                                       placeholder="e.g. Daily social media updates"
                                       required>
                                <?php if ($hasValidationErrors && $validation->hasError('title')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('title') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="description">Task Description <span class="text-danger">*</span></label>
                                <textarea name="description"
                                          id="description"
                                          rows="5"
                                          class="form-control <?= $hasValidationErrors && $validation->hasError('description') ? 'is-invalid' : '' ?>"
                                          placeholder="Describe what you worked on or plan to work on."
                                          required><?= old('description') ?></textarea>
                                <?php if ($hasValidationErrors && $validation->hasError('description')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('description') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Update Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status"
                                                class="form-control <?= $hasValidationErrors && $validation->hasError('status') ? 'is-invalid' : '' ?>"
                                                required>
                                            <option value="">-- Select Status --</option>
                                            <option value="Pending" <?= old('status') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="In Progress" <?= old('status', 'In Progress') == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="Completed" <?= old('status') == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                        <?php if ($hasValidationErrors && $validation->hasError('status')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('status') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_remarks">My Notes / Comments</label>
                                        <textarea name="employee_remarks"
                                                  id="employee_remarks"
                                                  rows="3"
                                                  class="form-control"
                                                  placeholder="Add any additional context..."><?= old('employee_remarks') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="work_files">Upload Work Files</label>
                                <div class="custom-file">
                                    <input type="file"
                                           name="work_files[]"
                                           id="work_files"
                                           class="custom-file-input"
                                           multiple
                                           accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                    <label class="custom-file-label" for="work_files">Choose files...</label>
                                </div>
                                <small class="form-text text-muted">
                                    Upload any supporting documents, screenshots, or deliverables. Multiple files allowed.
                                </small>
                            </div>

                            <div id="filePreview" class="row" style="display:none;">
                                <div class="col-12">
                                    <label>Selected Files Preview:</label>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Save Task
                            </button>
                            <a href="<?= base_url('my-tasks') ?>" class="btn btn-secondary float-right">Cancel</a>
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
    $('#work_files').on('change', function(e) {
        const files = e.target.files;
        const fileCount = files.length;

        if (fileCount > 0) {
            const label = fileCount > 1 ? fileCount + ' files selected' : files[0].name;
            $(this).next('.custom-file-label').text(label);

            let previewHtml = '';
            for (let i = 0; i < fileCount; i++) {
                const file = files[i];
                const sizeKB = (file.size / 1024).toFixed(2);
                const displaySize = sizeKB > 1024 ? (sizeKB / 1024).toFixed(2) + ' MB' : sizeKB + ' KB';

                previewHtml += `
                    <div class="col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body p-2">
                                <i class="fas fa-file fa-2x text-success mb-1"></i>
                                <p class="mb-0 text-truncate small"><strong>${file.name}</strong></p>
                                <small class="text-muted">${displaySize}</small>
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
});
</script>
<?= $this->endSection() ?>

