<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\work\mytasks.php
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
                    
                    <!-- Message Alerts -->
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('message') ?>
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

                    <!-- Card for Task List -->
                    <div class="card shadow-lg">
                        <div class="card-header border-0">
                            <h3 class="card-title">List of Submitted Work Items</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('work/create') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus-circle"></i> Submit New Work
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">S.No.</th>
                                            <th>Title</th>
                                            <th>Submitted On</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th style="width: 150px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($tasks)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">No work items found. Click 'Submit New Work' to get started.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php $sn = 1; foreach ($tasks as $task): ?>
                                                <tr>
                                                    <td><?= $sn++ ?></td>
                                                    <td><?= esc($task['title']) ?></td>
                                                    <td><?= date('Y-m-d H:i', strtotime($task['submitted_at'])) ?></td>
                                                    <td>
                                                        <?php 
                                                            $statusClass = '';
                                                            switch ($task['status']) {
                                                                case 'Completed':
                                                                    $statusClass = 'badge-success';
                                                                    break;
                                                                case 'In Progress':
                                                                    $statusClass = 'badge-warning';
                                                                    break;
                                                                case 'Review':
                                                                    $statusClass = 'badge-info';
                                                                    break;
                                                                case 'Pending':
                                                                default:
                                                                    $statusClass = 'badge-secondary';
                                                                    break;
                                                            }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= esc($task['status']) ?></span>
                                                    </td>
                                                    <td><?= $task['due_date'] ? date('Y-m-d', strtotime($task['due_date'])) : 'N/A' ?></td>
                                                    <td>
                                                        <a href="<?= base_url('work/edit/' . $task['id']) ?>" class="btn btn-sm btn-info" title="Edit Work Item">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <!-- Delete button with confirmation -->
                                                        <a href="#" onclick="confirmDelete(<?= $task['id'] ?>)" class="btn btn-sm btn-danger" title="Delete Work Item">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this work item? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a id="deleteButton" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        // Set the action URL for the modal's delete button using a POST form submission
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('work/delete/') ?>' + id;

        // Ensure CSRF protection is handled if required by your setup
        // if (typeof csrf_token !== 'undefined' && typeof csrf_hash !== 'undefined') {
        //     var csrfInput = document.createElement('input');
        //     csrfInput.type = 'hidden';
        //     csrfInput.name = csrf_token;
        //     csrfInput.value = csrf_hash;
        //     form.appendChild(csrfInput);
        // }
        
        document.body.appendChild(form);

        // Update the modal's button click to submit the form
        document.getElementById('deleteButton').onclick = function() {
            $('#deleteModal').modal('hide'); // Hide modal first
            form.submit(); // Submit the POST request
        };

        // Show the modal
        $('#deleteModal').modal('show');
    }
</script>

<?= $this->endSection() ?>
