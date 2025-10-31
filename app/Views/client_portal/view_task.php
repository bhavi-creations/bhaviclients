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
                        <li class="breadcrumb-item"><a href="<?= base_url('client-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('work-updates') ?>">Work Updates</a></li>
                        <li class="breadcrumb-item active">Work Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Work Details Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Work Information
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
                        <dt class="col-sm-3">Work Title:</dt>
                        <dd class="col-sm-9"><strong><?= esc($task['title']) ?></strong></dd>

                        <dt class="col-sm-3">Description:</dt>
                        <dd class="col-sm-9"><?= nl2br(esc($task['description'])) ?></dd>

                        <?php if (!empty($task['priority'])): ?>
                            <dt class="col-sm-3">Priority:</dt>
                            <dd class="col-sm-9">
                                <?php
                                $priorityClass = [
                                    'Low' => 'secondary',
                                    'Medium' => 'info',
                                    'High' => 'warning',
                                    'Urgent' => 'danger'
                                ];
                                $pClass = $priorityClass[$task['priority']] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $pClass ?>">
                                    <?= esc($task['priority']) ?>
                                </span>
                            </dd>
                        <?php endif; ?>

                        <?php if (!empty($task['due_date'])): ?>
                            <dt class="col-sm-3">Due Date:</dt>
                            <dd class="col-sm-9">
                                <i class="far fa-calendar-alt"></i>
                                <?= date('F d, Y', strtotime($task['due_date'])) ?>
                            </dd>
                        <?php endif; ?>

                        <dt class="col-sm-3">Submitted At:</dt>
                        <dd class="col-sm-9">
                            <i class="far fa-calendar"></i>
                            <?= date('F d, Y', strtotime($task['submitted_at'])) ?> 
                            at <?= date('h:i A', strtotime($task['submitted_at'])) ?>
                        </dd>

                        <dt class="col-sm-3">Last Updated:</dt>
                        <dd class="col-sm-9">
                            <i class="far fa-clock"></i>
                            <?= date('F d, Y', strtotime($task['updated_at'])) ?> 
                            at <?= date('h:i A', strtotime($task['updated_at'])) ?>
                        </dd>

                        <dt class="col-sm-3">Employee:</dt>
                        <dd class="col-sm-9">
                            <i class="fas fa-user-tie text-primary"></i>
                            <strong><?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></strong>
                            <?php if (!empty($task['department_name'])): ?>
                                <br><small class="text-muted">
                                    <i class="fas fa-building"></i> <?= esc($task['department_name']) ?>
                                </small>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="row">
                <!-- Admin Manager Files & Remarks -->
                <div class="col-md-6">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-shield"></i> Admin Manager Section
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Admin Remarks -->
                            <?php if (!empty($task['admin_remarks'])): ?>
                                <h5><i class="fas fa-comment-dots"></i> Admin Remarks</h5>
                                <div class="alert alert-warning">
                                    <?= nl2br(esc($task['admin_remarks'])) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Admin Files -->
                            <h5>
                                <i class="fas fa-paperclip"></i> Admin Files
                                <?php
                                $adminFiles = !empty($task['admin_files']) ? json_decode($task['admin_files'], true) : [];
                                $adminFilesCount = is_array($adminFiles) ? count($adminFiles) : 0;
                                ?>
                                <span class="badge badge-warning"><?= $adminFilesCount ?></span>
                            </h5>

                            <?php if (!empty($adminFiles) && is_array($adminFiles)): ?>
                                <div class="row">
                                    <?php foreach ($adminFiles as $index => $file): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body p-2 text-center">
                                                    <?php 
                                                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                                                    $isImage = in_array(strtolower($fileExtension), $imageExtensions);
                                                    ?>
                                                    
                                                    <?php if ($isImage): ?>
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" target="_blank">
                                                            <img src="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                                 class="img-fluid rounded mb-2" 
                                                                 style="max-height: 120px; object-fit: cover;"
                                                                 alt="<?= esc($file) ?>">
                                                        </a>
                                                    <?php else: ?>
                                                        <div class="mb-2">
                                                            <?php
                                                            $iconClass = 'fa-file';
                                                            $iconColor = 'text-secondary';
                                                            
                                                            if ($fileExtension == 'pdf') {
                                                                $iconClass = 'fa-file-pdf';
                                                                $iconColor = 'text-danger';
                                                            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                                                $iconClass = 'fa-file-word';
                                                                $iconColor = 'text-primary';
                                                            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                                                $iconClass = 'fa-file-excel';
                                                                $iconColor = 'text-success';
                                                            } elseif (in_array($fileExtension, ['zip', 'rar', '7z'])) {
                                                                $iconClass = 'fa-file-archive';
                                                                $iconColor = 'text-warning';
                                                            }
                                                            ?>
                                                            <i class="fas <?= $iconClass ?> fa-3x <?= $iconColor ?>"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <p class="mb-1 text-truncate" style="font-size: 11px;" title="<?= esc($file) ?>">
                                                        <?= esc(strlen($file) > 20 ? substr($file, 0, 20) . '...' : $file) ?>
                                                    </p>
                                                    
                                                    <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                       class="btn btn-xs btn-warning btn-block" 
                                                       target="_blank"
                                                       download>
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No files uploaded by admin
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Employee Files & Remarks -->
                <div class="col-md-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-tie"></i> Employee Section
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Employee Remarks -->
                            <?php if (!empty($task['employee_remarks'])): ?>
                                <h5><i class="fas fa-comment"></i> Employee Remarks</h5>
                                <div class="alert alert-success">
                                    <?= nl2br(esc($task['employee_remarks'])) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Employee Files -->
                            <h5>
                                <i class="fas fa-paperclip"></i> Employee Files
                                <?php
                                $employeeFiles = !empty($task['employee_files']) ? json_decode($task['employee_files'], true) : [];
                                $employeeFilesCount = is_array($employeeFiles) ? count($employeeFiles) : 0;
                                ?>
                                <span class="badge badge-success"><?= $employeeFilesCount ?></span>
                            </h5>

                            <?php if (!empty($employeeFiles) && is_array($employeeFiles)): ?>
                                <div class="row">
                                    <?php foreach ($employeeFiles as $index => $file): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body p-2 text-center">
                                                    <?php 
                                                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                                                    $isImage = in_array(strtolower($fileExtension), $imageExtensions);
                                                    ?>
                                                    
                                                    <?php if ($isImage): ?>
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" target="_blank">
                                                            <img src="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                                 class="img-fluid rounded mb-2" 
                                                                 style="max-height: 120px; object-fit: cover;"
                                                                 alt="<?= esc($file) ?>">
                                                        </a>
                                                    <?php else: ?>
                                                        <div class="mb-2">
                                                            <?php
                                                            $iconClass = 'fa-file';
                                                            $iconColor = 'text-secondary';
                                                            
                                                            if ($fileExtension == 'pdf') {
                                                                $iconClass = 'fa-file-pdf';
                                                                $iconColor = 'text-danger';
                                                            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                                                $iconClass = 'fa-file-word';
                                                                $iconColor = 'text-primary';
                                                            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                                                $iconClass = 'fa-file-excel';
                                                                $iconColor = 'text-success';
                                                            } elseif (in_array($fileExtension, ['zip', 'rar', '7z'])) {
                                                                $iconClass = 'fa-file-archive';
                                                                $iconColor = 'text-warning';
                                                            }
                                                            ?>
                                                            <i class="fas <?= $iconClass ?> fa-3x <?= $iconColor ?>"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <p class="mb-1 text-truncate" style="font-size: 11px;" title="<?= esc($file) ?>">
                                                        <?= esc(strlen($file) > 20 ? substr($file, 0, 20) . '...' : $file) ?>
                                                    </p>
                                                    
                                                    <a href="<?= base_url('uploads/task_files/' . $file) ?>" 
                                                       class="btn btn-xs btn-success btn-block" 
                                                       target="_blank"
                                                       download>
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No files uploaded by employee
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="row">
                <div class="col-12">
                    <a href="<?= base_url('work-updates') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Work Updates
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
