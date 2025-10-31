<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\my_payslips.php
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

            <!-- Employee Info Card -->
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Employee Name:</strong>
                            <p class="text-muted"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong>
                            <p class="text-muted"><?= esc($employee['email']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Department:</strong>
                            <p class="text-muted"><?= esc($employee['department_name'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Payslips:</strong>
                            <p class="text-muted">
                                <span class="badge badge-info badge-lg">
                                    <i class="fas fa-file-invoice"></i> <?= count($payslips) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payslips Table -->
            <div class="card shadow-lg">
                <div class="card-header border-0">
                    <h3 class="card-title">My Salary Payslips</h3>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($payslips)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Month</th>
                                        <th>File Name</th>
                                        <th>Remarks</th>
                                       
                                        <th>Uploaded On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($payslips as $payslip): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td>
                                                <strong><?= date('F Y', strtotime($payslip['month'] . '-01')) ?></strong>
                                            </td>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger"></i>
                                                <?= esc($payslip['payslip_file']) ?>
                                            </td>
                                            <td><?= esc($payslip['remarks'] ?? '-') ?></td>
                                         
                                            <td><?= date('M d, Y', strtotime($payslip['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('my-payslips/download/' . $payslip['id']) ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Download Payslip">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-5 text-center">
                            <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Payslips Available</h5>
                            <p class="text-muted">Your salary payslips will appear here once uploaded by admin</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
