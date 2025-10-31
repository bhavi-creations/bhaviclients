<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client_assets\create.php
$session = \Config\Services::session();
$hasValidationErrors = isset($validation) && is_object($validation);
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client-assets') ?>">Client Assets</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">

                    <?php if ($session->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $session->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($hasValidationErrors && $validation->getErrors()): ?>
                        <div class="alert alert-warning alert-dismissible fade show">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                            <ul class="mb-0">
                                <?php foreach ($validation->getErrors() as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Upload Client Assets</h3>
                        </div>

                        <?= form_open_multipart('client-assets/store') ?>

                        <div class="card-body">

                            <!-- Select Client -->
                            <h5 class="text-primary mb-3"><i class="fas fa-user-tie"></i> Select Client</h5>

                            <div class="form-group">
                                <label for="client_id">Client <span class="text-danger">*</span></label>
                                <select id="client_id" 
                                        name="client_id" 
                                        class="form-control select2 <?= $hasValidationErrors && $validation->hasError('client_id') ? 'is-invalid' : '' ?>" 
                                        required>
                                    <option value="">-- Select Client --</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= esc($client['id']) ?>" <?= old('client_id') == $client['id'] ? 'selected' : '' ?>>
                                            <?= esc($client['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($hasValidationErrors && $validation->hasError('client_id')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('client_id') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Logo Upload -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-image"></i> Client Logo</h5>

                            <div class="form-group">
                                <label for="logo_file">Upload Logo</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="logo_file" 
                                           class="custom-file-input" 
                                           id="logo_file"
                                           accept="image/png,image/jpeg,image/jpg,image/svg+xml">
                                    <label class="custom-file-label" for="logo_file">Choose logo file...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Allowed: PNG, JPG, JPEG, SVG (Max: 2MB)
                                </small>
                            </div>

                            <!-- Template Files -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-file-alt"></i> Template Files</h5>

                            <div class="form-group">
                                <label for="template_files">Upload Templates (Multiple)</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           name="template_files[]" 
                                           class="custom-file-input" 
                                           id="template_files"
                                           multiple
                                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,image/*">
                                    <label class="custom-file-label" for="template_files">Choose template files...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Upload multiple template files (PDF, DOC, PPT, Images, ZIP, etc.)
                                </small>
                            </div>

                            <!-- Social Media Links (Dynamic) -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-share-alt"></i> Social Media Links</h5>

                            <div id="socialMediaContainer">
                                <div class="social-media-row mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" 
                                                   name="social_platform[]" 
                                                   class="form-control" 
                                                   placeholder="Platform (e.g., Instagram)">
                                        </div>
                                        <div class="col-md-7">
                                            <input type="url" 
                                                   name="social_link[]" 
                                                   class="form-control" 
                                                   placeholder="Link (e.g., https://instagram.com/username)">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-block remove-social" disabled>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addSocialMedia" class="btn btn-success btn-sm mb-3">
                                <i class="fas fa-plus"></i> Add Another Social Media
                            </button>

                            <!-- Remarks -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-comment"></i> Additional Information</h5>

                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="3"
                                          placeholder="Any additional notes about these assets..."><?= old('remarks') ?></textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Assets
                            </button>
                            <a href="<?= base_url('client-assets') ?>" class="btn btn-secondary float-right">Cancel</a>
                        </div>

                        <?= form_close() ?>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Select2 for client dropdown
    $('#client_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Select Client --'
    });

    // Update logo file label
    $('#logo_file').on('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Choose logo file...';
        $(this).next('.custom-file-label').text(fileName);
    });

    // Update template files label
    $('#template_files').on('change', function(e) {
        var fileCount = e.target.files.length;
        var label = fileCount > 0 ? fileCount + ' file(s) selected' : 'Choose template files...';
        $(this).next('.custom-file-label').text(label);
    });

    // Add social media field
    $('#addSocialMedia').on('click', function() {
        var newRow = `
            <div class="social-media-row mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" 
                               name="social_platform[]" 
                               class="form-control" 
                               placeholder="Platform (e.g., YouTube)">
                    </div>
                    <div class="col-md-7">
                        <input type="url" 
                               name="social_link[]" 
                               class="form-control" 
                               placeholder="Link (e.g., https://youtube.com/@channel)">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-block remove-social">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#socialMediaContainer').append(newRow);
    });

    // Remove social media field
    $(document).on('click', '.remove-social', function() {
        $(this).closest('.social-media-row').remove();
    });
});
</script>
<?= $this->endSection() ?>
