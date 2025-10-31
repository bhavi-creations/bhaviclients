<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_report\view.php 
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client-report') ?>">Client Reports</a></li>
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
                <div class="col-md-4">
                    <!-- Report Information Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Report Information</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-5">Client:</dt>
                                <dd class="col-sm-7">
                                    <strong><?= esc($client['name'] ?? 'N/A') ?></strong>
                                </dd>

                                <dt class="col-sm-5">Title:</dt>
                                <dd class="col-sm-7"><?= esc($report['title']) ?></dd>

                                <dt class="col-sm-5">Report Date:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-info badge-lg">
                                        <?= date('M d, Y', strtotime($report['report_date'])) ?>
                                    </span>
                                </dd>

                                <dt class="col-sm-5">Uploaded On:</dt>
                                <dd class="col-sm-7"><?= date('M d, Y h:i A', strtotime($report['created_at'])) ?></dd>

                                <dt class="col-sm-5">Total Files:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-success"><?= count($files) ?> file(s)</span>
                                </dd>
                            </dl>

                            <?php if (!empty($report['remarks'])): ?>
                                <hr>
                                <h6><i class="fas fa-comment"></i> Remarks:</h6>
                                <p class="text-muted"><?= nl2br(esc($report['remarks'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Uploaded Files Card -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-folder-open"></i> Uploaded Files</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($files)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>File Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sno = 1; ?>
                                            <?php foreach ($files as $file): ?>
                                                <tr>
                                                    <td><?= $sno++ ?></td>
                                                    <td>
                                                        <i class="fas fa-file mr-2"></i>
                                                        <?= esc($file) ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('client-report/downloadFile/' . $report['id'] . '/' . $file) ?>" 
                                                           class="btn btn-info btn-sm" 
                                                           title="Download">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                        <?= form_open('client-report/deleteFile/' . $report['id'] . '/' . $file, ['class' => 'd-inline', 'onsubmit' => "return confirm('Delete this file?')"]) ?>
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?= form_close() ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted">No files uploaded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('client-report') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports List
            </a>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
