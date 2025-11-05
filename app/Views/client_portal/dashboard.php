<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Welcome, <?= esc($client['name']) ?>!</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- NEW: Notifications Widget Row -->
            <div class="row">
                <div class="col-md-12">
                    <?= view('components/notifications_widget') ?>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalTasks ?></h3>
                            <p>Work Updates</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="<?= base_url('work-updates') ?>" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $totalSchedules ?></h3>
                            <p>Weekly Schedules</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <a href="<?= base_url('my-weekly-schedule') ?>" class="small-box-footer">
                            View Schedules <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $totalFiles ?></h3>
                            <p>Working Calendars</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <a href="<?= base_url('download-files') ?>" class="small-box-footer">
                            View Files <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <?php if (session()->get('role_id') == 3): ?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?= $totalProjects ?></h3>
                                <p>Project Details</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <a href="<?= base_url('client-maintenance') ?>" class="small-box-footer">
                                View Projects <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="row">
                <!-- Current Week Schedule -->
                <div class="col-md-8">
                    <div class="card card-primary card-outline shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-week"></i> This Week's Schedule
                            </h3>
                            <div class="card-tools">
                                <?php if (!empty($weekSchedule)): ?>
                                    <span class="badge badge-info">
                                        <?= date('M d', strtotime($weekSchedule['week_start'])) ?> -
                                        <?= date('M d, Y', strtotime($weekSchedule['week_end'])) ?>
                                    </span>
                                <?php endif; ?>
                                <a href="<?= base_url('my-weekly-schedule') ?>" class="btn btn-sm btn-primary ml-2">
                                    View All Schedules
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($weekSchedule)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover m-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="100">Day</th>
                                                <?php foreach ($weekSchedule['departments'] as $dept): ?>
                                                    <th><?= esc($dept) ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            foreach ($days as $day):
                                                $isToday = ($day === $weekSchedule['current_day']);
                                            ?>
                                                <tr <?= $isToday ? 'class="table-primary"' : '' ?>>
                                                    <td class="font-weight-bold">
                                                        <?= $day ?>
                                                        <?php if ($isToday): ?>
                                                            <span class="badge badge-success badge-sm ml-1">Today</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <?php foreach ($weekSchedule['departments'] as $dept): ?>
                                                        <td>
                                                            <?php
                                                            $task = $weekSchedule['schedule'][$day][$dept] ?? '';
                                                            if (!empty($task)) {
                                                                echo '<span class="badge badge-info">' . nl2br(esc($task)) . '</span>';
                                                            } else {
                                                                echo '<span class="text-muted">-</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if (!empty($weekSchedule['notes'])): ?>
                                    <div class="alert alert-info m-3">
                                        <strong><i class="fas fa-sticky-note"></i> Week Notes:</strong><br>
                                        <?= nl2br(esc($weekSchedule['notes'])) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="p-5 text-center text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <h5>No Schedule for This Week</h5>
                                    <p>Your weekly schedule will appear here once created by the admin.</p>
                                    <a href="<?= base_url('my-weekly-schedule') ?>" class="btn btn-primary">
                                        View All Schedules
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building"></i> Company Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <strong>Company Name</strong>
                            <p class="text-muted"><?= esc($client['name']) ?></p>
                            <hr>

                            <strong>Contact Person</strong>
                            <p class="text-muted">
                                <?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>
                            </p>
                            <hr>

                            <strong>Email</strong>
                            <p class="text-muted"><?= esc($client['email']) ?></p>
                            <hr>

                            <strong>Phone</strong>
                            <p class="text-muted"><?= esc($client['phone']) ?></p>
                            <hr>

                            <strong>Started Date</strong>
                            <p class="text-muted">
                                <?php if (!empty($client['started_date'])): ?>
                                    <?= date('M d, Y', strtotime($client['started_date'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Not set</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </section>
</div>

<?= $this->endSection() ?>