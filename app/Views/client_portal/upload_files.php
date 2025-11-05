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
                        <li class="breadcrumb-item"><a href="<?= base_url('client-dashboard') ?>">Dashboard</a></li>
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
                                <i class="fas fa-cloud-upload-alt"></i> Upload Your Files
                            </h3>
                        </div>
                        <?= form_open_multipart(base_url('store-files')) ?>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Upload Instructions:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>You can upload images, videos, and raw footage</li>
                                    <li>Uploaded files will be visible to Admin, Managers, and Employees</li>
                                    <li>Multiple files can be selected at once</li>
                                    <li>Supported formats: JPG, PNG, MP4, MOV, AVI, PDF, etc.</li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label>Select Files <span class="text-danger">*</span></label>
                                <style>
                                    .file-upload-wrapper {
                                        position: relative;
                                        display: inline-block;
                                        width: 100%;
                                    }

                                    .file-upload-wrapper input[type=file] {
                                        position: absolute;
                                        left: 0;
                                        top: 0;
                                        opacity: 0;
                                        width: 100%;
                                        height: 100%;
                                        cursor: pointer;
                                    }

                                    .upload-button {
                                        display: inline-block;
                                        padding: 8px 20px;
                                        background: #007bff;
                                        color: white;
                                        border-radius: 4px;
                                        cursor: pointer;
                                        font-weight: 500;
                                    }

                                    .upload-button:hover {
                                        background: #0056b3;
                                    }

                                    .file-name-display {
                                        margin-left: 10px;
                                        color: #666;
                                    }
                                </style>

                                <div class="file-upload-wrapper">
                                    <span class="upload-button">Upload Files</span>
                                    <span class="file-name-display" id="fileNameDisplay">No file chosen</span>
                                    <input type="file"
                                        id="client_files"
                                        name="client_files[]"
                                        multiple
                                        required
                                        onchange="updateFileName(this)">
                                </div>

                                <script>
                                    function updateFileName(input) {
                                        const display = document.getElementById('fileNameDisplay');
                                        if (input.files.length === 1) {
                                            display.textContent = input.files[0].name;
                                        } else if (input.files.length > 1) {
                                            display.textContent = input.files.length + ' files selected';
                                        } else {
                                            display.textContent = 'No file chosen';
                                        }
                                    }
                                </script>

                                <small class="form-text text-muted">
                                    You can select multiple files by holding Ctrl (Windows) or Cmd (Mac)
                                </small>
                            </div>

                            <div id="filePreview" class="mt-3"></div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Sent Files
                            </button>
                            <a href="<?= base_url('client-dashboard') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-question-circle"></i> Why Upload Files?
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5><i class="fas fa-check-circle text-success"></i> Share Raw Materials</h5>
                            <p>Upload raw footage, images, or videos that need to be processed by our team.</p>

                            <h5><i class="fas fa-check-circle text-success"></i> Collaboration</h5>
                            <p>Files you upload are instantly available to admin, managers, and employees working on your projects.</p>

                            <h5><i class="fas fa-check-circle text-success"></i> Secure Storage</h5>
                            <p>Your files are securely stored and only accessible to authorized team members.</p>

                            <h5><i class="fas fa-check-circle text-success"></i> Easy Access</h5>
                            <p>Once uploaded, files can be viewed and downloaded by the team for processing.</p>
                        </div>
                    </div>

                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-lightbulb"></i> Quick Tips
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Name your files clearly before uploading</li>
                                <li>Compress large video files if possible</li>
                                <li>Group related files together</li>
                                <li>Contact us if you need to upload very large files</li>
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
                previewDiv.innerHTML += '<li class="list-group-item"><i class="fas fa-file mr-2"></i>' + file.name + ' <span class="badge badge-info float-right">' + fileSize + ' KB</span></li>';
            }

            previewDiv.innerHTML += '</ul>';
        } else {
            label.textContent = 'Choose files...';
            document.getElementById('filePreview').innerHTML = '';
        }
    });
</script>

<?= $this->endSection() ?>