<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_report\index.php 
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
                            <h3 class="card-title">All Client Reports</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('client-report/create') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus-circle"></i> Upload New Report
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Report Date</th>
                                            <th>Client Name</th>
                                            <th>Title</th>
                                            <th>Files</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($reports)): ?>
                                            <?php $sn = 1; ?>
                                            <?php foreach ($reports as $report): ?>
                                                <?php 
                                                $files = !empty($report['file_uploads']) ? json_decode($report['file_uploads'], true) : [];
                                                $fileCount = is_array($files) ? count($files) : 0;
                                                ?>
                                                <tr>
                                                    <td><?= $sn++ ?></td>
                                                    <td>
                                                        <strong><?= date('M d, Y', strtotime($report['report_date'])) ?></strong>
                                                    </td>
                                                    <td><?= esc($report['client_name'] ?? 'N/A') ?></td>
                                                    <td><?= esc($report['title']) ?></td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-file"></i> <?= $fileCount ?> file(s)
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('client-report/view/' . $report['id']) ?>" 
                                                           class="btn btn-sm btn-primary" 
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('client-report/edit/' . $report['id']) ?>" 
                                                           class="btn btn-sm btn-info" 
                                                           title="Edit Report">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" 
                                                           onclick="confirmDelete(<?= $report['id'] ?>)" 
                                                           class="btn btn-sm btn-danger" 
                                                           title="Delete Report">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center py-4">No reports found.</td></tr>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this report? This will also delete all associated files.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '<?= base_url("client-report/delete/") ?>' + id;
        $('#deleteModal').modal('show');
    }
</script>

<?= $this->endSection() ?>
