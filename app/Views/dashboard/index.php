<?php
// C:\xampp\htdocs\bhaviclients\app\Views\dashboard\index.php
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



            <!-- NEW: Notifications Widget Row (Top) -->
            <div class="row">
                <div class="col-md-12">
                    <?= view('components/notifications_widget') ?>
                </div>
            </div>

            <!-- Stats Widgets -->
            <div class="row">
                <!-- Total Clients -->
                <div class="col-lg-4 col-6">
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
                <div class="col-lg-4 col-6">
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

                <!-- Pending Payments -->
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= esc($pendingPaymentsCount ?? 0) ?></h3>
                            <p>Pending Payments</p>
                            <small>Total: ₹<?= number_format($pendingPaymentsAmount ?? 0, 2) ?></small>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        <a href="<?= base_url('client-payment/list') ?>" class="small-box-footer">
                            View Payments <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pending Leave Requests -->
                <!-- <div class="col-lg-4 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <?php
                            $leaveModel = new \App\Models\LeaveRequestModel();
                            $pendingLeaves = $leaveModel->getPendingCount();
                            ?>
                            <h3><?= $pendingLeaves ?></h3>
                            <p>Pending Leave Requests</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                        <a href="<?= base_url('leave-management') ?>" class="small-box-footer">
                            View Leave Requests <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div> -->
            </div>


            <!-- Recent Clients & Employees -->
            <div class="row">
                <!-- Recent Clients -->
                <div class="col-md-6">
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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentClients)): ?>
                                            <?php foreach ($recentClients as $client): ?>
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

                <!-- Recent Employees -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title"><i class="fas fa-users mr-2"></i> Recent Employees</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('employee') ?>" class="btn btn-tool text-white">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Department</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recentEmployees)): ?>
                                            <?php foreach ($recentEmployees as $employee): ?>
                                                <tr>
                                                    <td><strong><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></strong></td>
                                                    <td><?= esc($employee['email']) ?></td>
                                                    <td><?= esc($employee['department_name'] ?? 'N/A') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-3 text-muted">No employees found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Payments -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i> Upcoming Payments</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('client-payment/list') ?>" class="btn btn-tool">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Expected Amount</th>
                                            <th>Expected Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($upcomingPayments)): ?>
                                            <?php foreach ($upcomingPayments as $payment): ?>
                                                <tr>
                                                    <td><strong><?= esc($payment['client_name']) ?></strong></td>
                                                    <td><strong>₹<?= number_format($payment['expected_amount'], 2) ?></strong></td>
                                                    <td><?= date('M d, Y', strtotime($payment['expected_date'])) ?></td>
                                                    <td>
                                                        <?php if ($payment['status'] == 'overdue'): ?>
                                                            <span class="badge badge-danger">Overdue</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-3 text-muted">No pending payments</td>
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