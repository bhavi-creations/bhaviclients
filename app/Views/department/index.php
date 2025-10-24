<?= $this->extend('layouts/main') ?> <?= $this->section('content') ?>
   
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('message') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Department List</h3>
            <div class="card-tools">
                <a href="<?= base_url('department/create') ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($departments)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No departments found in the database.</td>
                        </tr>
                    <?php else: ?>
                        <?php $serial = 1 + (($currentPage ?? 1) - 1) * ($perPage ?? count($departments)); ?>
                        <?php foreach ($departments as $department): ?>
                            <tr>
                                <td><?= $serial++ ?></td>
                                <td><?= esc($department['name']) ?></td>
                                <td><?= esc($department['description']) ?? 'N/A' ?></td>
                               
                                <!-- ACTIONS COLUMN -->
                                <td>
                                    <a href="<?= base_url('department/edit/' . $department['id']) ?>" class="btn btn-sm btn-info me-1" title="Edit/View">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                   
                                    <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-department-id="<?= $department['id'] ?>"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
   
    <!-- Modal for Delete Confirmation (Bootstrap 5) -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this department? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <?= form_open(base_url('department/delete'), ['id' => 'deleteForm', 'style' => 'display: inline;']) ?>
                        <input type="hidden" name="id" id="delete-department-id" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
   
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var departmentId = button.getAttribute('data-department-id');

                var modalBodyInput = document.getElementById('delete-department-id');
                modalBodyInput.value = departmentId;

                var deleteForm = document.getElementById('deleteForm');
                deleteForm.action = "<?= base_url('department/delete/') ?>" + departmentId;
            });
        });
    </script>

<?= $this->endSection() ?>