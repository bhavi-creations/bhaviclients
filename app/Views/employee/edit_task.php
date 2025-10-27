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
                        <li class="breadcrumb-item"><a href="<?= base_url('my-tasks') ?>">My Tasks</a></li>
                        <li class="breadcrumb-item active">Edit Task</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
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
                    <h3 class="card-title">Edit Your Work Submission</h3>
                </div>

                <?= form_open_multipart(base_url('update-task/' . $task['id'])) ?>
                <div class="card-body">

                    <div class="form-group">
                        <label for="title">Work Title <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control <?= (session('validation') && session('validation')->hasError('title')) ? 'is-invalid' : '' ?>"
                            id="title"
                            name="title"
                            value="<?= old('title', $task['title']) ?>">
                        <?php if (session('validation') && session('validation')->hasError('title')): ?>
                            <div class="invalid-feedback">
                                <?= session('validation')->getError('title') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="client_id">Client (Optional)</label>
                        <select class="form-control" id="client_id" name="client_id">
                            <option value="">-- Select Client (if applicable) --</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client['id'] ?>"
                                    <?= old('client_id', $task['client_id']) == $client['id'] ? 'selected' : '' ?>>
                                    <?= esc($client['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Work Description <span class="text-danger">*</span></label>
                        <textarea class="form-control <?= (session('validation') && session('validation')->hasError('description')) ? 'is-invalid' : '' ?>"
                            id="description"
                            name="description"
                            rows="6"><?= old('description', $task['description']) ?></textarea>
                        <?php if (session('validation') && session('validation')->hasError('description')): ?>
                            <div class="invalid-feedback">
                                <?= session('validation')->getError('description') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control <?= (session('validation') && session('validation')->hasError('status')) ? 'is-invalid' : '' ?>"
                            id="status"
                            name="status">
                            <option value="Pending" <?= old('status', $task['status']) == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="In Progress" <?= old('status', $task['status']) == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= old('status', $task['status']) == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Review" <?= old('status', $task['status']) == 'Review' ? 'selected' : '' ?>>Review</option>
                        </select>
                        <?php if (session('validation') && session('validation')->hasError('status')): ?>
                            <div class="invalid-feedback">
                                <?= session('validation')->getError('status') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($task['files_upload'])): ?>
                        <?php $existingFiles = json_decode($task['files_upload'], true); ?>
                        <?php if (!empty($existingFiles)): ?>
                            <div class="form-group">
                                <label>Existing Files:</label>
                                <div class="row">
                                    <?php foreach ($existingFiles as $index => $file): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <?php
                                                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                                    $isImage = in_array(strtolower($fileExtension), $imageExtensions);
                                                    ?>

                                                    <?php if ($isImage): ?>
                                                        <!-- Image Preview -->
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>" target="_blank">
                                                            <img src="<?= base_url('uploads/task_files/' . $file) ?>"
                                                                class="img-fluid rounded mb-2"
                                                                style="max-height: 150px; width: 100%; object-fit: cover;"
                                                                alt="<?= esc($file) ?>">
                                                        </a>
                                                    <?php else: ?>
                                                        <!-- File Icon for Non-Images -->
                                                        <div class="text-center mb-2">
                                                            <i class="fas fa-file fa-3x text-secondary"></i>
                                                        </div>
                                                    <?php endif; ?>

                                                    <p class="mb-1 text-truncate" title="<?= esc($file) ?>">
                                                        <small><?= esc($file) ?></small>
                                                    </p>

                                                    <div class="btn-group btn-group-sm w-100">
                                                        <a href="<?= base_url('uploads/task_files/' . $file) ?>"
                                                            class="btn btn-info"
                                                            target="_blank"
                                                            title="View/Download">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('delete-file/' . $task['id'] . '/' . $index) ?>"
                                                            class="btn btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this file? This action cannot be undone.')"
                                                            title="Delete File">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="files">Add More Files (Optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="files" name="files[]" multiple>
                            <label class="custom-file-label" for="files">Choose files...</label>
                        </div>
                        <small class="form-text text-muted">Upload additional files</small>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Task
                    </button>
                    <a href="<?= base_url('my-tasks') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
                <?= form_close() ?>
            </div>

        </div>
    </section>
</div>

<script>
// Update file input label with selected file names
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = '';
    if (this.files.length > 1) {
        fileName = this.files.length + ' files selected';
    } else if (this.files.length === 1) {
        fileName = this.files[0].name;
    }
    var label = document.querySelector('.custom-file-label');
    label.textContent = fileName || 'Choose files...';
});
</script>

<?= $this->endSection() ?>
