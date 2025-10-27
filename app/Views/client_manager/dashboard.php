<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Client Manager Dashboard</h1>
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

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalClients ?></h3>
                            <p>Total Clients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= base_url('manager/clients') ?>" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $totalTasks ?></h3>
                            <p>Work Updates</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="<?= base_url('manager/work-updates') ?>" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $totalFiles ?></h3>
                            <p>Client Files</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="<?= base_url('manager/upload-files') ?>" class="small-box-footer">
                            Manage Files <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Recent Clients
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('manager/clients') ?>" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentClients)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentClients as $client): ?>
                                    <tr>
                                        <td><strong><?= esc($client['name']) ?></strong></td>
                                        <td><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></td>
                                        <td><?= esc($client['email']) ?></td>
                                        <td><?= esc($client['phone']) ?></td>
                                        <td><small><?= date('M d, Y', strtotime($client['created_at'])) ?></small></td>
                                        <td>
                                            <a href="<?= base_url('manager/client-files/' . $client['id']) ?>" 
                                               class="btn btn-sm btn-info"
                                               title="View Files">
                                                <i class="fas fa-folder"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-info-circle"></i> No clients yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
