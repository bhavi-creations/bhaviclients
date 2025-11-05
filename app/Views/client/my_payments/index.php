<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client\my_payments\index.php
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>My Payment Information</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active">My Payments</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <?php if (empty($projects)): ?>
                <!-- No Projects Message -->
                <div class="card card-warning">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                        <h4>No Payment Information Available</h4>
                        <p class="text-muted">Your payment details will appear here once projects are set up by the admin.</p>
                    </div>
                </div>
            <?php else: ?>

                <!-- Client Info Card -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building"></i> <?= esc($client['name']) ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Owner:</strong>
                                <p><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <strong>Email:</strong>
                                <p><?= esc($client['email']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <strong>Phone:</strong>
                                <p><?= esc($client['phone']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <strong>Manager:</strong>
                                <p><?= !empty($client['manager_name']) ? esc($client['manager_name']) : '<span class="text-muted">Not assigned</span>' ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Selector -->
                <?php if (count($projects) > 1): ?>
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-project-diagram"></i> My Projects</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Select Project:</label>
                                <select class="form-control" id="projectSelector" onchange="switchProject(this.value)">
                                    <?php foreach ($projects as $proj): ?>
                                        <option value="<?= $proj['id'] ?>" <?= $proj['id'] == $selectedProject['id'] ? 'selected' : '' ?>>
                                            <?= esc($proj['project_name']) ?> 
                                            (₹<?= number_format($proj['project_value'], 2) ?>)
                                            - <?= ucfirst($proj['status']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Project Status:</label>
                                <p>
                                    <span class="badge badge-<?= $selectedProject['status'] == 'active' ? 'success' : ($selectedProject['status'] == 'completed' ? 'primary' : 'secondary') ?> badge-lg">
                                        <?= strtoupper($selectedProject['status']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <!-- LEFT: Project Financial Summary -->
                    <div class="col-lg-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-line"></i> Project Financial Summary</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon"><i class="fas fa-rupee-sign text-primary"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Value</span>
                                                <span class="info-box-number">₹<?= number_format($summary['total_project_value'], 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Paid</span>
                                                <span class="info-box-number">₹<?= number_format($summary['total_paid'], 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Due</span>
                                                <span class="info-box-number">₹<?= number_format($summary['total_due'], 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <?php 
                                $percentage = $summary['total_project_value'] > 0 
                                    ? ($summary['total_paid'] / $summary['total_project_value']) * 100 
                                    : 0;
                                ?>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" 
                                         role="progressbar" 
                                         style="width: <?= $percentage ?>%" 
                                         aria-valuenow="<?= $percentage ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <strong><?= number_format($percentage, 1) ?>%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Project Timeline -->
                    <div class="col-lg-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Project Timeline</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon"><i class="fas fa-play text-success"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Start Date</span>
                                                <span class="info-box-number" style="font-size: 16px;">
                                                    <?= !empty($summary['project_start_date']) ? date('d M Y', strtotime($summary['project_start_date'])) : 'Not Set' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon"><i class="fas fa-flag-checkered text-danger"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">End Date</span>
                                                <span class="info-box-number" style="font-size: 16px;">
                                                    <?= !empty($summary['project_end_date']) ? date('d M Y', strtotime($summary['project_end_date'])) : 'Not Set' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($summary['project_start_date']) && !empty($summary['project_end_date'])): ?>
                                    <?php
                                    $start = new DateTime($summary['project_start_date']);
                                    $end = new DateTime($summary['project_end_date']);
                                    $today = new DateTime();
                                    $totalDays = $start->diff($end)->days;
                                    $elapsedDays = $start->diff($today)->days;
                                    $timelineProgress = $totalDays > 0 ? min(($elapsedDays / $totalDays) * 100, 100) : 0;
                                    ?>
                                    <div class="mt-3">
                                        <p class="mb-2"><strong>Project Duration:</strong> <?= $totalDays ?> days</p>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-info" 
                                                 role="progressbar" 
                                                 style="width: <?= $timelineProgress ?>%">
                                                <strong><?= number_format($timelineProgress, 1) ?>% Complete</strong>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= $elapsedDays ?> days elapsed, 
                                            <?= max(0, $totalDays - $elapsedDays) ?> days remaining
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle"></i> Project timeline will be set by admin
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PAYMENT HISTORY - FULL WIDTH -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history"></i> Payment History</h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($payments)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th>Receipt</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sn = 1; ?>
                                        <?php foreach ($payments as $payment): ?>
                                            <tr>
                                                <td><?= $sn++ ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $payment['payment_type'] == 'advance' ? 'primary' : ($payment['payment_type'] == 'final' ? 'success' : 'info') ?>">
                                                        <?= ucfirst($payment['payment_type']) ?>
                                                    </span>
                                                </td>
                                                <td><strong class="text-success">₹<?= number_format($payment['amount'], 2) ?></strong></td>
                                                <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                                <td><?= esc($payment['payment_method'] ?? '-') ?></td>
                                                <td><?= esc($payment['transaction_reference'] ?? '-') ?></td>
                                                <td>
                                                    <?php if (!empty($payment['transaction_file'])): ?>
                                                        <?php
                                                        $fileExt = strtolower(pathinfo($payment['transaction_file'], PATHINFO_EXTENSION));
                                                        ?>
                                                        <button class="btn btn-xs btn-info" 
                                                                onclick="viewFile('<?= base_url('uploads/payment_receipts/' . $payment['transaction_file']) ?>', '<?= $fileExt ?>', '<?= esc($payment['transaction_file']) ?>')"
                                                                title="View Receipt">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="<?= base_url('my-payments/download-payment-file/' . $payment['id']) ?>" 
                                                           class="btn btn-xs btn-primary" 
                                                           title="Download Receipt">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($payment['remarks'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="2" class="text-right"><strong>Total Paid:</strong></td>
                                            <td colspan="6"><strong class="text-success">₹<?= number_format($summary['total_paid'], 2) ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-2"></i>
                                <p>No payment records available yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PAYMENT SCHEDULE - FULL WIDTH BELOW HISTORY -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-check"></i> Payment Schedule</h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($schedules)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Expected Amount</th>
                                            <th>Expected Date</th>
                                            <th>Status</th>
                                            <th>Received Date</th>
                                            <th>Document</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sn = 1; ?>
                                        <?php foreach ($schedules as $schedule): ?>
                                            <tr>
                                                <td><?= $sn++ ?></td>
                                                <td><strong>₹<?= number_format($schedule['expected_amount'], 2) ?></strong></td>
                                                <td><?= date('d M Y', strtotime($schedule['expected_date'])) ?></td>
                                                <td>
                                                    <span class="badge badge-<?php
                                                        echo $schedule['status'] == 'received' || $schedule['status'] == 'paid' ? 'success' : 
                                                             ($schedule['status'] == 'overdue' ? 'danger' : 
                                                              ($schedule['status'] == 'cancelled' ? 'secondary' : 'warning'));
                                                    ?>">
                                                        <?= ucfirst($schedule['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= !empty($schedule['received_date']) ? date('d M Y', strtotime($schedule['received_date'])) : '-' ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($schedule['schedule_file'])): ?>
                                                        <?php
                                                        $fileExt = strtolower(pathinfo($schedule['schedule_file'], PATHINFO_EXTENSION));
                                                        ?>
                                                        <button class="btn btn-xs btn-info" 
                                                                onclick="viewFile('<?= base_url('uploads/payment_schedules/' . $schedule['schedule_file']) ?>', '<?= $fileExt ?>', '<?= esc($schedule['schedule_file']) ?>')"
                                                                title="View Document">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a href="<?= base_url('my-payments/download-schedule-file/' . $schedule['id']) ?>" 
                                                           class="btn btn-xs btn-primary" 
                                                           title="Download Document">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($schedule['remarks'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-2"></i>
                                <p>No payment schedules available yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</div>

<!-- File Viewer Modal -->
<div class="modal fade" id="fileViewerModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title"><i class="fas fa-file"></i> File Preview</h4>
                <button type="button" class="close text-white" onclick="closeFileViewer()">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" style="min-height: 300px;">
                <!-- Image Preview -->
                <div id="imagePreview" style="display:none;">
                    <img id="previewImage" src="" alt="File Preview" class="img-fluid" style="max-height: 500px; border: 1px solid #ddd;">
                </div>
                
                <!-- PDF Preview -->
                <div id="pdfPreview" style="display:none;">
                    <iframe id="previewPdf" src="" style="width:100%; height:500px; border:1px solid #ddd;"></iframe>
                </div>
                
                <!-- Document Info -->
                <div id="documentInfo" style="display:none; padding: 40px;">
                    <i class="fas fa-file fa-5x text-muted mb-3"></i>
                    <h5 id="fileName" class="mb-3"></h5>
                    <p class="text-muted">This file type cannot be previewed in the browser.</p>
                    <p>Click the download button below to view the file.</p>
                    <a id="downloadLink" href="" class="btn btn-primary mt-3" download>
                        <i class="fas fa-download"></i> Download File
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeFileViewer()">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Switch project
function switchProject(projectId) {
    window.location.href = '<?= base_url('my-payments') ?>?project_id=' + projectId;
}

// View file in modal
function viewFile(fileUrl, fileExt, fileName) {
    // Hide all preview sections first
    $('#imagePreview').hide();
    $('#pdfPreview').hide();
    $('#documentInfo').hide();
    
    // Reset content
    $('#previewImage').attr('src', '');
    $('#previewPdf').attr('src', '');
    
    // Determine file type and show appropriate preview
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    const ext = fileExt.toLowerCase();
    
    if (imageExtensions.includes(ext)) {
        // Show image preview
        $('#previewImage').attr('src', fileUrl);
        $('#imagePreview').show();
    } else if (ext === 'pdf') {
        // Show PDF preview
        $('#previewPdf').attr('src', fileUrl);
        $('#pdfPreview').show();
    } else {
        // Show download option for other files
        $('#fileName').text(fileName);
        $('#downloadLink').attr('href', fileUrl);
        $('#documentInfo').show();
    }
    
    // Show the modal
    $('#fileViewerModal').modal('show');
}

// Close file viewer modal
function closeFileViewer() {
    $('#fileViewerModal').modal('hide');
    
    // Clear all sources
    $('#previewImage').attr('src', '');
    $('#previewPdf').attr('src', '');
    
    // Hide all preview sections
    $('#imagePreview').hide();
    $('#pdfPreview').hide();
    $('#documentInfo').hide();
}

// Clear modal content when closed
$('#fileViewerModal').on('hidden.bs.modal', function () {
    $('#previewImage').attr('src', '');
    $('#previewPdf').attr('src', '');
    $('#imagePreview').hide();
    $('#pdfPreview').hide();
    $('#documentInfo').hide();
});
</script>
<?= $this->endSection() ?>
