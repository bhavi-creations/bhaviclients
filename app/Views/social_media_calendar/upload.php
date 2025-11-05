<?php
// C:\xampp\htdocs\bhaviclients\app\Views\social_media_calendar\upload.php
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-upload"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('social-media-calendar') ?>">Social Media Calendars</a></li>
                        <li class="breadcrumb-item active">Upload</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-upload"></i> Upload Social Media Calendar</h3>
                </div>
                <?= form_open_multipart('social-media-calendar/store') ?>
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Select Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Select Client --</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client['id'] ?>" 
                                        <?= ($selectedClient && $selectedClient['id'] == $client['id']) ? 'selected' : '' ?>>
                                    <?= esc($client['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($validation && $validation->hasError('client_id')): ?>
                            <small class="text-danger"><?= $validation->getError('client_id') ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Calendar Month <span class="text-danger">*</span></label>
                                <select name="calendar_month" class="form-control" required>
                                    <option value="">-- Select Month --</option>
                                    <?php
                                    $currentMonth = date('n');
                                    $months = [
                                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                    ];
                                    foreach ($months as $num => $name):
                                    ?>
                                        <option value="<?= $num ?>" <?= $num == $currentMonth ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($validation && $validation->hasError('calendar_month')): ?>
                                    <small class="text-danger"><?= $validation->getError('calendar_month') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Calendar Year <span class="text-danger">*</span></label>
                                <select name="calendar_year" class="form-control" required>
                                    <option value="">-- Select Year --</option>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($year = $currentYear - 1; $year <= $currentYear + 2; $year++): 
                                    ?>
                                        <option value="<?= $year ?>" <?= $year == $currentYear ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <?php if ($validation && $validation->hasError('calendar_year')): ?>
                                    <small class="text-danger"><?= $validation->getError('calendar_year') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Upload Calendar File <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" 
                                   name="calendar_file" 
                                   class="custom-file-input" 
                                   id="fileInput" 
                                   required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="fileInput">Choose file...</label>
                        </div>
                        <small class="text-muted">
                            Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG | Maximum size: 10MB
                        </small>
                        <?php if ($validation && $validation->hasError('calendar_file')): ?>
                            <small class="text-danger d-block"><?= $validation->getError('calendar_file') ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Remarks / Notes</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Optional notes about this calendar (e.g., campaign themes, special events)"></textarea>
                    </div>

                    <div id="filePreview" class="mt-3"></div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Upload Calendar
                    </button>
                    <a href="<?= base_url('social-media-calendar') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
                <?= form_close() ?>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Show selected file
document.getElementById('fileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('filePreview');
    const label = document.querySelector('.custom-file-label');
    
    if (file) {
        label.textContent = file.name;
        
        const size = (file.size / 1024).toFixed(2);
        let html = '<div class="alert alert-info">';
        html += '<strong>Selected File:</strong><br>';
        html += '<i class="fas fa-file"></i> ' + file.name + ' (' + size + ' KB)';
        html += '</div>';
        preview.innerHTML = html;
    } else {
        label.textContent = 'Choose file...';
        preview.innerHTML = '';
    }
});
</script>
<?= $this->endSection() ?>
