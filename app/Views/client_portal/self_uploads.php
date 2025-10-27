<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>My Uploaded Files</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('client-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Uploads</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-upload"></i> Files You Have Uploaded</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary"><?= count($files) ?> Files</span>
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
                                            if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp'])) {
                                                $iconClass = 'fa-file-image';
                                                $iconColor = 'text-warning';
                                            } elseif (in_array(strtolower($fileExtension), ['pdf'])) {
                                                $iconClass = 'fa-file-pdf';
                                                $iconColor = 'text-danger';
                                            } elseif (in_array(strtolower($fileExtension), ['doc', 'docx'])) {
                                                $iconClass = 'fa-file-word';
                                                $iconColor = 'text-primary';
                                            } elseif (in_array(strtolower($fileExtension), ['xls', 'xlsx'])) {
                                                $iconClass = 'fa-file-excel';
                                                $iconColor = 'text-success';
                                            } elseif (in_array(strtolower($fileExtension), ['csv'])) {
                                                $iconClass = 'fa-file-csv';
                                                $iconColor = 'text-info';
                                            }
                                            ?>
                                            <div class="mb-3"><i class="fas <?= $iconClass ?> fa-5x <?= $iconColor ?>"></i></div>
                                            <h5 class="card-title text-truncate" title="<?= esc($file['original_name']) ?>">
                                                <?= esc($file['original_name']) ?>
                                            </h5>
                                            <p class="card-text">
                                                <small class="text-muted"><i class="fas fa-calendar"></i>
                                                    <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                                                </small>
                                                <br>
                                                <small class="text-muted"><i class="fas fa-hdd"></i>
                                                    <?= number_format($file['file_size'] / 1024, 2) ?> KB
                                                </small>
                                            </p>
                                            <a href="<?= base_url('client-download/self/' . $file['id']) ?>"
                                                class="btn btn-primary btn-block">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <a href="<?= base_url('self-delete/' . $file['id']) ?>"
                                                class="btn btn-danger btn-block"
                                                onclick="return confirm('Are you sure you want to delete this file?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>No Uploads Yet</h5>
                            <p>You have not uploaded any files yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>