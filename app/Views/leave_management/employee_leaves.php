<?php
// C:\xampp\htdocs\bhaviclients\app\Views\leave_management\employee_leaves.php
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
                        <li class="breadcrumb-item active"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <!-- Employee Info -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user"></i> <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Employee Code:</strong>
                            <p><?= esc($employee['employee_code']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Department:</strong>
                            <p><?= esc($employee['department_name'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Leaves:</strong>
                            <p><span class="badge badge-info badge-lg"><?= count($leaves) ?></span></p>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('leave-management') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Requests -->
            <?php if (!empty($leaves)): ?>
                <div class="row">
                    <?php
                    $leaveTypes = LeaveRequestModel::getLeaveTypes();
                    foreach ($leaves as $leave):
                    ?>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header <?= $leave['status'] == 'pending' ? 'bg-warning' : ($leave['status'] == 'approved' ? 'bg-success' : 'bg-danger') ?>">
                                    <h3 class="card-title text-white">
                                        <i class="fas fa-calendar"></i>
                                        <strong><?= esc($leaveTypes[$leave['leave_type']]) ?></strong>
                                    </h3>
                                    <div class="card-tools">
                                        <span class="badge badge-dark"><?= ucfirst($leave['status']) ?></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Duration:</strong><br>
                                        <i class="fas fa-calendar-day"></i>
                                        <?= date('d M Y', strtotime($leave['start_date'])) ?>
                                        <strong>to</strong>
                                        <?= date('d M Y', strtotime($leave['end_date'])) ?>
                                        <br>
                                        <span class="badge badge-info mt-1"><?= $leave['total_days'] ?> day(s)</span>
                                    </div>

                                    <div class="mb-2">
                                        <strong>Reason:</strong><br>
                                        <small><?= esc($leave['reason']) ?></small>
                                    </div>

                                    <?php if (!empty($leave['admin_remarks'])): ?>
                                        <div class="alert alert-light mb-2">
                                            <strong>Admin Remarks:</strong><br>
                                            <small><?= esc($leave['admin_remarks']) ?></small>
                                        </div>
                                    <?php endif; ?>

                                    <p class="text-muted mb-0">
                                        <small>
                                            <i class="fas fa-clock"></i>
                                            Applied: <?= date('d M Y, h:i A', strtotime($leave['created_at'])) ?>
                                        </small>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group d-flex" role="group">
                                        <a href="<?= base_url('leave-management/view/' . $leave['id']) ?>"
                                            class="btn btn-info btn-sm flex-fill">
                                            <i class="fas fa-eye"></i> View
                                        </a>

                                        <!-- Show Edit Status button for ALL statuses -->
                                        <button type="button"
                                            class="btn btn-warning btn-sm flex-fill"
                                            onclick="editStatus(<?= $leave['id'] ?>, '<?= $leave['status'] ?>')">
                                            <i class="fas fa-edit"></i> Edit Status
                                        </button>

                                        <a href="<?= base_url('leave-management/delete/' . $leave['id']) ?>"
                                            class="btn btn-danger btn-sm flex-fill"
                                            onclick="return confirm('Delete this leave request?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
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
                            <h5>No Leave Requests</h5>
                            <p>This employee hasn't applied for any leaves yet</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h4 class="modal-title"><i class="fas fa-edit"></i> Update Leave Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('', ['id' => 'statusForm']) ?>
            <div class="modal-body">
                <input type="hidden" name="status" id="status_value">

                <div class="form-group">
                    <label>Admin Remarks (Optional)</label>
                    <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Add remarks about this decision..."></textarea>
                </div>

                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        The employee will be notified about this status change.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                    <i class="fas fa-save"></i> Update Status
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
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
    // For changing status (shows Approve/Reject buttons)
    function updateStatus(leaveId, status) {
        document.getElementById('status_value').value = status;
        document.getElementById('statusForm').action = '<?= base_url('leave-management/update-status/') ?>' + leaveId;

        const header = document.getElementById('modalHeader');
        const submitBtn = document.getElementById('modalSubmitBtn');

        if (status === 'approved') {
            header.className = 'modal-header bg-success';
            submitBtn.className = 'btn btn-success';
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Approve Leave';
        } else {
            header.className = 'modal-header bg-danger';
            submitBtn.className = 'btn btn-danger';
            submitBtn.innerHTML = '<i class="fas fa-times"></i> Reject Leave';
        }

        $('#statusModal').modal('show');
    }

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
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    });
</script>
<?= $this->endSection() ?>