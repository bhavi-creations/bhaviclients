<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-cloud-upload-alt"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Client Uploads</li>
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
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Clients with Uploaded Files
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary"><?= count($clients) ?> Clients</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($clients)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Client Name</th>
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th class="text-center">Total Files</th>
                                        <th>Last Upload</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($clients as $client): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td>
                                                <strong class="text-primary">
                                                    <i class="fas fa-building"></i>
                                                    <?= esc($client['name']) ?>
                                                </strong>
                                            </td>
                                            <td><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></td>
                                            <td>
                                                <a href="mailto:<?= esc($client['email']) ?>">
                                                    <i class="fas fa-envelope"></i> <?= esc($client['email']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <i class="fas fa-phone"></i> <?= esc($client['phone']) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success badge-lg">
                                                    <?= $client['file_count'] ?> Files
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar"></i>
                                                <?= date('M d, Y h:i A', strtotime($client['last_upload'])) ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('client-uploads/by-client/' . $client['id']) ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-folder-open"></i> View Files
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>No Files Uploaded Yet</h5>
                            <p>No clients have uploaded any files yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
