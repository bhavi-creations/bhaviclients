<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\client_assets\index.php
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
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
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

            <div class="card shadow-lg">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-folder-open"></i> All Client Assets</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary badge-lg"><?= count($assets) ?> Total</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($assets)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Client Name</th>
                                        <th>Logos</th>
                                        <th>Templates</th>
                                        <th>Social Media</th>
                                        <th>Uploaded By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($assets as $asset): ?>
                                        <?php
                                        $templates = !empty($asset['template_files']) ? json_decode($asset['template_files'], true) : [];
                                        $socialMedia = !empty($asset['social_media']) ? json_decode($asset['social_media'], true) : [];
                                        
                                        // Count logos
                                        $logoCount = 0;
                                        if (!empty($asset['logo_png'])) $logoCount++;
                                        if (!empty($asset['logo_jpg'])) $logoCount++;
                                        if (!empty($asset['logo_psd'])) $logoCount++;
                                        if (!empty($asset['logo_pdf'])) $logoCount++;
                                        ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td><strong><?= esc($asset['client_name']) ?></strong></td>
                                            <td>
                                                <?php if ($logoCount > 0): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> <?= $logoCount ?> file(s)
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php if (!empty($asset['logo_png'])): ?><span class="badge badge-info">PNG</span> <?php endif; ?>
                                                        <?php if (!empty($asset['logo_jpg'])): ?><span class="badge badge-info">JPG</span> <?php endif; ?>
                                                        <?php if (!empty($asset['logo_psd'])): ?><span class="badge badge-info">PSD</span> <?php endif; ?>
                                                        <?php if (!empty($asset['logo_pdf'])): ?><span class="badge badge-info">PDF</span> <?php endif; ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">No logos</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= count($templates) ?> file(s)
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    <?= count($socialMedia) ?> platform(s)
                                                </span>
                                            </td>
                                            <td><?= esc($asset['uploaded_by_name'] . ' ' . $asset['uploaded_by_lastname']) ?></td>
                                            <td><?= date('M d, Y', strtotime($asset['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('employee-client-assets/view/' . $asset['id']) ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-5 text-center">
                            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Client Assets Available</h5>
                            <p class="text-muted">Client assets will appear here once uploaded by admin</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
