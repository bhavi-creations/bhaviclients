<?php
// C:\xampp\htdocs\bhaviclients\app\Views\notifications\index.php
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-bell"></i> <?= esc($title) ?></h1>
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
                    <h3 class="card-title"><i class="fas fa-list"></i> All Notifications</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('notifications/mark-all-read') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-check-double"></i> Mark All as Read
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($notifications)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($notifications as $notif): ?>
                                <li class="list-group-item <?= $notif['is_read'] ? '' : 'bg-light' ?>">
                                    <a href="<?= base_url('notifications/mark-read/' . $notif['id']) ?>" class="text-dark">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <?php if ($notif['type'] == 'leave_request'): ?>
                                                        <i class="fas fa-calendar-plus text-warning mr-2"></i>
                                                    <?php elseif ($notif['type'] == 'leave_approved'): ?>
                                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                                    <?php elseif ($notif['type'] == 'leave_rejected'): ?>
                                                        <i class="fas fa-times-circle text-danger mr-2"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-calendar-day text-info mr-2"></i>
                                                    <?php endif; ?>
                                                    
                                                    <strong><?= esc($notif['title']) ?></strong>
                                                    
                                                    <?php if (!$notif['is_read']): ?>
                                                        <span class="badge badge-primary ml-2">New</span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="mb-1 text-muted"><?= esc($notif['message']) ?></p>
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i> <?= timeAgo($notif['created_at']) ?>
                                                </small>
                                            </div>
                                            <div class="ml-3">
                                                <i class="fas fa-chevron-right text-muted"></i>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-bell-slash fa-4x mb-3"></i>
                            <h5>No Notifications</h5>
                            <p>You don't have any notifications yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
