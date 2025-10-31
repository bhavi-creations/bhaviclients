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

            <div class="card shadow-lg">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-excel"></i> Files Available for Download
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success badge-lg"><?= count($files) ?> Files</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($files)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">S.No.</th>
                                        <th style="width: 80px;">Type</th>
                                        <th>File Name</th>
                                        <th style="width: 120px;">Size</th>
                                        <th style="width: 150px;">Uploaded</th>
                                        <th style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($files as $file): ?>
                                        <?php
                                        $fileExtension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                        $iconClass = 'fa-file';
                                        $iconColor = 'text-secondary';
                                        $badgeClass = 'secondary';
                                        
                                        if (in_array($fileExtension, ['xls', 'xlsx'])) {
                                            $iconClass = 'fa-file-excel';
                                            $iconColor = 'text-success';
                                            $badgeClass = 'success';
                                        } elseif ($fileExtension == 'pdf') {
                                            $iconClass = 'fa-file-pdf';
                                            $iconColor = 'text-danger';
                                            $badgeClass = 'danger';
                                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                            $iconClass = 'fa-file-word';
                                            $iconColor = 'text-primary';
                                            $badgeClass = 'primary';
                                        } elseif ($fileExtension == 'csv') {
                                            $iconClass = 'fa-file-csv';
                                            $iconColor = 'text-info';
                                            $badgeClass = 'info';
                                        } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                                            $iconClass = 'fa-file-image';
                                            $iconColor = 'text-warning';
                                            $badgeClass = 'warning';
                                        } elseif (in_array($fileExtension, ['zip', 'rar', '7z'])) {
                                            $iconClass = 'fa-file-archive';
                                            $iconColor = 'text-dark';
                                            $badgeClass = 'dark';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td class="text-center">
                                                <i class="fas <?= $iconClass ?> fa-2x <?= $iconColor ?>"></i>
                                            </td>
                                            <td>
                                                <strong><?= esc($file['original_name']) ?></strong>
                                                <br>
                                                <span class="badge badge-<?= $badgeClass ?>">
                                                    <?= strtoupper($fileExtension) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-hdd text-muted"></i>
                                                <?php
                                                $sizeKB = $file['file_size'] / 1024;
                                                if ($sizeKB > 1024) {
                                                    echo number_format($sizeKB / 1024, 2) . ' MB';
                                                } else {
                                                    echo number_format($sizeKB, 2) . ' KB';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-calendar"></i>
                                                    <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                                                    <br>
                                                    <i class="far fa-clock"></i>
                                                    <?= date('h:i A', strtotime($file['uploaded_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('download-file/' . $file['id']) ?>" 
                                                   class="btn btn-success btn-sm btn-block"
                                                   title="Download File">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Files Available</h5>
                            <p class="text-muted">Files will appear here when the admin uploads them for you.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
