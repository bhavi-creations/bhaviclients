<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Project Details Record</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('maintenance') ?>">Project Details</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Create Form Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> New Project Details
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('maintenance/store') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Client <span class="text-danger">*</span></label>
                                    <select name="client_id" class="form-control" required>
                                        <option value="">-- Select Client --</option>
                                        <?php foreach($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>" <?= old('client_id') == $client['id'] ? 'selected' : '' ?>>
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
                                           value="<?= old('title') ?>"
                                           required />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" 
                                      class="form-control" 
                                      rows="4"
                                      placeholder="Enter project description"><?= old('description') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="Enter additional remarks or notes"><?= old('remarks') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Upload Files</label>
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
                                Allowed: PDF, DOC, DOCX, XLS, XLSX, Images. Max size: 10MB per file
                            </small>
                        </div>

                        <div class="form-group mt-4">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save"></i> Save Project Details
                            </button>
                            <a href="<?= base_url('maintenance') ?>" class="btn btn-secondary">
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
</script>

<?= $this->endSection() ?>
