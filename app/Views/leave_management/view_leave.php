<?php
// C:\xampp\htdocs\bhaviclients\app\Views\leave_management\view_leave.php
use App\Models\LeaveRequestModel;
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
                        <li class="breadcrumb-item"><a href="<?= base_url('leave-management') ?>">Leave Management</a></li>
                        <li class="breadcrumb-item active">View Leave</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card shadow-lg">
                        <div class="card-header <?= $leave['status'] == 'pending' ? 'bg-warning' : ($leave['status'] == 'approved' ? 'bg-success' : 'bg-danger') ?>">
                            <h3 class="card-title text-white">
                                <i class="fas fa-file-alt"></i>
                                <strong>Leave Request Details</strong>
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-dark badge-lg"><?= strtoupper($leave['status']) ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Employee Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-user"></i> Employee Information</h5>
                                    <p><strong>Name:</strong> <?= esc($leave['employee_name']) ?></p>
                                    <p><strong>Employee Code:</strong> <?= esc($leave['employee_code']) ?></p>
                                    <p><strong>Email:</strong> <?= esc($leave['email']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-calendar-check"></i> Leave Details</h5>
                                    <?php $leaveTypes = LeaveRequestModel::getLeaveTypes(); ?>
                                    <p><strong>Leave Type:</strong> 
                                        <span class="badge badge-info"><?= esc($leaveTypes[$leave['leave_type']]) ?></span>
                                    </p>
                                    <p><strong>Total Days:</strong> 
                                        <span class="badge badge-primary"><?= $leave['total_days'] ?> day(s)</span>
                                    </p>
                                    <p><strong>Applied On:</strong> <?= date('d M Y, h:i A', strtotime($leave['created_at'])) ?></p>
                                </div>
                            </div>

                            <hr>

                            <!-- Duration -->
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-calendar-alt"></i> Leave Duration</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-day"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Start Date</span>
                                                <span class="info-box-number"><?= date('d M Y', strtotime($leave['start_date'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-day"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">End Date</span>
                                                <span class="info-box-number"><?= date('d M Y', strtotime($leave['end_date'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reason -->
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-comment-dots"></i> Reason for Leave</h5>
                                <div class="alert alert-light border">
                                    <?= nl2br(esc($leave['reason'])) ?>
                                </div>
                            </div>

                            <!-- Admin Remarks -->
                            <?php if (!empty($leave['admin_remarks'])): ?>
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-sticky-note"></i> Admin Remarks</h5>
                                    <div class="alert alert-info">
                                        <?= nl2br(esc($leave['admin_remarks'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Approval Info -->
                            <?php if ($leave['status'] != 'pending' && !empty($leave['approved_by'])): ?>
                                <div class="mb-3">
                                    <h5 class="mb-3"><i class="fas fa-user-check"></i> Approval Information</h5>
                                    <p>
                                        <strong><?= ucfirst($leave['status']) ?> by:</strong> 
                                        <?= esc($leave['approver_first_name'] . ' ' . $leave['approver_last_name']) ?>
                                    </p>
                                    <p>
                                        <strong>Date:</strong> 
                                        <?= date('d M Y, h:i A', strtotime($leave['approved_at'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('leave-management/employee/' . $leave['employee_id']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Employee Leaves
                            </a>
                            
                            <!-- Always show Edit Status button -->
                            <button type="button" class="btn btn-warning" onclick="editStatus(<?= $leave['id'] ?>, '<?= $leave['status'] ?>')">
                                <i class="fas fa-edit"></i> Edit Status
                            </button>
                            
                            <a href="<?= base_url('leave-management/delete/' . $leave['id']) ?>" 
                               class="btn btn-danger float-right" 
                               onclick="return confirm('Delete this leave request?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Edit Status Modal (for changing approved/rejected leaves) -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Leave Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('', ['id' => 'editStatusForm']) ?>
            <div class="modal-body">
                
                <div class="form-group">
                    <label>Change Status To <span class="text-danger">*</span></label>
                    <select name="status" id="edit_status_value" class="form-control" required>
                        <!-- <option value="pending">Pending</option> -->
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Admin Remarks (Optional)</label>
                    <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Add remarks about this status change..."></textarea>
                </div>
                
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle"></i> 
                        The employee will be notified about this status change.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> Update Status
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
// For editing existing status (shows dropdown)
function editStatus(leaveId, currentStatus) {
    // Pre-select current status
    document.getElementById('edit_status_value').value = currentStatus;
    document.getElementById('editStatusForm').action = '<?= base_url('leave-management/update-status/') ?>' + leaveId;
    
    $('#editStatusModal').modal('show');
}

// Ensure modals close properly
$(document).ready(function() {
    $('.close, [data-dismiss="modal"]').on('click', function() {
        $('.modal').modal('hide');
    });
    
    // Clear forms when modals close
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });
});
</script>
<?= $this->endSection() ?>
