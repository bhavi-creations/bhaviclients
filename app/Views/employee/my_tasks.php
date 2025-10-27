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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Tasks</li>
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
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Work Submissions</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('submit-work') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Submit New Work
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Submitted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?> 
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td> 
                                            <td><?= esc($task['title']) ?></td>
                                            <td><?= esc($task['client_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'Pending' => 'warning',
                                                    'In Progress' => 'info',
                                                    'Completed' => 'success',
                                                    'Review' => 'primary'
                                                ];
                                                $class = $statusClass[$task['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $class ?>">
                                                    <?= esc($task['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y h:i A', strtotime($task['submitted_at'])) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal<?= $task['id'] ?>">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <a href="<?= base_url('edit-task/' . $task['id']) ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $task['id'] ?>)">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>

                                                <form id="deleteForm<?= $task['id'] ?>" action="<?= base_url('delete-task/' . $task['id']) ?>" method="post" style="display:none;">
                                                    <?= csrf_field() ?>
                                                </form>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="viewModal<?= $task['id'] ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Task Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>Title:</strong> <?= esc($task['title']) ?></p>
                                                                <p><strong>Client:</strong> <?= esc($task['client_name'] ?? 'N/A') ?></p>
                                                                <p><strong>Status:</strong>
                                                                    <span class="badge badge-<?= $class ?>">
                                                                        <?= esc($task['status']) ?>
                                                                    </span>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Submitted:</strong> <?= date('M d, Y h:i A', strtotime($task['submitted_at'])) ?></p>
                                                                <p><strong>Last Updated:</strong> <?= date('M d, Y h:i A', strtotime($task['updated_at'])) ?></p>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <p><strong>Description:</strong></p>
                                                        <p><?= nl2br(esc($task['description'])) ?></p>

                                                        <?php if (!empty($task['files_upload'])): ?>
                                                            <?php $files = json_decode($task['files_upload'], true); ?>
                                                            <?php if (!empty($files)): ?>
                                                                <hr>
                                                                <p><strong>Attached Files:</strong></p>
                                                                <div class="row">
                                                                    <?php foreach ($files as $index => $file): ?>
                                                                        <div class="col-md-4 mb-3">
                                                                            <div class="card">
                                                                                <div class="card-body p-2 text-center">
                                                                                    <?php
                                                                                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                                                    $isImage = in_array(strtolower($fileExtension), $imageExtensions);
                                                                                    ?>

                                                                                    <?php if ($isImage): ?>
                                                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" target="_blank">
                                                                                            <img src="<?= base_url('uploads/task_files/' . $file) ?>"
                                                                                                class="img-fluid rounded mb-2"
                                                                                                style="max-height: 120px;"
                                                                                                alt="<?= esc($file) ?>">
                                                                                        </a>
                                                                                    <?php else: ?>
                                                                                        <i class="fas fa-file fa-3x text-secondary mb-2"></i>
                                                                                    <?php endif; ?>

                                                                                    <p class="mb-1 text-truncate" style="font-size: 11px;">
                                                                                        <?= esc($file) ?>
                                                                                    </p>

                                                                                    <a href="<?= base_url('uploads/task_files/' . $file) ?>"
                                                                                        class="btn btn-sm btn-primary btn-block"
                                                                                        target="_blank">
                                                                                        <i class="fas fa-eye"></i> View
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No tasks submitted yet.
                            <a href="<?= base_url('submit-work') ?>">Submit your first work!</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
    function confirmDelete(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            document.getElementById('deleteForm' + taskId).submit();
        }
    }
</script>

<?= $this->endSection() ?>