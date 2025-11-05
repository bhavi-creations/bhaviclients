<?php
// C:\xampp\htdocs\bhaviclients\app\Views\social_media_calendar\index.php
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-alt"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <div class="card shadow-lg">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-building"></i> Clients with Social Media Calendars</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('social-media-calendar/upload') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Upload Calendar
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($clients)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Client Name</th>
                                        <th>Calendars</th>
                                        <th>Last Upload</th>
                                        <th>Latest Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($clients as $client): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td>
                                                <strong><?= esc($client['client_name']) ?></strong>
                                                <br><small class="text-muted"><?= esc($client['email']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info badge-lg">
                                                    <?= $client['calendar_count'] ?> Calendar(s)
                                                </span>
                                            </td>
                                            <td><?= date('d M Y', strtotime($client['last_upload'])) ?></td>
                                            <td>
                                                <?php if (!empty($client['latest_remarks'])): ?>
                                                    <small><?= esc($client['latest_remarks']) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('social-media-calendar/client/' . $client['client_id']) ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View Calendars">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-calendar-times fa-4x mb-3"></i>
                            <h5>No Social Media Calendars Uploaded Yet</h5>
                            <p>Upload calendars to clients using the button above</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
