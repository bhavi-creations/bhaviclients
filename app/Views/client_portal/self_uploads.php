<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>My Uploaded Files</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('client-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Uploads</li>
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

            <div class="card shadow-lg">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i> Files You Have Uploaded
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary badge-lg"><?= count($files) ?> Files</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($files)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">S.No.</th>
                                        <th style="width: 80px;">Type</th>
                                        <th>File Name</th>
                                        <th style="width: 120px;">Size</th>
                                        <th style="width: 150px;">Uploaded</th>
                                        <th style="width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($files as $file): ?>
                                        <?php
                                        $fileExtension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                        $iconClass = 'fa-file';
                                        $iconColor = 'text-secondary';
                                        $badgeClass = 'secondary';
                                        
                                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp', 'svg'])) {
                                            $iconClass = 'fa-file-image';
                                            $iconColor = 'text-warning';
                                            $badgeClass = 'warning';
                                        } elseif ($fileExtension == 'pdf') {
                                            $iconClass = 'fa-file-pdf';
                                            $iconColor = 'text-danger';
                                            $badgeClass = 'danger';
                                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                            $iconClass = 'fa-file-word';
                                            $iconColor = 'text-primary';
                                            $badgeClass = 'primary';
                                        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                            $iconClass = 'fa-file-excel';
                                            $iconColor = 'text-success';
                                            $badgeClass = 'success';
                                        } elseif ($fileExtension == 'csv') {
                                            $iconClass = 'fa-file-csv';
                                            $iconColor = 'text-info';
                                            $badgeClass = 'info';
                                        } elseif (in_array($fileExtension, ['zip', 'rar', '7z'])) {
                                            $iconClass = 'fa-file-archive';
                                            $iconColor = 'text-dark';
                                            $badgeClass = 'dark';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td class="text-center">
                                                <i class="fas <?= $iconClass ?> fa-2x <?= $iconColor ?>"></i>
                                            </td>
                                            <td>
                                                <strong><?= esc($file['original_name']) ?></strong>
                                                <br>
                                                <span class="badge badge-<?= $badgeClass ?>">
                                                    <?= strtoupper($fileExtension) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-hdd text-muted"></i>
                                                <?php
                                                $sizeKB = $file['file_size'] / 1024;
                                                if ($sizeKB > 1024) {
                                                    echo number_format($sizeKB / 1024, 2) . ' MB';
                                                } else {
                                                    echo number_format($sizeKB, 2) . ' KB';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-calendar"></i>
                                                    <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                                                    <br>
                                                    <i class="far fa-clock"></i>
                                                    <?= date('h:i A', strtotime($file['uploaded_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('download-file/' . $file['id']) ?>" 
                                                   class="btn btn-primary btn-sm mb-1"
                                                   title="Download File">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <button type="button"
                                                        class="btn btn-danger btn-sm mb-1"
                                                        onclick="confirmDelete(<?= $file['id'] ?>)"
                                                        title="Delete File">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Uploads Yet</h5>
                            <p class="text-muted">You have not uploaded any files yet.</p>
                            <a href="<?= base_url('upload-files') ?>" class="btn btn-primary">
                                <i class="fas fa-cloud-upload-alt"></i> Upload Files
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(fileId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to recover this file!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = '<?= base_url('delete-self-upload/') ?>' + fileId;
            form.submit();
        }
    });
}
</script>
<?= $this->endSection() ?>
