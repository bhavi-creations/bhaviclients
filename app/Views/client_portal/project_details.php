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
                        <li class="breadcrumb-item active">Project Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Flash Messages -->
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

            <!-- Table Card -->
            <div class="card shadow-lg">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram"></i> Your Project Details
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info badge-lg"><?= count($records) ?> Records</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($records)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">S.No</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th style="width: 100px;">Files</th>
                                        <th style="width: 150px;">Created</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($records as $row): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td>
                                                <strong><?= esc($row['title']) ?></strong>
                                            </td>
                                            <td>
                                                <?php 
                                                $desc = esc($row['description'] ?: 'No description');
                                                echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($row['file_uploads'])): ?>
                                                    <?php $files = json_decode($row['file_uploads'], true); ?>
                                                    <?php if (is_array($files) && count($files) > 0): ?>
                                                        <span class="badge badge-success badge-lg">
                                                            <i class="fas fa-paperclip"></i> <?= count($files) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">0</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-calendar"></i>
                                                    <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                                    <br>
                                                    <i class="far fa-clock"></i>
                                                    <?= date('h:i A', strtotime($row['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('view-project-detail/' . $row['id']) ?>" 
                                                   class="btn btn-info btn-sm btn-block"
                                                   title="View Details">
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
                            <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Project Details Yet</h5>
                            <p class="text-muted">Project details will appear here once added by the admin.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
