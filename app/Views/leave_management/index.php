<?php
// C:\xampp\htdocs\bhaviclients\app\Views\leave_management\index.php
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-check"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <!-- Pending Leaves Alert -->
            <?php if ($pendingCount > 0): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Pending Leave Requests!</h5>
                    You have <strong><?= $pendingCount ?></strong> pending leave request(s) waiting for approval.
                </div>
            <?php endif; ?>

            <div class="card shadow-lg">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-users"></i> Employees Leave Statistics</h3>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($employees)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Employee</th>
                                        <th>Employee Code</th>
                                        <th>Total Leaves</th>
                                        <th>Pending</th>
                                        <th>Approved</th>
                                        <th>Rejected</th>
                                        <th>Approved Days</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($employees as $emp): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td>
                                                <strong><?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?></strong>
                                            </td>
                                            <td><?= esc($emp['employee_code']) ?></td>
                                            <td><span class="badge badge-info badge-lg"><?= $emp['total_leaves'] ?></span></td>
                                            <td>
                                                <?php if ($emp['pending_leaves'] > 0): ?>
                                                    <span class="badge badge-warning badge-lg"><?= $emp['pending_leaves'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge badge-success"><?= $emp['approved_leaves'] ?></span></td>
                                            <td><span class="badge badge-danger"><?= $emp['rejected_leaves'] ?></span></td>
                                            <td><strong><?= $emp['total_approved_days'] ?></strong> days</td>
                                            <td>
                                                <a href="<?= base_url('leave-management/employee/' . $emp['employee_id']) ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View Leaves">
                                                    <i class="fas fa-eye"></i> View Leaves
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-calendar-times fa-4x mb-3"></i>
                            <h5>No Leave Requests Yet</h5>
                            <p>Employee leave requests will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
