<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_portal\weekly_schedule_view.php 
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
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
                        <li class="breadcrumb-item"><a href="<?= base_url('my-weekly-schedule') ?>">Weekly Schedules</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <!-- Schedule Info Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-week"></i> Schedule Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Client:</strong>
                                    <p class="text-muted"><?= esc($client['name'] ?? 'N/A') ?></p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Week Period:</strong>
                                    <p class="text-muted">
                                        <?= date('M d', strtotime($schedule['week_start_date'])) ?> - 
                                        <?= date('M d, Y', strtotime($schedule['week_end_date'])) ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong>
                                    <p>
                                        <span class="badge badge-success badge-lg">Published</span>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Created:</strong>
                                    <p class="text-muted"><?= date('M d, Y', strtotime($schedule['created_at'])) ?></p>
                                </div>
                            </div>

                            <?php if (!empty($schedule['remarks'])): ?>
                                <hr>
                                <strong>Remarks:</strong>
                                <p class="text-muted"><?= nl2br(esc($schedule['remarks'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Schedule Table -->
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Weekly Work Schedule</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="120">Day</th>
                                            <?php foreach ($departments as $dept): ?>
                                                <th><?= esc($dept) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($days as $day): ?>
                                            <tr>
                                                <td class="font-weight-bold bg-light"><?= $day ?></td>
                                                <?php foreach ($departments as $dept): ?>
                                                    <td>
                                                        <?php 
                                                        $task = $scheduleData[$day][$dept] ?? '';
                                                        echo !empty($task) ? nl2br(esc($task)) : '<span class="text-muted">-</span>';
                                                        ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('my-weekly-schedule') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Schedules
            </a>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
