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
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-lg">
                        <div class="card-header border-0">
                            <h3 class="card-title">All Employees</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('employee/create') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus-circle"></i> Add New Employee
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
                                                    <td><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                                                    <td><?= esc($employee['email']) ?></td>
                                                    <td><?= esc($employee['phone']) ?></td>
                                                    <td><?= esc($employee['department_name'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <a href="<?= base_url('employee/edit/' . $employee['id']) ?>" class="btn btn-sm btn-info" title="Edit Employee">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete button with JS triggered modal -->
                                                        <a href="#" onclick="confirmDelete(<?= $employee['id'] ?>)" class="btn btn-sm btn-danger" title="Delete Employee">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
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

<!-- Delete Confirmation Modal (Bootstrap 5 compatible) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                  
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this employee? This action cannot be undone.
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
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>

<?= $this->endSection() ?>
