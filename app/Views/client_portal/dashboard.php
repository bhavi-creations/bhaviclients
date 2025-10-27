<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Welcome, <?= esc($client['name']) ?>!</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalTasks ?></h3>
                            <p>Work Updates</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="<?= base_url('work-updates') ?>" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $totalFiles ?></h3>
                            <p>Files Available</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <a href="<?= base_url('download-files') ?>" class="small-box-footer">
                            Download <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><i class="fas fa-cloud-upload-alt"></i></h3>
                            <p>Upload Files</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-upload"></i>
                        </div>
                        <a href="<?= base_url('upload-files') ?>" class="small-box-footer">
                            Upload Now <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Work Updates -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tasks"></i> Recent Work Updates
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('work-updates') ?>" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentTasks)): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Employee</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentTasks as $task): ?>
                                            <tr>
                                                <td><?= esc($task['title']) ?></td>
                                                <td><?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'Pending' => 'warning',
                                                        'In Progress' => 'info',
                                                        'Completed' => 'success',
                                                        'Review' => 'primary'
                                                    ];
                                                    $class = $statusClass[$task['status']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge badge-<?= $class ?>">
                                                        <?= esc($task['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?= date('M d, Y', strtotime($task['submitted_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('view-work/' . $task['id']) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-info-circle"></i> No work updates yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Company Info & Recent Files -->
                <div class="col-md-4">
                    <!-- Company Info -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building"></i> Company Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <strong>Company Name</strong>
                            <p class="text-muted"><?= esc($client['name']) ?></p>
                            <hr>
                            
                            <strong>Contact Person</strong>
                            <p class="text-muted">
                                <?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>
                            </p>
                            <hr>
                            
                            <strong>Email</strong>
                            <p class="text-muted"><?= esc($client['email']) ?></p>
                            <hr>
                            
                            <strong>Phone</strong>
                            <p class="text-muted"><?= esc($client['phone']) ?></p>
                        </div>
                    </div>

                    <!-- Recent Files -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file"></i> Recent Files
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('download-files') ?>" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentFiles)): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($recentFiles as $file): ?>
                                        <li class="list-group-item">
                                            <i class="fas fa-file-excel mr-2 text-success"></i>
                                            <small class="text-truncate d-inline-block" style="max-width: 150px;">
                                                <?= esc($file['original_name']) ?>
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                                            </small>
                                            <a href="<?= base_url('download-file/' . $file['id']) ?>" 
                                               class="btn btn-xs btn-success float-right">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-info-circle"></i> No files yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
