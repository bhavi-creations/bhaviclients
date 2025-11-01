<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\index.php 
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
            <div class="row">
                <div class="col-12">

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-lg">
                        <div class="card-header border-0">
                            <h3 class="card-title">All Employees</h3>
                            <div class="card-tools">
                                <?php if (session()->get('role_id') == 1): ?>
                                    <!-- ONLY SHOW ADD BUTTON FOR SUPER ADMIN -->
                                    <a href="<?= base_url('employee/create') ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus-circle"></i> Add New Employee
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Emp Code</th>
                                            <th>Employee Name</th>
                                            <th>Phone</th>
                                            <th>Department</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($employees)): ?>
                                            <?php $sn = 1; ?>
                                            <?php foreach ($employees as $employee): ?>
                                                <tr>
                                                    <td><?= $sn++ ?></td>
                                                    <td>
                                                        <?php if (!empty($employee['employee_code'])): ?>
                                                            <span class="badge badge-info"><?= esc($employee['employee_code']) ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                                                    <td><?= esc($employee['phone']) ?></td>
                                                    <td><?= esc($employee['department_name'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <!-- VIEW BUTTON - VISIBLE TO ALL (Admin & Admin Manager) -->
                                                        <a href="<?= base_url('employee/view/' . $employee['id']) ?>" 
                                                           class="btn btn-sm btn-primary" 
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <?php if (session()->get('role_id') == 1): ?>
                                                            <!-- EDIT & DELETE - ONLY FOR SUPER ADMIN -->
                                                            <a href="<?= base_url('employee/edit/' . $employee['id']) ?>" 
                                                               class="btn btn-sm btn-info" 
                                                               title="Edit Employee">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="#" 
                                                               onclick="confirmDelete(<?= $employee['id'] ?>)" 
                                                               class="btn btn-sm btn-danger" 
                                                               title="Delete Employee">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center py-4">No employees found.</td></tr>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this employee? This action cannot be undone and will remove all related data including salary history.
                </div>
                <div class="modal-footer">
                     <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '<?= base_url("employee/delete/") ?>' + id;
        $('#deleteModal').modal('show');
    }
</script>

<?= $this->endSection() ?>
