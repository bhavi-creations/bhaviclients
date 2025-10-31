<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee_payslip\index.php 
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
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
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

            <div class="card shadow-lg">
                <div class="card-header border-0">
                    <h3 class="card-title">Employees with Payslips</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('employee-payslip/create') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle"></i> Upload New Payslip
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Employee Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Total Payslips</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($employees)): ?>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($employees as $employee): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td><strong><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></strong></td>
                                            <td><?= esc($employee['email']) ?></td>
                                            <td><?= esc($employee['department_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge badge-info badge-lg">
                                                    <i class="fas fa-file-invoice"></i> <?= $employee['payslip_count'] ?> Payslip(s)
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('employee-payslip/employee/' . $employee['id']) ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View All Payslips">
                                                    <i class="fas fa-eye"></i> View Payslips
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No payslips uploaded yet</h5>
                                            <p class="text-muted">Upload your first payslip to get started</p>
                                            <a href="<?= base_url('employee-payslip/create') ?>" class="btn btn-success mt-2">
                                                <i class="fas fa-plus-circle"></i> Upload Payslip
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
