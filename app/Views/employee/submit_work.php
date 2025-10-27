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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('my-tasks') ?>">My Tasks</a></li>
                        <li class="breadcrumb-item active">Submit Work</li>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Submit Your Daily Work</h3>
                </div>
                
                <?= form_open_multipart(base_url('store-work')) ?>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label for="title">Work Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= (session('validation') && session('validation')->hasError('title')) ? 'is-invalid' : '' ?>" 
                                   id="title" 
                                   name="title" 
                                   placeholder="e.g., Website Homepage Design"
                                   value="<?= old('title') ?>">
                            <?php if (session('validation') && session('validation')->hasError('title')): ?>
                                <div class="invalid-feedback">
                                    <?= session('validation')->getError('title') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="client_id">Client (Optional)</label>
                            <select class="form-control" id="client_id" name="client_id">
                                <option value="">-- Select Client (if applicable) --</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= old('client_id') == $client['id'] ? 'selected' : '' ?>>
                                        <?= esc($client['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Work Description <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= (session('validation') && session('validation')->hasError('description')) ? 'is-invalid' : '' ?>" 
                                      id="description" 
                                      name="description" 
                                      rows="6"
                                      placeholder="Describe what you worked on today in detail..."><?= old('description') ?></textarea>
                            <?php if (session('validation') && session('validation')->hasError('description')): ?>
                                <div class="invalid-feedback">
                                    <?= session('validation')->getError('description') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control <?= (session('validation') && session('validation')->hasError('status')) ? 'is-invalid' : '' ?>" 
                                    id="status" 
                                    name="status">
                                <option value="Pending" <?= old('status') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="In Progress" <?= old('status') == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= old('status') == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Review" <?= old('status') == 'Review' ? 'selected' : '' ?>>Review</option>
                            </select>
                            <?php if (session('validation') && session('validation')->hasError('status')): ?>
                                <div class="invalid-feedback">
                                    <?= session('validation')->getError('status') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="files">Attach Files (Optional)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="files" name="files[]" multiple>
                                <label class="custom-file-label" for="files">Choose files...</label>
                            </div>
                            <small class="form-text text-muted">You can upload multiple files (images, documents, etc.)</small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Submit Work
                        </button>
                        <a href="<?= base_url('my-tasks') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                <?= form_close() ?>
            </div>

        </div>
    </section>
</div>

<script>
// Update file input label with selected file names
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = '';
    if (this.files.length > 1) {
        fileName = this.files.length + ' files selected';
    } else if (this.files.length === 1) {
        fileName = this.files[0].name;
    }
    var label = document.querySelector('.custom-file-label');
    label.textContent = fileName || 'Choose files...';
});
</script>

<?= $this->endSection() ?>
