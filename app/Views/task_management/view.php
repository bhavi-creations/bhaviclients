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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('task-management') ?>">Employee Tasks</a></li>
                        <li class="breadcrumb-item active">Task Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Task Details Card -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-list"></i> Task Information
                            </h3>
                            <div class="card-tools">
                                <?php
                                $statusClass = [
                                    'Pending' => 'warning',
                                    'In Progress' => 'info',
                                    'Completed' => 'success',
                                    'Review' => 'primary'
                                ];
                                $class = $statusClass[$task['status']] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $class ?> badge-lg">
                                    <?= esc($task['status']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Task ID:</dt>
                                <dd class="col-sm-9">#<?= esc($task['id']) ?></dd>

                                <dt class="col-sm-3">Title:</dt>
                                <dd class="col-sm-9"><strong><?= esc($task['title']) ?></strong></dd>

                                <dt class="col-sm-3">Description:</dt>
                                <dd class="col-sm-9"><?= nl2br(esc($task['description'])) ?></dd>

                                <dt class="col-sm-3">Client:</dt>
                                <dd class="col-sm-9">
                                    <?php if (!empty($task['client_name'])): ?>
                                        <?= esc($task['client_name']) ?>
                                        <?php if (!empty($task['client_email'])): ?>
                                            <br><small class="text-muted"><?= esc($task['client_email']) ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not assigned to any client</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-3">Submitted At:</dt>
                                <dd class="col-sm-9">
                                    <?= date('F d, Y', strtotime($task['submitted_at'])) ?> 
                                    at <?= date('h:i A', strtotime($task['submitted_at'])) ?>
                                </dd>

                                <dt class="col-sm-3">Last Updated:</dt>
                                <dd class="col-sm-9">
                                    <?= date('F d, Y', strtotime($task['updated_at'])) ?> 
                                    at <?= date('h:i A', strtotime($task['updated_at'])) ?>
                                </dd>
                            </dl>

                            <!-- Attached Files Section -->
                            <?php if (!empty($task['files_upload'])): ?>
                                <?php $files = json_decode($task['files_upload'], true); ?>
                                <?php if (is_array($files) && !empty($files)): ?>
                                    <hr>
                                    <h5><i class="fas fa-paperclip"></i> Attached Files (<?= count($files) ?>)</h5>
                                    <div class="row mt-3">
                                        <?php foreach ($files as $index => $file): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body p-2 text-center">
                                                        <?php 
                                                        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                                        $isImage = in_array(strtolower($fileExtension), $imageExtensions);
                                                        ?>
                                                        
                                                        <?php if ($isImage): ?>
                                                            <!-- Image Preview -->
                                                            <a href="<?= base_url('uploads/task_files/' . $file) ?>" target="_blank">
                                                                <img src="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                                     class="img-fluid rounded mb-2" 
                                                                     style="max-height: 150px; object-fit: cover;"
                                                                     alt="<?= esc($file) ?>">
                                                            </a>
                                                        <?php else: ?>
                                                            <!-- File Icon -->
                                                            <div class="mb-2">
                                                                <?php
                                                                $iconClass = 'fa-file';
                                                                $iconColor = 'text-secondary';
                                                                
                                                                if (in_array(strtolower($fileExtension), ['pdf'])) {
                                                                    $iconClass = 'fa-file-pdf';
                                                                    $iconColor = 'text-danger';
                                                                } elseif (in_array(strtolower($fileExtension), ['doc', 'docx'])) {
                                                                    $iconClass = 'fa-file-word';
                                                                    $iconColor = 'text-primary';
                                                                } elseif (in_array(strtolower($fileExtension), ['xls', 'xlsx'])) {
                                                                    $iconClass = 'fa-file-excel';
                                                                    $iconColor = 'text-success';
                                                                } elseif (in_array(strtolower($fileExtension), ['zip', 'rar', '7z'])) {
                                                                    $iconClass = 'fa-file-archive';
                                                                    $iconColor = 'text-warning';
                                                                }
                                                                ?>
                                                                <i class="fas <?= $iconClass ?> fa-4x <?= $iconColor ?>"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <p class="mb-1 text-truncate" style="font-size: 12px;" title="<?= esc($file) ?>">
                                                            <?= esc($file) ?>
                                                        </p>
                                                        
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                           class="btn btn-sm btn-primary btn-block" 
                                                           target="_blank"
                                                           download>
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <hr>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No files attached to this task.
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('task-management') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tasks
                            </a>
                            
                            <?php if (session()->get('role_id') == 1): ?>
                                <button type="button" 
                                        class="btn btn-warning" 
                                        data-toggle="modal" 
                                        data-target="#statusModal">
                                    <i class="fas fa-edit"></i> Update Status
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="confirmDelete()">
                                    <i class="fas fa-trash"></i> Delete Task
                                </button>

                                <!-- Hidden Delete Form -->
                                <form id="deleteForm" 
                                      action="<?= base_url('task-management/delete/' . $task['id']) ?>" 
                                      method="post" 
                                      style="display:none;">
                                    <?= csrf_field() ?>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Employee Info Card -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user"></i> Employee Information
                            </h3>
                        </div>
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" 
                                     src="https://ui-avatars.com/api/?name=<?= urlencode($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?>&size=128&background=007bff&color=fff" 
                                     alt="Employee">
                            </div>

                            <h3 class="profile-username text-center mt-2">
                                <?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?>
                            </h3>

                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope mr-2"></i>Email</b>
                                    <p class="text-muted mb-0"><?= esc($task['emp_email'] ?? 'N/A') ?></p>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-phone mr-2"></i>Phone</b>
                                    <p class="text-muted mb-0"><?= esc($task['emp_phone'] ?? 'N/A') ?></p>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-building mr-2"></i>Department</b>
                                    <p class="text-muted mb-0"><?= esc($task['department_name'] ?? 'N/A') ?></p>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i> Task Statistics
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-paperclip"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Attached Files</span>
                                    <span class="info-box-number">
                                        <?php
                                        $fileCount = 0;
                                        if (!empty($task['files_upload'])) {
                                            $files = json_decode($task['files_upload'], true);
                                            $fileCount = is_array($files) ? count($files) : 0;
                                        }
                                        echo $fileCount;
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Status Update Modal (Admin Only) -->
<?php if (session()->get('role_id') == 1): ?>
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Task Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('task-management/update-status/' . $task['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Current Status: 
                                <span class="badge badge-<?= $class ?>">
                                    <?= esc($task['status']) ?>
                                </span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>New Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="Pending" <?= $task['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Review" <?= $task['status'] == 'Review' ? 'selected' : '' ?>>Review</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this task? All associated files will also be deleted. This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?= $this->endSection() ?>
