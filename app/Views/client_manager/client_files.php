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
                        <li class="breadcrumb-item"><a href="<?= base_url('manager-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('manager/clients') ?>">Clients</a></li>
                        <li class="breadcrumb-item active">Files</li>
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

            <!-- Client Info Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Client Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Company:</strong>
                            <p class="text-muted"><?= esc($client['name']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Contact Person:</strong>
                            <p class="text-muted"><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong>
                            <p class="text-muted"><?= esc($client['email']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Phone:</strong>
                            <p class="text-muted"><?= esc($client['phone']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Files Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt"></i> Files for <?= esc($client['name']) ?>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success"><?= count($files) ?> Files</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($files)): ?>
                        <div class="row">
                            <?php foreach ($files as $file): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <?php
                                            $fileExtension = pathinfo($file['original_name'], PATHINFO_EXTENSION);
                                            $iconClass = 'fa-file';
                                            $iconColor = 'text-secondary';
                                            
                                            if (in_array(strtolower($fileExtension), ['xls', 'xlsx'])) {
                                                $iconClass = 'fa-file-excel';
                                                $iconColor = 'text-success';
                                            } elseif (in_array(strtolower($fileExtension), ['pdf'])) {
                                                $iconClass = 'fa-file-pdf';
                                                $iconColor = 'text-danger';
                                            } elseif (in_array(strtolower($fileExtension), ['doc', 'docx'])) {
                                                $iconClass = 'fa-file-word';
                                                $iconColor = 'text-primary';
                                            } elseif (in_array(strtolower($fileExtension), ['csv'])) {
                                                $iconClass = 'fa-file-csv';
                                                $iconColor = 'text-info';
                                            }
                                            ?>
                                            
                                            <div class="mb-3">
                                                <i class="fas <?= $iconClass ?> fa-4x <?= $iconColor ?>"></i>
                                            </div>
                                            
                                            <h6 class="card-title text-truncate" title="<?= esc($file['original_name']) ?>">
                                                <?= esc($file['original_name']) ?>
                                            </h6>
                                            
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> 
                                                    <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-hdd"></i> 
                                                    <?= number_format($file['file_size'] / 1024, 2) ?> KB
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> 
                                                    By: <?= esc($file['uploaded_by'] ?? 'Admin') ?>
                                                </small>
                                            </p>
                                            
                                            <div class="btn-group btn-group-sm w-100">
                                                <a href="<?= base_url('manager/download-file/' . $file['id']) ?>" 
                                                   class="btn btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger" 
                                                        onclick="confirmDelete(<?= $file['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Hidden Delete Form -->
                                            <form id="deleteForm<?= $file['id'] ?>" 
                                                  action="<?= base_url('manager/delete-file/' . $file['id']) ?>" 
                                                  method="post" 
                                                  style="display:none;">
                                                <?= csrf_field() ?>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>No Files Uploaded</h5>
                            <p>No files have been uploaded for this client yet.</p>
                            <a href="<?= base_url('manager/upload-files') ?>" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Files
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('manager/clients') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Clients
                    </a>
                    <a href="<?= base_url('manager/upload-files') ?>" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload More Files
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
function confirmDelete(fileId) {
    if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
        document.getElementById('deleteForm' + fileId).submit();
    }
}
</script>

<?= $this->endSection() ?>
