<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\client_assets\view.php
?>
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
                        <li class="breadcrumb-item"><a href="<?= base_url('employee-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('employee-client-assets') ?>">Client Assets</a></li>
                        <li class="breadcrumb-item active">View</li>
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
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Client Information -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-tie"></i> Client Information</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-5">Client Name:</dt>
                                <dd class="col-sm-7"><strong><?= esc($asset['client_name']) ?></strong></dd>

                                <dt class="col-sm-5">Email:</dt>
                                <dd class="col-sm-7"><?= esc($asset['client_email']) ?></dd>

                                <dt class="col-sm-5">Uploaded By:</dt>
                                <dd class="col-sm-7"><?= esc($asset['uploaded_by_name'] . ' ' . $asset['uploaded_by_lastname']) ?></dd>

                                <dt class="col-sm-5">Uploaded On:</dt>
                                <dd class="col-sm-7"><?= date('M d, Y', strtotime($asset['created_at'])) ?></dd>

                                <?php if (!empty($asset['updated_at']) && $asset['updated_at'] != $asset['created_at']): ?>
                                    <dt class="col-sm-5">Last Updated:</dt>
                                    <dd class="col-sm-7"><?= date('M d, Y', strtotime($asset['updated_at'])) ?></dd>
                                <?php endif; ?>

                                <?php if (!empty($asset['remarks'])): ?>
                                    <dt class="col-sm-12 mt-2">Remarks:</dt>
                                    <dd class="col-sm-12">
                                        <div class="alert alert-info mb-0">
                                            <?= nl2br(esc($asset['remarks'])) ?>
                                        </div>
                                    </dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('employee-client-assets') ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-image"></i> Client Logo</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if (!empty($asset['logo_file'])): ?>
                                <img src="<?= base_url('uploads/client_assets/logos/' . $asset['logo_file']) ?>" 
                                     alt="Client Logo" 
                                     class="img-fluid mb-3" 
                                     style="max-height: 250px; border: 2px solid #ddd; padding: 15px; background: white; border-radius: 8px;">
                                <br>
                                <a href="<?= base_url('employee-client-assets/download/logo/' . $asset['logo_file']) ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download Logo
                                </a>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No logo available
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Files -->
            <?php if (!empty($asset['template_files_array'])): ?>
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-alt"></i> Template Files (<?= count($asset['template_files_array']) ?>)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($asset['template_files_array'] as $index => $file): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center p-3">
                                            <?php
                                            $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $iconClass = 'fa-file';
                                            $iconColor = 'text-secondary';
                                            
                                            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                                                $iconClass = 'fa-file-image';
                                                $iconColor = 'text-info';
                                            } elseif ($fileExt == 'pdf') {
                                                $iconClass = 'fa-file-pdf';
                                                $iconColor = 'text-danger';
                                            } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                                $iconClass = 'fa-file-word';
                                                $iconColor = 'text-primary';
                                            } elseif (in_array($fileExt, ['xls', 'xlsx'])) {
                                                $iconClass = 'fa-file-excel';
                                                $iconColor = 'text-success';
                                            } elseif (in_array($fileExt, ['ppt', 'pptx'])) {
                                                $iconClass = 'fa-file-powerpoint';
                                                $iconColor = 'text-warning';
                                            } elseif (in_array($fileExt, ['zip', 'rar', '7z'])) {
                                                $iconClass = 'fa-file-archive';
                                                $iconColor = 'text-dark';
                                            }
                                            ?>
                                            <i class="fas <?= $iconClass ?> fa-4x <?= $iconColor ?> mb-3"></i>
                                            <p class="mb-2 text-truncate" style="font-size: 12px;" title="<?= esc($file) ?>">
                                                <strong><?= esc(strlen($file) > 25 ? substr(basename($file), 0, 25) . '...' : basename($file)) ?></strong>
                                            </p>
                                            <span class="badge badge-secondary mb-2">.<?= strtoupper($fileExt) ?></span>
                                            <br>
                                            <a href="<?= base_url('employee-client-assets/download/template/' . $file) ?>" 
                                               class="btn btn-sm btn-primary btn-block mt-2">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card card-secondary">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Template Files</h5>
                        <p class="text-muted">No templates have been uploaded for this client</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Social Media Links -->
            <?php if (!empty($asset['social_media_array'])): ?>
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-share-alt"></i> Social Media Links (<?= count($asset['social_media_array']) ?>)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($asset['social_media_array'] as $social): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <?php
                                                $platform = strtolower($social['platform']);
                                                $iconClass = 'fa-link';
                                                $iconColor = 'text-secondary';
                                                $brandClass = '';
                                                
                                                if (strpos($platform, 'instagram') !== false) {
                                                    $iconClass = 'fa-instagram';
                                                    $iconColor = 'text-danger';
                                                    $brandClass = 'fab';
                                                } elseif (strpos($platform, 'facebook') !== false) {
                                                    $iconClass = 'fa-facebook';
                                                    $iconColor = 'text-primary';
                                                    $brandClass = 'fab';
                                                } elseif (strpos($platform, 'twitter') !== false || strpos($platform, 'x') !== false) {
                                                    $iconClass = 'fa-twitter';
                                                    $iconColor = 'text-info';
                                                    $brandClass = 'fab';
                                                } elseif (strpos($platform, 'linkedin') !== false) {
                                                    $iconClass = 'fa-linkedin';
                                                    $iconColor = 'text-primary';
                                                    $brandClass = 'fab';
                                                } elseif (strpos($platform, 'youtube') !== false) {
                                                    $iconClass = 'fa-youtube';
                                                    $iconColor = 'text-danger';
                                                    $brandClass = 'fab';
                                                } elseif (strpos($platform, 'tiktok') !== false) {
                                                    $iconClass = 'fa-tiktok';
                                                    $iconColor = 'text-dark';
                                                    $brandClass = 'fab';
                                                } elseif (strpos($platform, 'pinterest') !== false) {
                                                    $iconClass = 'fa-pinterest';
                                                    $iconColor = 'text-danger';
                                                    $brandClass = 'fab';
                                                } elseif (strpos($platform, 'whatsapp') !== false) {
                                                    $iconClass = 'fa-whatsapp';
                                                    $iconColor = 'text-success';
                                                    $brandClass = 'fab';
                                                } else {
                                                    $brandClass = 'fas';
                                                }
                                                ?>
                                                <i class="<?= $brandClass ?> <?= $iconClass ?> <?= $iconColor ?>"></i>
                                                <?= esc($social['platform']) ?>
                                            </h5>
                                            <p class="card-text">
                                                <small class="text-muted text-truncate d-block" title="<?= esc($social['link']) ?>">
                                                    <?= esc($social['link']) ?>
                                                </small>
                                            </p>
                                            <a href="<?= esc($social['link']) ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="btn btn-sm btn-primary btn-block">
                                                <i class="fas fa-external-link-alt"></i> Visit Page
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card card-secondary">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-share-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Social Media Links</h5>
                        <p class="text-muted">No social media links have been added for this client</p>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
