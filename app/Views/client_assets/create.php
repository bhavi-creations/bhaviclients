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

                            <!-- Logo Upload - Separate Fields -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-image"></i> Client Logos</h5>

                            <div class="row">
                                <!-- PNG Logo -->
                                <div class="col-md-6 mb-3">
                                    <label for="logo_png">PNG Logo</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               name="logo_png" 
                                               class="custom-file-input" 
                                               id="logo_png"
                                               accept=".png">
                                        <label class="custom-file-label" for="logo_png">Upload PNG format</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> PNG format only (Max: 5MB)
                                    </small>
                                </div>

                                <!-- JPG Logo -->
                                <div class="col-md-6 mb-3">
                                    <label for="logo_jpg">JPG Logo</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               name="logo_jpg" 
                                               class="custom-file-input" 
                                               id="logo_jpg"
                                               accept=".jpg,.jpeg">
                                        <label class="custom-file-label" for="logo_jpg">Upload JPG format</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> JPG/JPEG format only (Max: 5MB)
                                    </small>
                                </div>

                                <!-- PSD Logo -->
                                <div class="col-md-6 mb-3">
                                    <label for="logo_psd">PSD Logo</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               name="logo_psd" 
                                               class="custom-file-input" 
                                               id="logo_psd"
                                               accept=".psd">
                                        <label class="custom-file-label" for="logo_psd">Upload PSD format</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> PSD format only (Max: 50MB)
                                    </small>
                                </div>

                                <!-- PDF Logo -->
                                <div class="col-md-6 mb-3">
                                    <label for="logo_pdf">PDF Logo</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               name="logo_pdf" 
                                               class="custom-file-input" 
                                               id="logo_pdf"
                                               accept=".pdf">
                                        <label class="custom-file-label" for="logo_pdf">Upload PDF format</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> PDF format only (Max: 5MB)
                                    </small>
                                </div>
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

                            <!-- Social Media Links (Fixed Platforms) -->
                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-share-alt"></i> Social Media Accounts</h5>

                            <?php
                            $platforms = [
                                'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook'],
                                'instagram' => ['name' => 'Instagram', 'icon' => 'fab fa-instagram'],
                                'youtube' => ['name' => 'YouTube', 'icon' => 'fab fa-youtube'],
                                'twitter' => ['name' => 'Twitter', 'icon' => 'fab fa-twitter'],
                                'quora' => ['name' => 'Quora', 'icon' => 'fab fa-quora'],
                                'website' => ['name' => 'Website', 'icon' => 'fas fa-globe'],
                                'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin'],
                                'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest'],
                                'gmb' => ['name' => 'Google My Business', 'icon' => 'fab fa-google']
                            ];
                            ?>

                            <?php foreach ($platforms as $key => $platform): ?>
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="<?= $platform['icon'] ?>"></i> <?= $platform['name'] ?>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?= $platform['name'] ?> Link</label>
                                                    <input type="url" 
                                                           name="<?= $key ?>_link" 
                                                           class="form-control" 
                                                           placeholder="https://<?= $key ?>.com/yourprofile">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?= $platform['name'] ?> Username</label>
                                                    <input type="text" 
                                                           name="<?= $key ?>_username" 
                                                           class="form-control" 
                                                           placeholder="Username">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?= $platform['name'] ?> Password</label>
                                                    <input type="text" 
                                                           name="<?= $key ?>_password" 
                                                           class="form-control" 
                                                           placeholder="Password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

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

    // Update file labels for all logo inputs
    $('#logo_png').on('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Upload PNG format';
        $(this).next('.custom-file-label').text(fileName);
    });

    $('#logo_jpg').on('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Upload JPG format';
        $(this).next('.custom-file-label').text(fileName);
    });

    $('#logo_psd').on('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Upload PSD format';
        $(this).next('.custom-file-label').text(fileName);
    });

    $('#logo_pdf').on('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Upload PDF format';
        $(this).next('.custom-file-label').text(fileName);
    });

    // Update template files label
    $('#template_files').on('change', function(e) {
        var fileCount = e.target.files.length;
        var label = fileCount > 0 ? fileCount + ' file(s) selected' : 'Choose template files...';
        $(this).next('.custom-file-label').text(label);
    });
});
</script>
<?= $this->endSection() ?>
