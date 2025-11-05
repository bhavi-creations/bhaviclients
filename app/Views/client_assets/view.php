<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client_assets\view.php
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client-assets') ?>">Client Assets</a></li>
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

            <div class="row">
                <!-- Client Information -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-tie"></i> Client Information</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('client-assets/edit/' . $asset['id']) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit Assets
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Client Name:</strong>
                                    <p><?= esc($asset['client_name']) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Email:</strong>
                                    <p><?= esc($asset['client_email']) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Uploaded By:</strong>
                                    <p><?= esc($asset['uploaded_by_name'] . ' ' . $asset['uploaded_by_lastname']) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Uploaded On:</strong>
                                    <p><?= date('M d, Y', strtotime($asset['created_at'])) ?></p>
                                </div>
                            </div>

                            <?php if (!empty($asset['remarks'])): ?>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <strong>Remarks:</strong>
                                        <div class="alert alert-info mb-0 mt-2">
                                            <?= nl2br(esc($asset['remarks'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Logos -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-image"></i> Client Logos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- PNG Logo -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h6 class="mb-0">PNG Logo</h6>
                                </div>
                                <div class="card-body text-center">
                                    <?php if (!empty($asset['logo_png'])): ?>
                                        <i class="fas fa-file-image fa-3x text-success mb-3"></i>
                                        <p class="text-truncate" title="<?= esc($asset['logo_png']) ?>">
                                            <?= esc($asset['logo_png']) ?>
                                        </p>
                                        <a href="<?= base_url('client-assets/download/logo/' . $asset['logo_png']) ?>" 
                                           class="btn btn-sm btn-primary btn-block">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Not uploaded</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- JPG Logo -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h6 class="mb-0">JPG Logo</h6>
                                </div>
                                <div class="card-body text-center">
                                    <?php if (!empty($asset['logo_jpg'])): ?>
                                        <i class="fas fa-file-image fa-3x text-success mb-3"></i>
                                        <p class="text-truncate" title="<?= esc($asset['logo_jpg']) ?>">
                                            <?= esc($asset['logo_jpg']) ?>
                                        </p>
                                        <a href="<?= base_url('client-assets/download/logo/' . $asset['logo_jpg']) ?>" 
                                           class="btn btn-sm btn-primary btn-block">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Not uploaded</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- PSD Logo -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h6 class="mb-0">PSD Logo</h6>
                                </div>
                                <div class="card-body text-center">
                                    <?php if (!empty($asset['logo_psd'])): ?>
                                        <i class="fas fa-file-code fa-3x text-success mb-3"></i>
                                        <p class="text-truncate" title="<?= esc($asset['logo_psd']) ?>">
                                            <?= esc($asset['logo_psd']) ?>
                                        </p>
                                        <a href="<?= base_url('client-assets/download/logo/' . $asset['logo_psd']) ?>" 
                                           class="btn btn-sm btn-primary btn-block">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Not uploaded</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Logo -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h6 class="mb-0">PDF Logo</h6>
                                </div>
                                <div class="card-body text-center">
                                    <?php if (!empty($asset['logo_pdf'])): ?>
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                        <p class="text-truncate" title="<?= esc($asset['logo_pdf']) ?>">
                                            <?= esc($asset['logo_pdf']) ?>
                                        </p>
                                        <a href="<?= base_url('client-assets/download/logo/' . $asset['logo_pdf']) ?>" 
                                           class="btn btn-sm btn-primary btn-block">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Not uploaded</p>
                                    <?php endif; ?>
                                </div>
                            </div>
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
                                    <div class="card">
                                        <div class="card-body text-center p-3">
                                            <?php
                                            $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $iconClass = 'fa-file';
                                            $iconColor = 'text-secondary';
                                            
                                            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
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
                                            } elseif (in_array($fileExt, ['zip', 'rar'])) {
                                                $iconClass = 'fa-file-archive';
                                                $iconColor = 'text-dark';
                                            }
                                            ?>
                                            <i class="fas <?= $iconClass ?> fa-3x <?= $iconColor ?> mb-2"></i>
                                            <p class="mb-2 text-truncate" style="font-size: 12px;" title="<?= esc($file) ?>">
                                                <strong><?= esc(strlen($file) > 20 ? substr(basename($file), 0, 20) . '...' : basename($file)) ?></strong>
                                            </p>
                                            <a href="<?= base_url('client-assets/download/template/' . $file) ?>" 
                                               class="btn btn-sm btn-primary btn-block">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Social Media Accounts -->
            <?php if (!empty($asset['social_media_array'])): ?>
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-share-alt"></i> Social Media Accounts</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php 
                            $platformIcons = [
                                'facebook' => ['icon' => 'fab fa-facebook', 'color' => 'primary'],
                                'instagram' => ['icon' => 'fab fa-instagram', 'color' => 'danger'],
                                'youtube' => ['icon' => 'fab fa-youtube', 'color' => 'danger'],
                                'twitter' => ['icon' => 'fab fa-twitter', 'color' => 'info'],
                                'quora' => ['icon' => 'fab fa-quora', 'color' => 'danger'],
                                'website' => ['icon' => 'fas fa-globe', 'color' => 'primary'],
                                'linkedin' => ['icon' => 'fab fa-linkedin', 'color' => 'primary'],
                                'pinterest' => ['icon' => 'fab fa-pinterest', 'color' => 'danger'],
                                'gmb' => ['icon' => 'fab fa-google', 'color' => 'danger']
                            ];
                            
                            foreach ($asset['social_media_array'] as $platform => $data): 
                                if (empty($data['link']) && empty($data['username']) && empty($data['password'])) continue;
                                $platformName = ucfirst($platform);
                                if ($platform == 'gmb') $platformName = 'Google My Business';
                                $icon = $platformIcons[$platform]['icon'] ?? 'fas fa-link';
                                $color = $platformIcons[$platform]['color'] ?? 'secondary';
                            ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-<?= $color ?>">
                                            <h6 class="mb-0 text-white">
                                                <i class="<?= $icon ?>"></i> <?= $platformName ?>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if (!empty($data['link'])): ?>
                                                <p class="mb-2">
                                                    <strong>Link:</strong><br>
                                                    <a href="<?= esc($data['link']) ?>" target="_blank" class="text-truncate d-block">
                                                        <?= esc($data['link']) ?>
                                                    </a>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($data['username'])): ?>
                                                <p class="mb-2">
                                                    <strong>Username:</strong><br>
                                                    <code><?= esc($data['username']) ?></code>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($data['password'])): ?>
                                                <p class="mb-2">
                                                    <strong>Password:</strong><br>
                                                    <code><?= esc($data['password']) ?></code>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($data['link'])): ?>
                                                <a href="<?= esc($data['link']) ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-<?= $color ?> btn-block mt-2">
                                                    <i class="fas fa-external-link-alt"></i> Visit
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Back Button -->
            <div class="row">
                <div class="col-md-12">
                    <a href="<?= base_url('client-assets') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assets List
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
