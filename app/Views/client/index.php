<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client\index.php 
$userRoleId = session()->get('role_id');
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
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Unified Flash Messages (Uses app/Views/flash_messages.php) -->
                    <?= view('flash_messages') ?>

                    <!-- Card for Client List -->
                    <div class="card shadow-lg">
                        <div class="card-header border-0">
                            <h3 class="card-title">All Clients</h3>
                            <?php if ($userRoleId == 1): ?>
                                <div class="card-tools">
                                    <a href="<?= base_url('client/create') ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Add New Client
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Company Name</th>
                                            <th>Owner Name</th>
                                            <th>Manager Name</th>
                                            <th>Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($clients)): ?>
                                            <?php $sn = 1; ?>
                                            <?php foreach ($clients as $client): ?>
                                                <tr>
                                                    <td><?= $sn++ ?></td>
                                                    <td><?= esc($client['name']) ?></td>
                                                    <td>
                                                        <?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($client['manager_name'])): ?>
                                                            <?= esc($client['manager_name']) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Not assigned</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($client['phone']) ?></td>

                                                    <td>
                                                        <!-- Admin Upload Files (Excel) - For Admin & Admin Manager -->
                                                        <a href="<?= base_url('client/files/' . $client['id']) ?>"
                                                            class="btn btn-sm btn-primary mr-1"
                                                            title="Admin Upload Files (Excel)">
                                                            <i class="fas fa-file-excel"></i>
                                                        </a>

                                                        <!-- Client Self-Uploads (View) - For Admin & Admin Manager -->
                                                        <a href="<?= base_url('client-uploads/by-client/' . $client['id']) ?>"
                                                            class="btn btn-sm btn-info mr-1"
                                                            title="Client Uploaded Files">
                                                            <i class="fas fa-cloud-upload-alt"></i>
                                                        </a>

                                                        <!-- Below actions ONLY for Admin (role_id = 1) -->
                                                        <?php if ($userRoleId == 1): ?>
                                                            <!-- Project Details -->
                                                            <a href="<?= base_url('maintenance/client/' . $client['id']) ?>"
                                                                class="btn btn-sm btn-warning mr-1"
                                                                title="Project Details">
                                                                <i class="fas fa-project-diagram"></i>
                                                            </a>

                                                            <!-- View Client Details -->
                                                            <a href="<?= base_url('client/view/' . $client['id']) ?>"
                                                                class="btn btn-sm btn-secondary mr-1"
                                                                title="View Client Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>

                                                            <!-- Edit Client -->
                                                            <a href="<?= base_url('client/edit/' . $client['id']) ?>"
                                                                class="btn btn-sm btn-dark mr-1"
                                                                title="Edit Client">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <!-- Delete Client -->
                                                            <?= form_open('client/delete/' . $client['id'], ['onsubmit' => "return confirm('Are you sure you want to delete this client? This action cannot be undone.')", 'class' => 'd-inline']) ?>
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Client">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                            <?= form_close() ?>
                                                        <?php endif; ?>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">No clients found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- Custom Confirmation Modal for Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this client record? This action cannot be undone and will remove the linked user account.
            </div>
            <div class="modal-footer">
                <a id="deleteButton" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id) {
        // Set the href attribute of the modal's delete button
        document.getElementById('deleteButton').href = '<?= base_url('client/delete/') ?>' + id;
        // Show the modal
        $('#deleteModal').modal('show');
    }
</script>
<?= $this->endSection() ?>
