<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client\files.php 
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Three Months Excell Sheets</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client') ?>">Clients</a></li>
                        <li class="breadcrumb-item active">Files</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('message') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Client Info Card -->
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-building mr-2"></i>
                        <strong><?= esc($client['name']) ?></strong>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('client/view/' . $client['id']) ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-eye"></i> View Client
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upload Files Card -->
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white">
                        <i class="fas fa-cloud-upload-alt"></i> Upload New Files
                    </h3>
                </div>
                <div class="card-body">
                    <?= form_open_multipart('client/upload/' . $client['id']) ?>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="client_files">
                                        <i class="fas fa-file"></i> Select Files (Multiple files allowed)
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               name="client_files[]" 
                                               class="custom-file-input" 
                                               id="client_files"
                                               multiple 
                                               required
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.xlsx,.xls">
                                        <label class="custom-file-label" for="client_files">Choose files...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Allowed: PDF, DOC, DOCX, XLS, XLSX, Images (JPG, PNG), ZIP
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-block mb-3">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Uploaded Documents Table -->
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title text-white">
                        <i class="fas fa-folder-open"></i> Uploaded Documents
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-dark"  ><?= count($clientFiles) ?> Files</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($clientFiles)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">S.No</th>
                                        <th width="40%">Document Name</th>
                                        <th width="15%">File Type</th>
                                        <th width="12%">Size</th>
                                        <th width="15%">Uploaded On</th>
                                        <th width="13%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($clientFiles as $file): ?>
                                        <?php
                                        $extension = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
                                        $iconClass = 'fa-file';
                                        $badgeClass = 'secondary';
                                        
                                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            $iconClass = 'fa-file-image';
                                            $badgeClass = 'success';
                                        } elseif ($extension == 'pdf') {
                                            $iconClass = 'fa-file-pdf';
                                            $badgeClass = 'danger';
                                        } elseif (in_array($extension, ['doc', 'docx'])) {
                                            $iconClass = 'fa-file-word';
                                            $badgeClass = 'primary';
                                        } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                            $iconClass = 'fa-file-excel';
                                            $badgeClass = 'success';
                                        } elseif ($extension == 'zip') {
                                            $iconClass = 'fa-file-archive';
                                            $badgeClass = 'warning';
                                        }

                                        // Format file size
                                        $fileSize = $file['file_size'];
                                        if ($fileSize < 1024) {
                                            $sizeFormatted = $fileSize . ' B';
                                        } elseif ($fileSize < 1048576) {
                                            $sizeFormatted = round($fileSize / 1024, 2) . ' KB';
                                        } else {
                                            $sizeFormatted = round($fileSize / 1048576, 2) . ' MB';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td>
                                                <i class="fas <?= $iconClass ?> text-<?= $badgeClass ?> mr-2"></i>
                                                <strong><?= esc($file['original_name']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $badgeClass ?>">
                                                    <?= strtoupper($extension) ?>
                                                </span>
                                            </td>
                                            <td><?= $sizeFormatted ?></td>
                                            <td>
                                                <small><?= date('M d, Y h:i A', strtotime($file['uploaded_at'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('client/download/' . $file['id']) ?>" 
                                                       class="btn btn-info" 
                                                       title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <?= form_open('client/deleteFile/' . $file['id'], ['class' => 'd-inline', 'onsubmit' => "return confirm('Are you sure you want to delete this file?');"]) ?>
                                                        <button type="submit" class="btn btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?= form_close() ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <h5>No files uploaded yet</h5>
                            <p>Upload your first file using the form above</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('client') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Clients
            </a>

        </div>
    </section>
</div>

<script>
// Update file input label with selected file names
document.getElementById('client_files').addEventListener('change', function(e) {
    var files = e.target.files;
    var label = document.querySelector('.custom-file-label');
    
    if (files.length > 0) {
        if (files.length === 1) {
            label.textContent = files[0].name;
        } else {
            label.textContent = files.length + ' files selected';
        }
    } else {
        label.textContent = 'Choose files...';
    }
});
</script>

<?= $this->endSection() ?>
