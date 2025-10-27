<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-folder-open"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client-uploads') ?>">Client Uploads</a></li>
                        <li class="breadcrumb-item active"><?= esc($client['name']) ?></li>
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

            <!-- Client Info Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Client Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Company:</strong>
                            <p class="text-muted"><?= esc($client['name']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Contact Person:</strong>
                            <p class="text-muted"><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong>
                            <p class="text-muted"><?= esc($client['email']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Phone:</strong>
                            <p class="text-muted"><?= esc($client['phone']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filter Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filter Files by Date
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('client-uploads/by-client/' . $client['id']) ?>" id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" 
                                           class="form-control" 
                                           name="from_date" 
                                           id="from_date"
                                           value="<?= $fromDate ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" 
                                           class="form-control" 
                                           name="to_date" 
                                           id="to_date"
                                           value="<?= $toDate ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="<?= base_url('client-uploads/by-client/' . $client['id']) ?>" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Files Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cloud-download-alt"></i> Uploaded Files
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success"><?= count($files) ?> Files</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($files)): ?>
                        <div class="row">
                            <?php foreach ($files as $file): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body text-center p-2">
                                            <?php
                                            $fileExtension = pathinfo($file['original_name'], PATHINFO_EXTENSION);
                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                                            $isImage = in_array(strtolower($fileExtension), $imageExtensions);
                                            ?>
                                            
                                            <?php if ($isImage): ?>
                                                <!-- Image Preview -->
                                                <a href="<?= base_url('uploads/client_uploads/' . $file['file_name']) ?>" target="_blank">
                                                    <img src="<?= base_url('uploads/client_uploads/' . $file['file_name']) ?>" 
                                                         class="img-fluid rounded mb-2" 
                                                         style="max-height: 120px; object-fit: cover; width: 100%;"
                                                         alt="<?= esc($file['original_name']) ?>">
                                                </a>
                                            <?php else: ?>
                                                <!-- File Icon -->
                                                <div class="mb-2 py-3">
                                                    <?php
                                                    $iconClass = 'fa-file';
                                                    $iconColor = 'text-secondary';
                                                    
                                                    if (in_array(strtolower($fileExtension), ['mp4', 'mov', 'avi', 'mkv', 'wmv'])) {
                                                        $iconClass = 'fa-file-video';
                                                        $iconColor = 'text-danger';
                                                    } elseif (in_array(strtolower($fileExtension), ['pdf'])) {
                                                        $iconClass = 'fa-file-pdf';
                                                        $iconColor = 'text-danger';
                                                    } elseif (in_array(strtolower($fileExtension), ['zip', 'rar', '7z'])) {
                                                        $iconClass = 'fa-file-archive';
                                                        $iconColor = 'text-warning';
                                                    } elseif (in_array(strtolower($fileExtension), ['doc', 'docx'])) {
                                                        $iconClass = 'fa-file-word';
                                                        $iconColor = 'text-primary';
                                                    } elseif (in_array(strtolower($fileExtension), ['xls', 'xlsx'])) {
                                                        $iconClass = 'fa-file-excel';
                                                        $iconColor = 'text-success';
                                                    }
                                                    ?>
                                                    <i class="fas <?= $iconClass ?> fa-3x <?= $iconColor ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <h6 class="card-title text-truncate mb-1" 
                                                style="font-size: 11px;" 
                                                title="<?= esc($file['original_name']) ?>">
                                                <?= esc($file['original_name']) ?>
                                            </h6>
                                            
                                            <p class="card-text mb-2">
                                                <small class="text-muted" style="font-size: 10px;">
                                                    <i class="fas fa-calendar"></i> 
                                                    <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                                                </small>
                                                <br>
                                                <small class="text-muted" style="font-size: 10px;">
                                                    <i class="fas fa-clock"></i>
                                                    <?= date('h:i A', strtotime($file['uploaded_at'])) ?>
                                                </small>
                                                <br>
                                                <small class="text-muted" style="font-size: 10px;">
                                                    <i class="fas fa-hdd"></i> 
                                                    <?= number_format($file['file_size'] / 1024 / 1024, 2) ?> MB
                                                </small>
                                            </p>
                                            
                                            <div class="btn-group btn-group-sm w-100">
                                                <a href="<?= base_url('client-uploads/download/' . $file['id']) ?>" 
                                                   class="btn btn-success btn-sm"
                                                   title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <?php if ($isImage || in_array(strtolower($fileExtension), ['pdf'])): ?>
                                                    <a href="<?= base_url('uploads/client_uploads/' . $file['file_name']) ?>" 
                                                       class="btn btn-info btn-sm" 
                                                       target="_blank"
                                                       title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (in_array(session()->get('role_id'), [1, 2, 5])): ?>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm" 
                                                            onclick="confirmDelete(<?= $file['id'] ?>)"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <!-- Hidden Delete Form -->
                                                    <form id="deleteForm<?= $file['id'] ?>" 
                                                          action="<?= base_url('client-uploads/delete/' . $file['id']) ?>" 
                                                          method="post" 
                                                          style="display:none;">
                                                        <?= csrf_field() ?>
                                                    </form>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>No Files Found</h5>
                            <?php if ($fromDate || $toDate): ?>
                                <p>No files found for the selected date range.</p>
                                <a href="<?= base_url('client-uploads/by-client/' . $client['id']) ?>" class="btn btn-primary">
                                    <i class="fas fa-redo"></i> View All Files
                                </a>
                            <?php else: ?>
                                <p>This client hasn't uploaded any files yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('client-uploads') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Clients List
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>

<?php if (in_array(session()->get('role_id'), [1, 2, 5])): ?>
<script>
function confirmDelete(fileId) {
    if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
        document.getElementById('deleteForm' + fileId).submit();
    }
}
</script>
<?php endif; ?>

<?= $this->endSection() ?>
