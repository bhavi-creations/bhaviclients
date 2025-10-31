<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee_payslip\employee_payslips.php 
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
                        <li class="breadcrumb-item"><a href="<?= base_url('employee-payslip') ?>">Payslips</a></li>
                        <li class="breadcrumb-item active"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></li>
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
                        <div class="col-md-6">
                            <strong>Employee Name:</strong>
                            <p class="text-muted"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong>
                            <p class="text-muted"><?= esc($employee['email']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payslips Table -->
            <div class="card shadow-lg">
                <div class="card-header border-0">
                    <h3 class="card-title">All Payslips</h3>
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
                                    <th>Month</th>
                                    <th>Remarks</th>
                                   
                                    <th>Uploaded On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($payslips)): ?>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($payslips as $payslip): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td>
                                                <strong><?= date('F Y', strtotime($payslip['month'] . '-01')) ?></strong>
                                            </td>
                                            <td><?= esc($payslip['remarks'] ?? '-') ?></td>
                                             
                                            <td><?= date('M d, Y', strtotime($payslip['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('employee-payslip/download/' . $payslip['id']) ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="<?= base_url('employee-payslip/edit/' . $payslip['id']) ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" 
                                                   onclick="confirmDelete(<?= $payslip['id'] ?>)" 
                                                   class="btn btn-sm btn-danger" 
                                                   title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-4">No payslips found for this employee.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('employee-payslip') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Employee List
            </a>

        </div>
    </section>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this payslip? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '<?= base_url("employee-payslip/delete/") ?>' + id;
        $('#deleteModal').modal('show');
    }
</script>

<?= $this->endSection() ?>
