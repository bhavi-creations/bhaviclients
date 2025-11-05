<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client_payment\index.php 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Payment Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client') ?>">Clients</a></li>
                        <li class="breadcrumb-item active">Payments</li>
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

            <!-- Project Selector & Add New Project -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-project-diagram"></i> Projects</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addProjectModal">
                            <i class="fas fa-plus"></i> Add New Project
                        </button>
                    </div>
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
                            <label>Current Project Status:</label>
                            <p>
                                <span class="badge badge-<?= $selectedProject['status'] == 'active' ? 'success' : ($selectedProject['status'] == 'completed' ? 'primary' : 'secondary') ?>">
                                    <?= strtoupper($selectedProject['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">


                <!-- RIGHT: Project Timeline -->
                <div class="col-lg-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Project Timeline</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#editTimelineModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
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
                                    <div class="progress">
                                        <div class="progress-bar bg-info"
                                            role="progressbar"
                                            style="width: <?= $timelineProgress ?>%">
                                            <?= number_format($timelineProgress, 1) ?>% Complete
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?= $elapsedDays ?> days elapsed,
                                        <?= max(0, $totalDays - $elapsedDays) ?> days remaining
                                    </small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> Set project timeline to track progress
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>



                <!-- LEFT: Project Financial Summary -->
                <div class="col-lg-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Project Financial Summary</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#editProjectValueModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
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
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success"
                                    role="progressbar"
                                    style="width: <?= $percentage ?>%"
                                    aria-valuenow="<?= $percentage ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    <?= number_format($percentage, 1) ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <!-- PAYMENT HISTORY - FULL WIDTH -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Payment History</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addPaymentModal">
                            <i class="fas fa-plus"></i> Add Payment
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($payments)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Reference</th>
                                        <th>File</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
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
                                            <td><strong>₹<?= number_format($payment['amount'], 2) ?></strong></td>
                                            <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                            <td><?= esc($payment['payment_method'] ?? '-') ?></td>
                                            <td><?= esc($payment['transaction_reference'] ?? '-') ?></td>
                                            <td>
                                                <?php if (!empty($payment['transaction_file'])): ?>
                                                    <?php
                                                    $fileExt = strtolower(pathinfo($payment['transaction_file'], PATHINFO_EXTENSION));
                                                    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']);
                                                    ?>
                                                    <button class="btn btn-sm btn-info"
                                                        onclick="viewFile('<?= base_url('uploads/payment_receipts/' . $payment['transaction_file']) ?>', '<?= $fileExt ?>', '<?= esc($payment['transaction_file']) ?>')"
                                                        title="View File">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= base_url('client-payment/download-payment-file/' . $payment['id']) ?>"
                                                        class="btn btn-sm btn-primary"
                                                        title="Download File">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><?= esc($payment['remarks'] ?? '-') ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm"
                                                    onclick="editPayment(<?= htmlspecialchars(json_encode($payment), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="<?= base_url('client-payment/delete-payment/' . $payment['id']) ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Delete this payment?')"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right">Total Paid:</td>
                                        <td colspan="7">₹<?= number_format($summary['total_paid'], 2) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-2"></i>
                            <p>No payments recorded yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PAYMENT SCHEDULE - FULL WIDTH BELOW HISTORY -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check"></i> Payment Schedule</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addScheduleModal">
                            <i class="fas fa-plus"></i> Add Schedule
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($schedules)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Expected Amount</th>
                                        <th>Expected Date</th>
                                        <th>Status</th>
                                        <th>Received Date</th>
                                        <th>File</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
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
                                                                            echo $schedule['status'] == 'received' || $schedule['status'] == 'paid' ? 'success' : ($schedule['status'] == 'overdue' ? 'danger' : ($schedule['status'] == 'cancelled' ? 'secondary' : 'warning'));
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
                                                    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']);
                                                    ?>
                                                    <button class="btn btn-sm btn-info"
                                                        onclick="viewFile('<?= base_url('uploads/payment_schedules/' . $schedule['schedule_file']) ?>', '<?= $fileExt ?>', '<?= esc($schedule['schedule_file']) ?>')"
                                                        title="View File">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= base_url('client-payment/download-schedule-file/' . $schedule['id']) ?>"
                                                        class="btn btn-sm btn-primary"
                                                        title="Download File">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><?= esc($schedule['remarks'] ?? '-') ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm"
                                                    onclick="editSchedule(<?= htmlspecialchars(json_encode($schedule), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="<?= base_url('client-payment/delete-schedule/' . $schedule['id']) ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Delete this schedule?')"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-2"></i>
                            <p>No payment schedules yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- MODALS START HERE -->

<!-- Add New Project Modal -->
<div class="modal fade" id="addProjectModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title"><i class="fas fa-plus"></i> Add New Project</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open_multipart('client-payment/add-project/' . $client['id']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label>Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="project_name" class="form-control" required placeholder="e.g., Website Development Phase 2">
                </div>
                <div class="form-group">
                    <label>Project Value (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="project_value" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="project_start_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="project_end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create Project</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Edit Project Value Modal -->
<div class="modal fade" id="editProjectValueModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Edit Project Value</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open('client-payment/update-project-value/' . $client['id']) ?>
            <input type="hidden" name="project_id" value="<?= $selectedProject['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Total Project Value (₹)</label>
                    <input type="number" name="total_project_value" class="form-control"
                        value="<?= $summary['total_project_value'] ?>"
                        step="0.01" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Edit Timeline Modal -->
<div class="modal fade" id="editTimelineModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Edit Project Timeline</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open('client-payment/update-timeline/' . $client['id']) ?>
            <input type="hidden" name="project_id" value="<?= $selectedProject['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Project Start Date</label>
                    <input type="date" name="project_start_date" class="form-control"
                        value="<?= $summary['project_start_date'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Project End Date</label>
                    <input type="date" name="project_end_date" class="form-control"
                        value="<?= $summary['project_end_date'] ?? '' ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">Update Timeline</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title">Add Payment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open_multipart('client-payment/add-payment/' . $client['id']) ?>
            <input type="hidden" name="project_id" value="<?= $selectedProject['id'] ?>">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Type</label>
                            <select name="payment_type" class="form-control" required>
                                <option value="advance">Advance</option>
                                <option value="installment" selected>Installment</option>
                                <option value="final">Final</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Amount (₹)</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <input type="text" name="payment_method" class="form-control" placeholder="e.g., Bank Transfer, Cash">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Transaction Reference</label>
                    <input type="text" name="transaction_reference" class="form-control" placeholder="e.g., TRANS123456">
                </div>
                <div class="form-group">
                    <label>Upload Receipt/Proof <small class="text-muted">(PDF, JPG, PNG, DOC - Max 5MB)</small></label>
                    <input type="file" name="transaction_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Add Payment</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Edit Payment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editPaymentForm" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Type</label>
                                <select name="payment_type" id="edit_payment_type" class="form-control" required>
                                    <option value="advance">Advance</option>
                                    <option value="installment">Installment</option>
                                    <option value="final">Final</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount (₹)</label>
                                <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date</label>
                                <input type="date" name="payment_date" id="edit_payment_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <input type="text" name="payment_method" id="edit_payment_method" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Transaction Reference</label>
                        <input type="text" name="transaction_reference" id="edit_transaction_reference" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Upload New Receipt/Proof <small class="text-muted">(Leave empty to keep existing)</small></label>
                        <input type="file" name="transaction_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small id="existing_file_info" class="text-muted"></small>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" id="edit_remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update Payment</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title">Add Payment Schedule</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open_multipart('client-payment/add-schedule/' . $client['id']) ?>
            <input type="hidden" name="project_id" value="<?= $selectedProject['id'] ?>">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expected Amount (₹)</label>
                            <input type="number" name="expected_amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expected Date</label>
                            <input type="date" name="expected_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Upload Document <small class="text-muted">(PDF, JPG, PNG, DOC - Max 5MB)</small></label>
                    <input type="file" name="schedule_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Add Schedule</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Edit Payment Schedule</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editScheduleForm" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Expected Amount (₹)</label>
                                <input type="number" name="expected_amount" id="edit_expected_amount" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Expected Date</label>
                                <input type="date" name="expected_date" id="edit_expected_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="edit_status" class="form-control" onchange="toggleReceivedDate()" required>
                                    <option value="pending">Pending</option>
                                    <option value="received">Received</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="received_date_group" style="display:none;">
                            <div class="form-group">
                                <label>Received Date</label>
                                <input type="date" name="received_date" id="edit_received_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Upload New Document <small class="text-muted">(Leave empty to keep existing)</small></label>
                        <input type="file" name="schedule_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small id="existing_schedule_file_info" class="text-muted"></small>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" id="edit_schedule_remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update Schedule</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- File Viewer Modal - FIXED VERSION -->
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

                <!-- Document Info (for non-previewable files) -->
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
        window.location.href = '<?= base_url('client-payment/' . $client['id']) ?>?project_id=' + projectId;
    }

    // Edit payment
    function editPayment(payment) {
        document.getElementById('editPaymentForm').action = '<?= base_url('client-payment/edit-payment/') ?>' + payment.id;
        document.getElementById('edit_payment_type').value = payment.payment_type;
        document.getElementById('edit_amount').value = payment.amount;
        document.getElementById('edit_payment_date').value = payment.payment_date;
        document.getElementById('edit_payment_method').value = payment.payment_method || '';
        document.getElementById('edit_transaction_reference').value = payment.transaction_reference || '';
        document.getElementById('edit_remarks').value = payment.remarks || '';

        if (payment.transaction_file) {
            document.getElementById('existing_file_info').textContent = 'Current file: ' + payment.transaction_file;
        } else {
            document.getElementById('existing_file_info').textContent = '';
        }

        $('#editPaymentModal').modal('show');
    }

    // Edit schedule
    function editSchedule(schedule) {
        document.getElementById('editScheduleForm').action = '<?= base_url('client-payment/edit-schedule/') ?>' + schedule.id;
        document.getElementById('edit_expected_amount').value = schedule.expected_amount;
        document.getElementById('edit_expected_date').value = schedule.expected_date;
        document.getElementById('edit_status').value = schedule.status;
        document.getElementById('edit_received_date').value = schedule.received_date || '';
        document.getElementById('edit_schedule_remarks').value = schedule.remarks || '';

        if (schedule.schedule_file) {
            document.getElementById('existing_schedule_file_info').textContent = 'Current file: ' + schedule.schedule_file;
        } else {
            document.getElementById('existing_schedule_file_info').textContent = '';
        }

        toggleReceivedDate();
        $('#editScheduleModal').modal('show');
    }

    // Toggle received date field
    function toggleReceivedDate() {
        const status = document.getElementById('edit_status').value;
        const receivedGroup = document.getElementById('received_date_group');

        if (status === 'received') {
            receivedGroup.style.display = 'block';
            document.getElementById('edit_received_date').required = true;
        } else {
            receivedGroup.style.display = 'none';
            document.getElementById('edit_received_date').required = false;
        }
    }
</script>


<script>
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

        // Clear all sources to stop loading
        $('#previewImage').attr('src', '');
        $('#previewPdf').attr('src', '');

        // Hide all preview sections
        $('#imagePreview').hide();
        $('#pdfPreview').hide();
        $('#documentInfo').hide();
    }

    // Clear modal content when it's fully closed
    $('#fileViewerModal').on('hidden.bs.modal', function() {
        $('#previewImage').attr('src', '');
        $('#previewPdf').attr('src', '');
        $('#imagePreview').hide();
        $('#pdfPreview').hide();
        $('#documentInfo').hide();
    });
</script>


<?= $this->endSection() ?>