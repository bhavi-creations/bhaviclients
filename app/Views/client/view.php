<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client\view.php 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Client Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client') ?>">Clients</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">

                    <!-- Client Details Card -->
                    <div class="card shadow-lg">
                        <div class="card-header border-0 bg-primary">
                            <h3 class="card-title text-white">
                                <i class="fas fa-building mr-2"></i>
                                <strong><?= esc($client['name']) ?></strong>
                            </h3>
                            <div class="card-tools">
                                <a href="<?= base_url('client/edit/' . $client['id']) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-info-circle"></i> Basic Information
                                    </h5>

                                    <div class="form-group">
                                        <label><i class="fas fa-building"></i> Company Name</label>
                                        <p class="form-control-static"><strong><?= esc($client['name']) ?></strong></p>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fas fa-user"></i> Owner Name</label>
                                        <p class="form-control-static">
                                            <?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fas fa-envelope"></i> Email</label>
                                        <p class="form-control-static">
                                            <a href="mailto:<?= esc($client['email']) ?>"><?= esc($client['email']) ?></a>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fas fa-phone"></i> Phone</label>
                                        <p class="form-control-static">
                                            <a href="tel:<?= esc($client['phone']) ?>"><?= esc($client['phone']) ?></a>
                                        </p>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-users"></i> Management Information
                                    </h5>

                                    <div class="form-group">
                                        <label><i class="fas fa-user-tie"></i> Manager Name</label>
                                        <p class="form-control-static">
                                            <?= !empty($client['manager_name']) ? esc($client['manager_name']) : '<span class="text-muted">Not provided</span>' ?>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fas fa-phone-alt"></i> Manager Phone</label>
                                        <p class="form-control-static">
                                            <?php if (!empty($client['manager_phone'])): ?>
                                                <a href="tel:<?= esc($client['manager_phone']) ?>"><?= esc($client['manager_phone']) ?></a>
                                            <?php else: ?>
                                                <span class="text-muted">Not provided</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fas fa-link"></i> Reference</label>
                                        <p class="form-control-static">
                                            <?= !empty($client['reference']) ? esc($client['reference']) : '<span class="text-muted">Not provided</span>' ?>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-check"></i> Started Date</label>
                                        <p class="form-control-static">
                                            <?= !empty($client['started_date']) ? date('F d, Y', strtotime($client['started_date'])) : '<span class="text-muted">Not provided</span>' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks Section -->
                            <?php if (!empty($client['remarks'])): ?>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="text-primary border-bottom pb-2">
                                            <i class="fas fa-comment-dots"></i> Remarks
                                        </h5>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <?= nl2br(esc($client['remarks'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- System Information -->
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="far fa-calendar-plus"></i> Created At</label>
                                        <p class="form-control-static text-muted">
                                            <?= date('F d, Y h:i A', strtotime($client['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="far fa-calendar-check"></i> Last Updated</label>
                                        <p class="form-control-static text-muted">
                                            <?= date('F d, Y h:i A', strtotime($client['updated_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">

                            <a href="<?= base_url('client') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Client List
                            </a>
                            <a href="<?= base_url('client/files/' . $client['id']) ?>" class="btn btn-primary">
                                <i class="fas fa-file-upload"></i> Manage Files
                            </a>
                            <a href="<?= base_url('maintenance/client/' . $client['id']) ?>" class="btn btn-warning">
                                <i class="fas fa-project-diagram"></i> Project Details
                            </a>

                            <a href="<?= base_url('client-payment/' . $client['id']) ?>" class="btn btn-success">
                                <i class="fas fa-rupee-sign"></i> Payment Management
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>