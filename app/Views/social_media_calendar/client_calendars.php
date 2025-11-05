<?php
// C:\xampp\htdocs\bhaviclients\app\Views\social_media_calendar\client_calendars.php
use App\Models\SocialMediaCalendarModel;
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
                        <li class="breadcrumb-item"><a href="<?= base_url('social-media-calendar') ?>">Social Media Calendars</a></li>
                        <li class="breadcrumb-item active"><?= esc($client['name']) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <!-- Client Info Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building"></i> <?= esc($client['name']) ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Email:</strong>
                            <p><?= esc($client['email']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Phone:</strong>
                            <p><?= esc($client['phone']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Total Calendars:</strong>
                            <p><span class="badge badge-info badge-lg"><?= count($calendars) ?></span></p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="<?= base_url('social-media-calendar/upload/' . $client['id']) ?>" class="btn btn-success">
                            <i class="fas fa-plus"></i> Upload New Calendar
                        </a>
                        <a href="<?= base_url('social-media-calendar') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Calendars in Card Format -->
            <?php if (!empty($calendars)): ?>
                <div class="row">
                    <?php foreach ($calendars as $calendar): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info">
                                    <h3 class="card-title">
                                        <i class="fas fa-calendar-day"></i>
                                        <strong><?= SocialMediaCalendarModel::getMonthName($calendar['calendar_month']) ?> <?= $calendar['calendar_year'] ?></strong>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <?php
                                        $ext = strtolower($calendar['file_extension']);
                                        $iconClass = 'fa-file';
                                        $iconColor = 'text-secondary';

                                        if ($ext == 'pdf') {
                                            $iconClass = 'fa-file-pdf';
                                            $iconColor = 'text-danger';
                                        } elseif (in_array($ext, ['doc', 'docx'])) {
                                            $iconClass = 'fa-file-word';
                                            $iconColor = 'text-primary';
                                        } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                            $iconClass = 'fa-file-excel';
                                            $iconColor = 'text-success';
                                        } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                            $iconClass = 'fa-file-image';
                                            $iconColor = 'text-info';
                                        }
                                        ?>
                                        <i class="fas <?= $iconClass ?> <?= $iconColor ?> fa-3x"></i>
                                    </div>

                                    <h5 class="mb-2"><?= esc($calendar['original_name']) ?></h5>

                                    <p class="text-muted mb-2">
                                        <i class="fas fa-hdd"></i> <?= number_format($calendar['file_size'] / 1024, 2) ?> KB
                                    </p>

                                    <?php if (!empty($calendar['remarks'])): ?>
                                        <div class="alert alert-light mb-3">
                                            <strong>Remarks:</strong><br>
                                            <small><?= esc($calendar['remarks']) ?></small>
                                        </div>
                                    <?php endif; ?>

                                    <p class="text-muted mb-0">
                                        <small>
                                            <i class="fas fa-clock"></i>
                                            Uploaded: <?= date('d M Y, h:i A', strtotime($calendar['uploaded_at'])) ?>
                                        </small>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group-vertical d-flex" role="group">
                                        <!-- View Button -->
                                        <a href="<?= base_url('social-media-calendar/view/' . $calendar['id']) ?>"
                                            class="btn btn-info btn-sm"
                                            target="_blank"
                                            title="View File">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        <div class="btn-group d-flex mt-1" role="group">
                                            <a href="<?= base_url('social-media-calendar/download/' . $calendar['id']) ?>"
                                                class="btn btn-primary btn-sm flex-fill"
                                                title="Download">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button"
                                                class="btn btn-warning btn-sm flex-fill"
                                                onclick="editCalendar(<?= $calendar['id'] ?>, '<?= esc($calendar['calendar_month']) ?>', '<?= esc($calendar['calendar_year']) ?>', '<?= esc($calendar['remarks'] ?? '') ?>')"
                                                title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="<?= base_url('social-media-calendar/delete/' . $calendar['id']) ?>"
                                                class="btn btn-danger btn-sm flex-fill"
                                                onclick="return confirm('Delete this calendar permanently?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-calendar-times fa-4x mb-3"></i>
                            <h5>No Social Media Calendars Yet</h5>
                            <p>Upload calendars for this client using the button above</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<!-- Edit Calendar Modal -->
<div class="modal fade" id="editCalendarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Calendar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open_multipart('social-media-calendar/update', ['id' => 'editCalendarForm']) ?>
            <div class="modal-body">

                <input type="hidden" name="calendar_id" id="edit_calendar_id">
                <input type="hidden" name="client_id" value="<?= $client['id'] ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Month <span class="text-danger">*</span></label>
                            <select name="calendar_month" id="edit_month" class="form-control" required>
                                <?php
                                $months = [
                                    1 => 'January',
                                    2 => 'February',
                                    3 => 'March',
                                    4 => 'April',
                                    5 => 'May',
                                    6 => 'June',
                                    7 => 'July',
                                    8 => 'August',
                                    9 => 'September',
                                    10 => 'October',
                                    11 => 'November',
                                    12 => 'December'
                                ];
                                foreach ($months as $num => $name):
                                ?>
                                    <option value="<?= $num ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Year <span class="text-danger">*</span></label>
                            <select name="calendar_year" id="edit_year" class="form-control" required>
                                <?php
                                $currentYear = date('Y');
                                for ($year = $currentYear - 1; $year <= $currentYear + 2; $year++):
                                ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Replace File (Optional)</label>
                    <div class="custom-file">
                        <input type="file"
                            name="calendar_file"
                            class="custom-file-input"
                            id="editFileInput"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <label class="custom-file-label" for="editFileInput">Choose new file...</label>
                    </div>
                    <small class="text-muted">Leave empty to keep existing file</small>
                    <div id="currentFileName" class="mt-2"></div>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" id="edit_remarks" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> Update Calendar
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function editCalendar(id, month, year, remarks) {
        document.getElementById('edit_calendar_id').value = id;
        document.getElementById('edit_month').value = month;
        document.getElementById('edit_year').value = year;
        document.getElementById('edit_remarks').value = remarks;

        // Reset file input
        document.getElementById('editFileInput').value = '';
        document.querySelector('#editCalendarModal .custom-file-label').textContent = 'Choose new file...';
        document.getElementById('currentFileName').innerHTML = '';

        $('#editCalendarModal').modal('show');
    }

    // Close modal functionality
    $('#editCalendarModal .close, #editCalendarModal [data-dismiss="modal"]').on('click', function() {
        $('#editCalendarModal').modal('hide');
    });

    // Show selected file name
    document.getElementById('editFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const label = document.querySelector('#editCalendarModal .custom-file-label');

        if (file) {
            label.textContent = file.name;
            const size = (file.size / 1024).toFixed(2);
            document.getElementById('currentFileName').innerHTML =
                '<div class="alert alert-info mt-2"><small><i class="fas fa-file"></i> New file: ' +
                file.name + ' (' + size + ' KB)</small></div>';
        } else {
            label.textContent = 'Choose new file...';
            document.getElementById('currentFileName').innerHTML = '';
        }
    });
</script>
<?= $this->endSection() ?>
