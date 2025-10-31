<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Project Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('maintenance') ?>">Project Details</a></li>
                        <?php if (isset($client)): ?>
                            <li class="breadcrumb-item"><a href="<?= base_url('maintenance/client/' . $client['id']) ?>"><?= esc($client['name']) ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active">View</li>
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

            <!-- Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram"></i> 
                        <strong><?= esc($record['title']) ?></strong>
                    </h3>
                    <div class="card-tools">
                        <?php if (session()->get('role_id') == 1): ?>
                            <a href="<?= base_url('maintenance/edit/' . $record['id']) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column: Details -->
                        <div class="col-md-12">
                            <?php if (isset($client)): ?>
                                <div class="form-group">
                                    <label><i class="fas fa-building"></i> Client</label>
                                    <p class="form-control-static">
                                        <strong><?= esc($client['name']) ?></strong>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label><i class="fas fa-heading"></i> Title</label>
                                <p class="form-control-static">
                                    <strong><?= esc($record['title']) ?></strong>
                                </p>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-align-left"></i> Description</label>
                                <div class="card">
                                    <div class="card-body">
                                        <?= nl2br(esc($record['description'] ?: 'No description provided.')) ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($record['remarks'])): ?>
                                <div class="form-group">
                                    <label><i class="fas fa-comment-dots"></i> Remarks</label>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <?= nl2br(esc($record['remarks'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="far fa-calendar-plus"></i> Created At</label>
                                        <p class="form-control-static">
                                            <?= date('F d, Y h:i A', strtotime($record['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="far fa-calendar-check"></i> Last Updated</label>
                                        <p class="form-control-static">
                                            <?= date('F d, Y h:i A', strtotime($record['updated_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attached Files Section -->
                    <?php if (!empty($record['file_uploads'])): ?>
                        <?php $files = json_decode($record['file_uploads'], true); ?>
                        <?php if (is_array($files) && count($files) > 0): ?>
                            <hr>
                            <div class="form-group">
                                <h4><i class="fas fa-paperclip"></i> Attached Files (<?= count($files) ?>)</h4>
                            </div>

                            <div class="row">
                                <?php foreach ($files as $file): ?>
                                    <?php
                                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                    $isPdf = ($extension == 'pdf');
                                    $iconClass = 'fa-file';
                                    $badgeClass = 'secondary';
                                    
                                    if ($isImage) {
                                        $iconClass = 'fa-file-image';
                                        $badgeClass = 'success';
                                    } elseif ($isPdf) {
                                        $iconClass = 'fa-file-pdf';
                                        $badgeClass = 'danger';
                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                        $iconClass = 'fa-file-word';
                                        $badgeClass = 'primary';
                                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                        $iconClass = 'fa-file-excel';
                                        $badgeClass = 'success';
                                    }
                                    
                                    $filePath = base_url('uploads/maintenance/' . $file);
                                    ?>
                                    
                                    <div class="col-md-<?= ($isImage || $isPdf) ? '6' : '6' ?> mb-3">
                                        <div class="card">
                                            <?php if ($isImage): ?>
                                                <!-- Image Preview -->
                                                <img src="<?= $filePath ?>" 
                                                     class="card-img-top" 
                                                     alt="<?= esc($file) ?>"
                                                     style="height: 300px; object-fit: contain; background: #f4f4f4; padding: 10px;">
                                            <?php elseif ($isPdf): ?>
                                                <!-- PDF Preview -->
                                                <iframe src="<?= $filePath ?>" 
                                                        style="width: 100%; height: 300px; border: none;"
                                                        title="<?= esc($file) ?>">
                                                </iframe>
                                            <?php else: ?>
                                                <!-- File Icon -->
                                                <div class="card-body text-center" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas <?= $iconClass ?> fa-5x text-<?= $badgeClass ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="card-footer">
                                                <div class="mb-2">
                                                    <small class="d-block text-truncate" title="<?= esc($file) ?>">
                                                        <strong><?= esc($file) ?></strong>
                                                    </small>
                                                    <span class="badge badge-<?= $badgeClass ?>">
                                                        <?= strtoupper($extension) ?>
                                                    </span>
                                                </div>
                                                <div class="btn-group btn-group-sm btn-block">
                                                    <a href="<?= base_url('maintenance/downloadFile/' . $record['id'] . '/' . urlencode($file)) ?>" 
                                                       class="btn btn-info">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                    <?php if (session()->get('role_id') == 1): ?>
                                                        <button type="button"
                                                                class="btn btn-danger" 
                                                                onclick="confirmDeleteFile('<?= esc($file) ?>')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <hr>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-folder-open fa-3x mb-2"></i>
                            <p>No files attached to this project</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <?php if (session()->get('role_id') == 1): ?>
                        <?php if (isset($client)): ?>
                            <a href="<?= base_url('maintenance/client/' . $client['id']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Client Records
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('maintenance') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to All Clients
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= base_url('client-maintenance') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to My Project Details
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
function confirmDeleteFile(filename) {
    if (confirm('Are you sure you want to delete this file: ' + filename + '?')) {
        window.location.href = '<?= base_url('maintenance/deleteFile/' . $record['id'] . '/') ?>' + encodeURIComponent(filename);
    }
}
</script>

<?= $this->endSection() ?>
