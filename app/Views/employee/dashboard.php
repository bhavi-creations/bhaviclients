<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\dashboard.php
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-tachometer-alt"></i>
                        <?= esc($title ?? 'Employee Dashboard') ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Welcome Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2">
                                        <i class="fas fa-hand-wave text-warning"></i>
                                        Welcome back, <strong><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></strong>!
                                    </h3>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-building"></i> <?= esc($employee['department_name'] ?? 'N/A') ?> Department
                                        <?php if (!empty($employee['employee_code'])): ?>
                                            &nbsp;|&nbsp; <i class="fas fa-id-badge"></i> <?= esc($employee['employee_code']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <p class="mb-1"><small class="text-muted">Today's Date</small></p>
                                    <h5><i class="far fa-calendar-alt"></i> <?= date('M d, Y') ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- NEW: Notifications Widget Row (Top) -->
            <div class="row">
                <div class="col-md-12">
                    <?= view('components/notifications_widget') ?>
                </div>
            </div>
            <!-- Stats Widgets Row 1 -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= esc($totalTasks ?? 0) ?></h3>
                            <p>Total Tasks</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="<?= base_url('my-tasks') ?>" class="small-box-footer">
                            View All Tasks <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= esc($completedTasks ?? 0) ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="<?= base_url('my-tasks') ?>" class="small-box-footer">
                            View Completed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= esc($pendingTasks ?? 0) ?></h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="<?= base_url('my-tasks') ?>" class="small-box-footer">
                            View Pending <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= esc($reviewTasks ?? 0) ?></h3>
                            <p>In Review</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <a href="<?= base_url('my-tasks') ?>" class="small-box-footer">
                            View Review <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Access Cards Row 2 -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="info-box bg-gradient-primary">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current Salary</span>
                            <span class="info-box-number">
                                <?php if (!empty($latestSalary)): ?>
                                    ₹<?= number_format($latestSalary['salary_amount'], 2) ?>
                                <?php else: ?>
                                    Not Set
                                <?php endif; ?>
                            </span>
                            <a href="<?= base_url('my-details') ?>" class="text-white">
                                <small>View Details <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">My Payslips</span>
                            <span class="info-box-number"><?= esc($payslipCount) ?> Payslips</span>
                            <a href="<?= base_url('my-payslips') ?>" class="text-white">
                                <small>View Payslips <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="fas fa-folder-open"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Client Assets</span>
                            <span class="info-box-number"><?= esc($clientAssetsCount) ?> Assets</span>
                            <a href="<?= base_url('employee-client-assets') ?>" class="text-white">
                                <small>View Assets <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Recent Tasks Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Recent Tasks
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('my-tasks') ?>" class="btn btn-tool btn-sm">
                                    <i class="fas fa-external-link-alt"></i> View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentTasks)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover m-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">S.No.</th>
                                                <th>Title</th>
                                                <th>Client</th>
                                                <th style="width: 120px;">Status</th>
                                                <th style="width: 150px;">Submitted</th>
                                                <th style="width: 100px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sno = 1; ?>
                                            <?php foreach ($recentTasks as $task): ?>
                                                <tr>
                                                    <td><?= $sno++ ?></td>
                                                    <td>
                                                        <strong><?= esc($task['title']) ?></strong>
                                                        <?php if (!empty($task['description'])): ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?= esc(strlen($task['description']) > 50 ? substr($task['description'], 0, 50) . '...' : $task['description']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($task['client_name'])): ?>
                                                            <i class="fas fa-user-tie text-primary"></i> <?= esc($task['client_name']) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = 'secondary';
                                                        if ($task['status'] == 'Completed') {
                                                            $statusClass = 'success';
                                                        } elseif ($task['status'] == 'In Progress') {
                                                            $statusClass = 'warning';
                                                        } elseif ($task['status'] == 'Review') {
                                                            $statusClass = 'info';
                                                        } elseif ($task['status'] == 'Pending') {
                                                            $statusClass = 'danger';
                                                        }
                                                        ?>
                                                        <span class="badge badge-<?= $statusClass ?>">
                                                            <?= esc($task['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($task['submitted_at'])): ?>
                                                            <small>
                                                                <i class="far fa-clock"></i>
                                                                <?= date('M d, Y', strtotime($task['submitted_at'])) ?>
                                                            </small>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('my-tasks/view/' . $task['id']) ?>"
                                                            class="btn btn-sm btn-info"
                                                            title="View Task">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="p-5 text-center">
                                    <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Recent Tasks</h5>
                                    <p class="text-muted">You don't have any tasks yet. Check back later!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($recentTasks)): ?>
                            <div class="card-footer text-center">
                                <a href="<?= base_url('my-tasks') ?>" class="btn btn-primary">
                                    <i class="fas fa-list"></i> View All Tasks
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt"></i> Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="<?= base_url('my-tasks') ?>" class="btn btn-app btn-block">
                                        <i class="fas fa-tasks"></i> My Tasks
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="<?= base_url('my-leaves') ?>" class="btn btn-app btn-block">
                                        <i class="fas fa-calendar-check"></i> My Leaves
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="<?= base_url('my-payslips') ?>" class="btn btn-app btn-block">
                                        <i class="fas fa-file-invoice-dollar"></i> Payslips
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="<?= base_url('my-details') ?>" class="btn btn-app btn-block">
                                        <i class="fas fa-id-card"></i> My Details
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="<?= base_url('employee-client-assets') ?>" class="btn btn-app btn-block">
                                        <i class="fas fa-folder-open"></i> Assets
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="<?= base_url('holidays-list') ?>" class="btn btn-app btn-block">
                                        <i class="fas fa-calendar-day"></i> Holidays
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>