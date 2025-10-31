<?php
// C:\xampp\htdocs\bhaviclients\app\Views\dashboard\admin_manager.php
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title ?? 'Dashboard') ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Stats Widgets Row 1 -->
            <div class="row">
                <!-- Total Clients -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= esc($totalClients ?? 0) ?></h3>
                            <p>Total Clients</p>
                        </div>
                        <div class="icon"><i class="fas fa-handshake"></i></div>
                        <a href="<?= base_url('client') ?>" class="small-box-footer">
                            View All Clients <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Employees -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= esc($totalEmployees ?? 0) ?></h3>
                            <p>Total Employees</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= base_url('employee') ?>" class="small-box-footer">
                            View All Employees <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Tasks -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= esc($totalTasks ?? 0) ?></h3>
                            <p>Total Tasks</p>
                        </div>
                        <div class="icon"><i class="fas fa-tasks"></i></div>
                        <a href="<?= base_url('task-management') ?>" class="small-box-footer">
                            View Tasks <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Payslips -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= esc($totalPayslips ?? 0) ?></h3>
                            <p>Total Payslips</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <a href="<?= base_url('employee-payslip') ?>" class="small-box-footer">
                            View Payslips <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Widgets Row 2 (Task Status) -->
            <div class="row">
                <!-- Pending Tasks -->
                <div class="col-lg-6 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= esc($pendingTasks ?? 0) ?></h3>
                            <p>Pending Tasks</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <a href="<?= base_url('task-management?status=Pending') ?>" class="small-box-footer">
                            View Pending <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Completed Tasks -->
                <div class="col-lg-6 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= esc($completedTasks ?? 0) ?></h3>
                            <p>Completed Tasks</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="<?= base_url('task-management?status=Completed') ?>" class="small-box-footer">
                            View Completed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks & Payslips -->
            <div class="row">
                <!-- Recent Tasks -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-tasks mr-2"></i> Recent Tasks</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('task-management') ?>" class="btn btn-tool text-white">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Employee</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentTasks)): ?>
                                            <?php foreach($recentTasks as $task): ?>
                                                <tr>
                                                    <td><strong><?= esc($task['title']) ?></strong></td>
                                                    <td><?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></td>
                                                    <td>
                                                        <?php
                                                        $statusBadge = [
                                                            'Pending' => 'warning',
                                                            'In Progress' => 'info',
                                                            'Completed' => 'success',
                                                            'Review' => 'primary'
                                                        ];
                                                        $badge = $statusBadge[$task['status']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge badge-<?= $badge ?>"><?= esc($task['status']) ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-3 text-muted">No tasks found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Payslips -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-2"></i> Recent Payslips</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('employee-payslip') ?>" class="btn btn-tool">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Month</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentPayslips)): ?>
                                            <?php foreach($recentPayslips as $payslip): ?>
                                                <tr>
                                                    <td><strong><?= esc($payslip['first_name'] . ' ' . $payslip['last_name']) ?></strong></td>
                                                    <td><?= date('F Y', strtotime($payslip['month'] . '-01')) ?></td>
                                                    <td><?= date('M d', strtotime($payslip['created_at'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-3 text-muted">No payslips uploaded</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title"><i class="fas fa-handshake mr-2"></i> Recent Clients</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('client') ?>" class="btn btn-tool text-white">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Company Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentClients)): ?>
                                            <?php foreach($recentClients as $client): ?>
                                                <tr>
                                                    <td><strong><?= esc($client['name']) ?></strong></td>
                                                    <td><?= esc($client['email']) ?></td>
                                                    <td><?= esc($client['phone']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-3 text-muted">No clients found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
