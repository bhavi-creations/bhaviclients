<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Project Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('maintenance') ?>">Project Details</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

            <!-- Edit Form Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Update Project Details
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('maintenance/update/'.$record['id']) ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Client <span class="text-danger">*</span></label>
                                    <select name="client_id" class="form-control" required>
                                        <?php foreach($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>" <?= ($client['id'] == $record['client_id'] ? 'selected' : '') ?>>
                                                <?= esc($client['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title" 
                                           class="form-control" 
                                           placeholder="Enter project title"
                                           value="<?= esc($record['title']) ?>"
                                           required />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" 
                                      class="form-control" 
                                      rows="4"
                                      placeholder="Enter project description"><?= esc($record['description']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="Enter additional remarks or notes"><?= esc($record['remarks'] ?? '') ?></textarea>
                        </div>

                        <!-- Existing Files -->
                        <?php if (!empty($record['file_uploads'])): ?>
                            <?php $files = json_decode($record['file_uploads'], true); ?>
                            <?php if (is_array($files) && count($files) > 0): ?>
                                <div class="form-group">
                                    <label><i class="fas fa-paperclip"></i> Existing Files (<?= count($files) ?>)</label>
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($files as $file): ?>
                                                    <?php
                                                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $iconClass = 'fa-file';
                                                    $badgeClass = 'secondary';
                                                    
                                                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $iconClass = 'fa-file-image';
                                                        $badgeClass = 'success';
                                                    } elseif (in_array($extension, ['pdf'])) {
                                                        $iconClass = 'fa-file-pdf';
                                                        $badgeClass = 'danger';
                                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                                        $iconClass = 'fa-file-word';
                                                        $badgeClass = 'primary';
                                                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                        $iconClass = 'fa-file-excel';
                                                        $badgeClass = 'success';
                                                    }
                                                    ?>
                                                    <div class="list-group-item d-flex justify-content-between align-items-center p-2">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas <?= $iconClass ?> fa-2x text-<?= $badgeClass ?> mr-2"></i>
                                                            <div>
                                                                <small class="d-block text-truncate" style="max-width: 300px;" title="<?= esc($file) ?>">
                                                                    <?= esc($file) ?>
                                                                </small>
                                                                <span class="badge badge-<?= $badgeClass ?> badge-sm">
                                                                    <?= strtoupper($extension) ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="<?= base_url('maintenance/downloadFile/' . $record['id'] . '/' . urlencode($file)) ?>" 
                                                               class="btn btn-info" 
                                                               title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-danger" 
                                                                    onclick="confirmDeleteFile('<?= esc($file) ?>')"
                                                                    title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Add New Files -->
                        <div class="form-group">
                            <label>Upload New Files</label>
                            <div class="custom-file">
                                <input type="file" 
                                       name="files[]" 
                                       class="custom-file-input" 
                                       id="fileInput"
                                       multiple
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                                <label class="custom-file-label" for="fileInput">Choose files (multiple allowed)</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Allowed: PDF, DOC, DOCX, XLS, XLSX, Images. Max size: 10MB per file. New files will be added to existing files.
                            </small>
                        </div>

                        <div class="form-group mt-4">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save"></i> Update Project Details
                            </button>
                            <a href="<?= base_url('maintenance/client/' . $record['client_id']) ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Update file input label with selected file names
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var files = e.target.files;
    var label = document.querySelector('.custom-file-label');
    
    if (files.length > 0) {
        if (files.length === 1) {
            label.textContent = files[0].name;
        } else {
            label.textContent = files.length + ' files selected';
        }
    } else {
        label.textContent = 'Choose files (multiple allowed)';
    }
});

function confirmDeleteFile(filename) {
    if (confirm('Are you sure you want to delete this file: ' + filename + '?')) {
        window.location.href = '<?= base_url('maintenance/deleteFile/' . $record['id'] . '/') ?>' + encodeURIComponent(filename);
    }
}
</script>

<?= $this->endSection() ?>
