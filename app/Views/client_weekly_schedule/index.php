<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_weekly_schedule\index.php 
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
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

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
                        <div class="card-header border-0">
                            <h3 class="card-title">Clients with Weekly Schedules</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('weekly-schedule/create') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus-circle"></i> Create New Schedule
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Client Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Total Schedules</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($clients)): ?>
                                            <?php $sn = 1; ?>
                                            <?php foreach ($clients as $client): ?>
                                                <tr>
                                                    <td><?= $sn++ ?></td>
                                                    <td><strong><?= esc($client['name']) ?></strong></td>
                                                    <td><?= esc($client['email']) ?></td>
                                                    <td><?= esc($client['phone']) ?></td>
                                                    <td>
                                                        <span class="badge badge-info badge-lg">
                                                            <i class="fas fa-calendar-week"></i> <?= $client['schedule_count'] ?> Schedule(s)
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('weekly-schedule/client/' . $client['id']) ?>" 
                                                           class="btn btn-sm btn-primary" 
                                                           title="View All Schedules">
                                                            <i class="fas fa-list"></i> View Schedules
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No schedules created yet</h5>
                                                    <p class="text-muted">Create your first weekly schedule to get started</p>
                                                    <a href="<?= base_url('weekly-schedule/create') ?>" class="btn btn-success mt-2">
                                                        <i class="fas fa-plus-circle"></i> Create Schedule
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
