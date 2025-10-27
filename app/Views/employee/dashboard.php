<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title ?? 'Employee Dashboard') ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Stats Widgets -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= esc($totalTasks ?? 0) ?></h3>
                            <p>Total Tasks</p>
                        </div>
                        <div class="icon"><i class="ion ion-bag"></i></div>
                        <a href="<?= base_url('my-tasks') ?>" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= esc($completedTasks ?? 0) ?></h3>
                            <p>Completed Tasks</p>
                        </div>
                        <div class="icon"><i class="ion ion-stats-bars"></i></div>
                        <a href="<?= base_url('my-tasks') ?>" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= esc($pendingTasks ?? 0) ?></h3>
                            <p>Pending Tasks</p>
                        </div>
                        <div class="icon"><i class="ion ion-person-add"></i></div>
                        <a href="<?= base_url('submit-work') ?>" class="small-box-footer">Submit Work <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">Recent Tasks</h3></div>
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; foreach($recentTasks ?? [] as $task): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><?= esc($task['title']) ?></td>
                                            <td><?= esc($task['client_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge badge-<?= $task['status'] == 'Completed' ? 'success' : ($task['status'] == 'In Progress' ? 'warning' : 'secondary') ?>">
                                                    <?= esc($task['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($task['submitted_at']) ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                    <?php if (empty($recentTasks)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No recent tasks found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
