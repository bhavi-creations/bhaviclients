<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client_portal\weekly_schedule_index.php
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
                        <li class="breadcrumb-item"><a href="<?= base_url('client-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Weekly Schedules</li>
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

            <!-- Schedules List -->
            <div class="card shadow-lg">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-week"></i> Your Weekly Schedules
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info badge-lg"><?= count($schedules) ?> Total</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($schedules)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">S.No.</th>
                                        <th>Week Period</th>
                                        <th style="width: 120px;">Status</th>
                                        <th>Remarks</th>
                                        <th style="width: 150px;">Created</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td>
                                                <strong>
                                                    <i class="far fa-calendar-alt text-primary"></i>
                                                    <?= date('M d', strtotime($schedule['week_start_date'])) ?> - 
                                                    <?= date('M d, Y', strtotime($schedule['week_end_date'])) ?>
                                                </strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php
                                                    $start = new DateTime($schedule['week_start_date']);
                                                    $end = new DateTime($schedule['week_end_date']);
                                                    $interval = $start->diff($end);
                                                    echo ($interval->days + 1) . ' days';
                                                    ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Published
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($schedule['remarks'])): ?>
                                                    <span class="text-muted">
                                                        <?= esc(strlen($schedule['remarks']) > 80 ? substr($schedule['remarks'], 0, 80) . '...' : $schedule['remarks']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-clock"></i>
                                                    <?= date('M d, Y', strtotime($schedule['created_at'])) ?>
                                                    <br>
                                                    <?= date('h:i A', strtotime($schedule['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('view-weekly-schedule/' . $schedule['id']) ?>" 
                                                   class="btn btn-info btn-sm btn-block"
                                                   title="View Schedule">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Weekly Schedules Available</h5>
                            <p class="text-muted">Your weekly schedules will appear here once created by the admin.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
