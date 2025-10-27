<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title ?? 'Dashboard') ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content: Widgets and overview -->
    <section class="content">
        <div class="container-fluid">

            <!-- Stats Widgets -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= esc($totalClients ?? 0) ?></h3>
                            <p>Total Clients</p>
                        </div>
                        <div class="icon"><i class="ion ion-person-add"></i></div>
                        <a href="<?= base_url('client') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= esc($totalEmployees ?? 0) ?></h3>
                            <p>Total Employees</p>
                        </div>
                        <div class="icon"><i class="ion ion-person"></i></div>
                        <a href="<?= base_url('employee') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= esc($totalTasks ?? 0) ?></h3>
                            <p>Tasks</p>
                        </div>
                        <div class="icon"><i class="fas fa-tasks"></i></div>
                        <a href="<?= base_url('task-management') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= esc($totalFiles ?? 0) ?></h3>
                            <p>Client Files</p>
                        </div>
                        <div class="icon"><i class="ion ion-ios-folder"></i></div>
                        <a href="<?= base_url('client-uploads') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Recent Clients Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">Recent Clients</h3></div>
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; foreach($recentClients ?? [] as $client): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><?= esc($client['name']) ?></td>
                                            <td><?= esc($client['email']) ?></td>
                                            <td><?= esc($client['created_at']) ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                    <?php if (empty($recentClients)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No recent clients found.</td>
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
