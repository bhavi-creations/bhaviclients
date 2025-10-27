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

            <div class="row">
                <!-- Work Details Card -->
                <div class="col-md-8">
                    <div class="card">
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
                                    <i class="fas fa-info-circle"></i> No files attached to this work.
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('work-updates') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Work Updates
                            </a>
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
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
