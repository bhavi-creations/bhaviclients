<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\task_management\view.php 
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

            <div class="row">
                <div class="col-md-8">
                    <!-- Task Details Card -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-tasks"></i> Task Information</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('task-management/edit/' . $task['id']) ?>" class="btn btn-tool text-white">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Task Title:</dt>
                                <dd class="col-sm-9"><strong><?= esc($task['title']) ?></strong></dd>

                                <dt class="col-sm-3">Description:</dt>
                                <dd class="col-sm-9"><?= nl2br(esc($task['description'])) ?></dd>

                                <?php if (!empty($task['admin_remarks'])): ?>
                                    <dt class="col-sm-3">Admin Notes:</dt>
                                    <dd class="col-sm-9">
                                        <div class="alert alert-info">
                                            <?= nl2br(esc($task['admin_remarks'])) ?>
                                        </div>
                                    </dd>
                                <?php endif; ?>

                                <?php if (!empty($task['employee_remarks'])): ?>
                                    <dt class="col-sm-3">Employee Notes:</dt>
                                    <dd class="col-sm-9">
                                        <div class="alert alert-warning">
                                            <?= nl2br(esc($task['employee_remarks'])) ?>
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
                                    <dd class="col-sm-9"><?= date('M d, Y', strtotime($task['due_date'])) ?></dd>
                                <?php endif; ?>

                                <dt class="col-sm-3">Created At:</dt>
                                <dd class="col-sm-9"><?= date('M d, Y h:i A', strtotime($task['created_at'])) ?></dd>

                                <?php if (!empty($task['submitted_at'])): ?>
                                    <dt class="col-sm-3">Submitted At:</dt>
                                    <dd class="col-sm-9"><?= date('M d, Y h:i A', strtotime($task['submitted_at'])) ?></dd>
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
                                    <h3 class="card-title"><i class="fas fa-folder-open"></i> Reference Files (Admin Uploaded)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($adminFiles as $file): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center p-2">
                                                        <?php
                                                        $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                        ?>
                                                        <?php if (in_array($fileExt, $imageExts)): ?>
                                                            <img src="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                                 class="img-fluid mb-2" 
                                                                 style="max-height: 100px;">
                                                        <?php else: ?>
                                                            <i class="fas fa-file fa-3x text-info mb-2"></i>
                                                        <?php endif; ?>
                                                        <p class="mb-1 text-truncate small"><?= esc($file) ?></p>
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                           class="btn btn-sm btn-info btn-block" 
                                                           target="_blank">
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

                    <!-- Employee Work Files Card -->
                    <?php if (!empty($task['employee_files'])): ?>
                        <?php $employeeFiles = json_decode($task['employee_files'], true); ?>
                        <?php if (is_array($employeeFiles) && !empty($employeeFiles)): ?>
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-paperclip"></i> Work Files (Employee Uploaded)</h3>
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
                                                            <img src="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                                 class="img-fluid mb-2" 
                                                                 style="max-height: 100px;">
                                                        <?php else: ?>
                                                            <i class="fas fa-file fa-3x text-success mb-2"></i>
                                                        <?php endif; ?>
                                                        <p class="mb-1 text-truncate small"><?= esc($file) ?></p>
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                           class="btn btn-sm btn-success btn-block" 
                                                           target="_blank">
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
                </div>

                <div class="col-md-4">
                    <!-- Employee Info Card -->
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user"></i> Assigned To</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></p>
                            <p><strong>Email:</strong> <?= esc($task['emp_email']) ?></p>
                            <p><strong>Phone:</strong> <?= esc($task['emp_phone']) ?></p>
                            <p><strong>Department:</strong> <?= esc($task['department_name'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <!-- Client Info Card -->
                    <?php if (!empty($task['client_name'])): ?>
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-handshake"></i> Client</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> <?= esc($task['client_name']) ?></p>
                                <?php if (!empty($task['client_email'])): ?>
                                    <p><strong>Email:</strong> <?= esc($task['client_email']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Assigned By Card -->
                    <?php if (!empty($task['assigned_by_name'])): ?>
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user-tie"></i> Assigned By</h3>
                            </div>
                            <div class="card-body">
                                <p><?= esc($task['assigned_by_name'] . ' ' . $task['assigned_by_lastname']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('task-management') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tasks
            </a>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
