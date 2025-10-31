<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\view_task.php 
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
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <!-- Task Details Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tasks"></i> Task Information</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Task Title:</dt>
                                <dd class="col-sm-9"><strong><?= esc($task['title']) ?></strong></dd>

                                <dt class="col-sm-3">Description:</dt>
                                <dd class="col-sm-9"><?= nl2br(esc($task['description'])) ?></dd>

                                <?php if (!empty($task['admin_remarks'])): ?>
                                    <dt class="col-sm-3">Instructions:</dt>
                                    <dd class="col-sm-9">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle"></i>
                                            <?= nl2br(esc($task['admin_remarks'])) ?>
                                        </div>
                                    </dd>
                                <?php endif; ?>

                                <dt class="col-sm-3">Priority:</dt>
                                <dd class="col-sm-9">
                                    <?php
                                    $priorityBadge = [
                                        'Low' => 'secondary',
                                        'Medium' => 'info',
                                        'High' => 'warning',
                                        'Urgent' => 'danger'
                                    ];
                                    $badge = $priorityBadge[$task['priority']] ?? 'secondary';
                                    ?>
                                    <span class="badge badge-<?= $badge ?> badge-lg"><?= esc($task['priority']) ?></span>
                                </dd>

                                <dt class="col-sm-3">Status:</dt>
                                <dd class="col-sm-9">
                                    <?php
                                    $statusBadge = [
                                        'Pending' => 'warning',
                                        'In Progress' => 'info',
                                        'Completed' => 'success',
                                        'Review' => 'primary'
                                    ];
                                    $badge = $statusBadge[$task['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge badge-<?= $badge ?> badge-lg"><?= esc($task['status']) ?></span>
                                </dd>

                                <?php if (!empty($task['due_date'])): ?>
                                    <dt class="col-sm-3">Due Date:</dt>
                                    <dd class="col-sm-9">
                                        <strong><?= date('M d, Y', strtotime($task['due_date'])) ?></strong>
                                        <?php
                                        $today = strtotime(date('Y-m-d'));
                                        $dueDate = strtotime($task['due_date']);
                                        $daysLeft = floor(($dueDate - $today) / 86400);
                                        ?>
                                        <?php if ($daysLeft < 0): ?>
                                            <span class="badge badge-danger ml-2">Overdue by <?= abs($daysLeft) ?> day(s)</span>
                                        <?php elseif ($daysLeft == 0): ?>
                                            <span class="badge badge-warning ml-2">Due Today!</span>
                                        <?php elseif ($daysLeft <= 3): ?>
                                            <span class="badge badge-warning ml-2"><?= $daysLeft ?> day(s) left</span>
                                        <?php endif; ?>
                                    </dd>
                                <?php endif; ?>

                                <dt class="col-sm-3">Assigned On:</dt>
                                <dd class="col-sm-9"><?= date('M d, Y h:i A', strtotime($task['created_at'])) ?></dd>

                                <?php if (!empty($task['submitted_at'])): ?>
                                    <dt class="col-sm-3">Submitted At:</dt>
                                    <dd class="col-sm-9"><?= date('M d, Y h:i A', strtotime($task['submitted_at'])) ?></dd>
                                <?php endif; ?>

                                <?php if (!empty($task['client_name'])): ?>
                                    <dt class="col-sm-3">Client:</dt>
                                    <dd class="col-sm-9"><?= esc($task['client_name']) ?></dd>
                                <?php endif; ?>

                                <?php if (!empty($task['assigned_by_name'])): ?>
                                    <dt class="col-sm-3">Assigned By:</dt>
                                    <dd class="col-sm-9"><?= esc($task['assigned_by_name'] . ' ' . $task['assigned_by_lastname']) ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>

                    <!-- Admin Reference Files Card -->
                    <?php if (!empty($task['admin_files'])): ?>
                        <?php $adminFiles = json_decode($task['admin_files'], true); ?>
                        <?php if (is_array($adminFiles) && !empty($adminFiles)): ?>
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-folder-open"></i> Reference Materials</h3>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        Download these reference files to understand what needs to be done
                                    </p>
                                    <div class="row">
                                        <?php foreach ($adminFiles as $file): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center p-2">
                                                        <?php
                                                        $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                        $videoExts = ['mp4', 'avi', 'mov', 'wmv'];
                                                        ?>
                                                        <?php if (in_array($fileExt, $imageExts)): ?>
                                                            <a href="<?= base_url('uploads/task_files/' . $file) ?>" target="_blank">
                                                                <img src="<?= base_url('uploads/task_files/' . $file) ?>"
                                                                    class="img-fluid mb-2"
                                                                    style="max-height: 100px;">
                                                            </a>
                                                        <?php elseif (in_array($fileExt, $videoExts)): ?>
                                                            <i class="fas fa-video fa-3x text-danger mb-2"></i>
                                                        <?php elseif ($fileExt == 'pdf'): ?>
                                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-file fa-3x text-info mb-2"></i>
                                                        <?php endif; ?>
                                                        <p class="mb-1 text-truncate small"><?= esc(basename($file)) ?></p>
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>"
                                                            class="btn btn-sm btn-info btn-block"
                                                            download>
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- My Submitted Work Files Card -->
                    <?php if (!empty($task['employee_files'])): ?>
                        <?php $employeeFiles = json_decode($task['employee_files'], true); ?>
                        <?php if (is_array($employeeFiles) && !empty($employeeFiles)): ?>
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-paperclip"></i> My Submitted Work</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($employeeFiles as $file): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center p-2">
                                                        <?php
                                                        $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                        ?>
                                                        <?php if (in_array($fileExt, $imageExts)): ?>
                                                            <a href="<?= base_url('uploads/task_files/' . $file) ?>" target="_blank">
                                                                <img src="<?= base_url('uploads/task_files/' . $file) ?>"
                                                                    class="img-fluid mb-2"
                                                                    style="max-height: 100px;">
                                                            </a>
                                                        <?php else: ?>
                                                            <i class="fas fa-file fa-3x text-success mb-2"></i>
                                                        <?php endif; ?>
                                                        <p class="mb-1 text-truncate small"><?= esc(basename($file)) ?></p>
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>"
                                                            class="btn btn-sm btn-success btn-block"
                                                            download>
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- My Notes Card -->
                    <?php if (!empty($task['employee_remarks'])): ?>
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-sticky-note"></i> My Notes</h3>
                            </div>
                            <div class="card-body">
                                <?= nl2br(esc($task['employee_remarks'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <!-- Quick Actions Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <!-- Edit button always visible -->
                            <a href="<?= base_url('my-tasks/edit/' . $task['id']) ?>"
                                class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-edit"></i> Edit Work Submission
                            </a>

                            <?php if ($task['status'] != 'Completed'): ?>
                                <a href="<?= base_url('my-tasks/submit/' . $task['id']) ?>"
                                    class="btn btn-success btn-block">
                                    <i class="fas fa-upload"></i> Submit Work
                                </a>
                            <?php else: ?>
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle"></i> Task completed!<br>
                                    <small>You can still edit your submission</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status Update Card -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-sync"></i> Quick Status Update</h3>
                        </div>
                        <div class="card-body">
                            <?= form_open('my-tasks/update-status/' . $task['id']) ?>
                            <div class="form-group">
                                <label>Change Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Pending" <?= $task['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Back Button -->
            <a href="<?= base_url('my-tasks') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to My Tasks
            </a>

        </div>
    </section>
</div>

<?= $this->endSection() ?>