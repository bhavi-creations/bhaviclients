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
                        <li class="breadcrumb-item active">Upload Files</li>
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

            <div class="row">
                <!-- Upload Form -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cloud-upload-alt"></i> Upload Files for Client
                            </h3>
                        </div>
                        <?= form_open_multipart(base_url('manager/store-files')) ?>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Upload Instructions:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Select a client to upload files for</li>
                                    <li>Upload Excel sheets, PDFs, or documents</li>
                                    <li>Clients can download these files from their portal</li>
                                    <li>Multiple files can be uploaded at once</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label>Select Client <span class="text-danger">*</span></label>
                                <select class="form-control" name="client_id" id="client_id" required>
                                    <option value="">-- Select Client --</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>">
                                            <?= esc($client['name']) ?> - <?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Select Files <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input" 
                                           id="client_files" 
                                           name="client_files[]" 
                                           multiple 
                                           required>
                                    <label class="custom-file-label" for="client_files">Choose files...</label>
                                </div>
                                <small class="form-text text-muted">
                                    Supported: Excel (.xls, .xlsx), PDF, Word, CSV
                                </small>
                            </div>

                            <div id="filePreview" class="mt-3"></div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Files
                            </button>
                            <a href="<?= base_url('manager-dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>

                <!-- Clients List -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users"></i> Quick Access - Client Files
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($clients as $client): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= esc($client['name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></small>
                                        </div>
                                        <a href="<?= base_url('manager/client-files/' . $client['id']) ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-folder"></i> View Files
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Update file input label and show preview
document.getElementById('client_files').addEventListener('change', function(e) {
    var fileCount = this.files.length;
    var label = document.querySelector('.custom-file-label');
    
    if (fileCount > 0) {
        label.textContent = fileCount + ' file(s) selected';
        
        // Show file preview
        var previewDiv = document.getElementById('filePreview');
        previewDiv.innerHTML = '<h6>Selected Files:</h6><ul class="list-group">';
        
        for (var i = 0; i < fileCount; i++) {
            var file = this.files[i];
            var fileSize = (file.size / 1024).toFixed(2);
            
            var iconClass = 'fa-file';
            if (file.name.endsWith('.xlsx') || file.name.endsWith('.xls')) {
                iconClass = 'fa-file-excel text-success';
            } else if (file.name.endsWith('.pdf')) {
                iconClass = 'fa-file-pdf text-danger';
            } else if (file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                iconClass = 'fa-file-word text-primary';
            }
            
            previewDiv.innerHTML += '<li class="list-group-item"><i class="fas ' + iconClass + ' mr-2"></i>' + file.name + ' <span class="badge badge-info float-right">' + fileSize + ' KB</span></li>';
        }
        
        previewDiv.innerHTML += '</ul>';
    } else {
        label.textContent = 'Choose files...';
        document.getElementById('filePreview').innerHTML = '';
    }
});
</script>

<?= $this->endSection() ?>
