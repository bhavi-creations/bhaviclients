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
                        <li class="breadcrumb-item"><a href="<?= base_url('manager-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Manage Clients</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Clients</h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= count($clients) ?> Clients</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($clients)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">S.No</th>
                                        <th width="20%">Company Name</th>
                                        <th width="20%">Contact Person</th>
                                        <th width="20%">Email</th>
                                        <th width="15%">Phone</th>
                                        <th width="10%">Created</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($clients as $client): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><strong><?= esc($client['name']) ?></strong></td>
                                            <td><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></td>
                                            <td><?= esc($client['email']) ?></td>
                                            <td><?= esc($client['phone']) ?></td>
                                            <td><small><?= date('M d, Y', strtotime($client['created_at'])) ?></small></td>
                                            <td>
                                                <a href="<?= base_url('manager/client-files/' . $client['id']) ?>" 
                                                   class="btn btn-sm btn-info"
                                                   title="View Files">
                                                    <i class="fas fa-folder"></i> Files
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No clients found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
