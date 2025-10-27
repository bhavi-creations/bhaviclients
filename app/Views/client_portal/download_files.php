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
                        <li class="breadcrumb-item"><a href="<?= base_url('client-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Download Files</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-excel"></i> Files Available for Download
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success"><?= count($files) ?> Files</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($files)): ?>
                        <div class="row">
                            <?php foreach ($files as $file): ?>
                                <div class="col-md-4 mb-3">
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
                                                <i class="fas <?= $iconClass ?> fa-5x <?= $iconColor ?>"></i>
                                            </div>
                                            
                                            <h5 class="card-title text-truncate" title="<?= esc($file['original_name']) ?>">
                                                <?= esc($file['original_name']) ?>
                                            </h5>
                                            
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
                                            </p>
                                            
                                            <a href="<?= base_url('download-file/' . $file['id']) ?>" 
                                               class="btn btn-success btn-block">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>No Files Available</h5>
                            <p>There are no files uploaded for you yet. Files will appear here when the admin uploads them.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
