<?php 
$session = \Config\Services::session();
?>
<!-- Load Layout Template -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Flash Messages -->
                    <?php if ($session->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="icon fas fa-check"></i> <?= $session->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if ($session->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="icon fas fa-ban"></i> <?= $session->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Role List</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('roles/create') ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Role
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- rolesTable is used as the target for DataTables initialization -->
                            <table id="rolesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">ID</th>
                                        <th style="width: 25%">Name</th>
                                        <th style="width: 40%">Description</th>
                                        <th style="width: 15%">Created At</th>
                                        <th style="width: 10%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><?= esc($role['id']) ?></td>
                                            <td><?= esc($role['name']) ?></td>
                                            <td><?= esc($role['description'] ?? 'N/A') ?></td>
                                            <td><?= esc(date('Y-m-d H:i', strtotime($role['created_at']))) ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Delete button triggers modal -->
                                                <a href="#" class="btn btn-sm btn-danger delete-role" data-id="<?= $role['id'] ?>" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- Delete Confirmation Modal (Moved to the bottom of the content section) -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        You are about to delete this role. Are you sure you want to proceed? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
        <a id="confirmDelete" href="#" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</a>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Custom script to initialize DataTables and handle delete confirmation -->
<script>
    $(function () {
        // Initialize DataTables (Assuming main layout loads DataTables assets)
        $("#rolesTable").DataTable({
            "responsive": true, 
            "lengthChange": true, 
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "order": [[ 0, "desc" ]] // Order by ID descending
        }).buttons().container().appendTo('#rolesTable_wrapper .col-md-6:eq(0)');

        // Handle Delete Button Click to show modal
        $('.delete-role').on('click', function(e) {
            e.preventDefault();
            const roleId = $(this).data('id');
            const deleteUrl = '<?= base_url('roles/delete') ?>/' + roleId;
            
            // Set the URL for the confirmation button
            $('#confirmDelete').attr('href', deleteUrl);
            
            // Show the modal
            $('#deleteModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
