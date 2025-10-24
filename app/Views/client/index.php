<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client\index.php 
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
                            <div class="card-tools">
                                <a href="<?= base_url('client/create') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Add New Client
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Company Name</th>
                                            <th>Owner Name</th>
                                            <th>Email</th> <!-- NEW: Added Email column header -->
                                            <th>Phone</th>
                                            <!-- REMOVED: Address column header -->
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($clients)): ?>
                                            <?php $sn = 1; // Initialize Serial Number counter 
                                            ?>
                                            <?php foreach ($clients as $client): ?>
                                                <tr>
                                                    <td><?= $sn++ ?></td> <!-- Print and increment S.No. -->
                                                    <td><?= esc($client['name']) ?></td>
                                                    <td>
                                                        <?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>
                                                    </td>
                                                    <td><?= esc($client['email']) ?></td> <!-- NEW: Added Email data cell -->
                                                    <td><?= esc($client['phone']) ?></td>
                                                    <!-- REMOVED: The problematic line that referenced $client['address'] has been removed entirely -->

                                                    <td>
                                                        <!-- NEW: Upload Files button -->
                                                        <a href="<?= base_url('client/files/' . $client['id']) ?>" class="btn btn-sm btn-primary mr-1" title="Upload Client Files">
                                                            <i class="fas fa-file-upload"></i>
                                                        </a>
                                                        <a href="<?= base_url('client/edit/' . $client['id']) ?>" class="btn btn-sm btn-info mr-1" title="Edit Client">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <!-- Delete button with confirmation -->
                                                        <?= form_open('client/delete/' . $client['id'], ['onsubmit' => "return confirm('Are you sure you want to delete this client? This action cannot be undone.')", 'class' => 'd-inline']) ?>
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <?= form_close() ?>
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