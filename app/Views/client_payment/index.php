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
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('message') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Client Info & Project Summary Card -->
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-building mr-2"></i>
                        <strong><?= esc($client['name']) ?></strong>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('client/view/' . $client['id']) ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-eye"></i> View Client
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Project Summary -->
                        <div class="col-md-8">
                            <h5 class="text-primary border-bottom pb-2">
                                <i class="fas fa-chart-line"></i> Project Financial Summary
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-rupee-sign"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Project Value</span>
                                            <span class="info-box-number">₹<?= number_format($summary['total_project_value'], 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Paid</span>
                                            <span class="info-box-number">₹<?= number_format($summary['total_paid'], 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Due</span>
                                            <span class="info-box-number">₹<?= number_format($summary['total_due'], 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Project Value -->
                        <div class="col-md-4">
                            <h5 class="text-primary border-bottom pb-2">
                                <i class="fas fa-edit"></i> Update Project Value
                            </h5>
                            <?= form_open('client-payment/update-project-value/' . $client['id']) ?>
                                <div class="form-group">
                                    <label>Project Value (₹)</label>
                                    <input type="number" 
                                           name="total_project_value" 
                                           class="form-control" 
                                           step="0.01" 
                                           value="<?= $summary['total_project_value'] ?>" 
                                           required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Update Value
                                </button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Payment History -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title text-white">
                                <i class="fas fa-history"></i> Payment History
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#addPaymentModal">
                                    <i class="fas fa-plus"></i> Add Payment
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($payments)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Ref</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($payments as $payment): ?>
                                                <tr>
                                                    <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                                    <td>
                                                        <span class="badge badge-<?= $payment['payment_type'] == 'advance' ? 'primary' : ($payment['payment_type'] == 'final' ? 'success' : 'info') ?>">
                                                            <?= ucfirst($payment['payment_type']) ?>
                                                        </span>
                                                    </td>
                                                    <td><strong>₹<?= number_format($payment['amount'], 2) ?></strong></td>
                                                    <td><?= esc($payment['payment_method'] ?? 'N/A') ?></td>
                                                    <td><small><?= esc($payment['transaction_reference'] ?? '-') ?></small></td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm" 
                                                                data-toggle="modal" 
                                                                data-target="#viewPaymentModal<?= $payment['id'] ?>"
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-warning btn-sm" 
                                                                data-toggle="modal" 
                                                                data-target="#editPaymentModal<?= $payment['id'] ?>"
                                                                title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <?= form_open('client-payment/delete-payment/' . $payment['id'], ['class' => 'd-inline', 'onsubmit' => "return confirm('Delete this payment?')"]) ?>
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?= form_close() ?>
                                                    </td>
                                                </tr>

                                                <!-- View Payment Modal -->
                                                <div class="modal fade" id="viewPaymentModal<?= $payment['id'] ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info">
                                                                <h4 class="modal-title text-white">Payment Details</h4>
                                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Type:</strong> <?= ucfirst($payment['payment_type']) ?></p>
                                                                <p><strong>Amount:</strong> ₹<?= number_format($payment['amount'], 2) ?></p>
                                                                <p><strong>Date:</strong> <?= date('F d, Y', strtotime($payment['payment_date'])) ?></p>
                                                                <p><strong>Method:</strong> <?= esc($payment['payment_method'] ?? 'Not specified') ?></p>
                                                                <p><strong>Reference:</strong> <?= esc($payment['transaction_reference'] ?? 'Not specified') ?></p>
                                                                <?php if (!empty($payment['remarks'])): ?>
                                                                    <p><strong>Remarks:</strong><br><?= nl2br(esc($payment['remarks'])) ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Edit Payment Modal -->
                                                <div class="modal fade" id="editPaymentModal<?= $payment['id'] ?>">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning">
                                                                <h4 class="modal-title">Edit Payment</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <?= form_open('client-payment/edit-payment/' . $payment['id']) ?>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Payment Type <span class="text-danger">*</span></label>
                                                                            <select name="payment_type" class="form-control" required>
                                                                                <option value="advance" <?= $payment['payment_type'] == 'advance' ? 'selected' : '' ?>>Advance</option>
                                                                                <option value="installment" <?= $payment['payment_type'] == 'installment' ? 'selected' : '' ?>>Installment</option>
                                                                                <option value="final" <?= $payment['payment_type'] == 'final' ? 'selected' : '' ?>>Final Payment</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Amount (₹) <span class="text-danger">*</span></label>
                                                                            <input type="number" name="amount" class="form-control" step="0.01" value="<?= $payment['amount'] ?>" required>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Payment Date <span class="text-danger">*</span></label>
                                                                            <input type="date" name="payment_date" class="form-control" value="<?= $payment['payment_date'] ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Payment Method</label>
                                                                            <select name="payment_method" class="form-control">
                                                                                <option value="">-- Select Method --</option>
                                                                                <option value="Cash" <?= $payment['payment_method'] == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                                                                <option value="UPI" <?= $payment['payment_method'] == 'UPI' ? 'selected' : '' ?>>UPI</option>
                                                                                <option value="Bank Transfer" <?= $payment['payment_method'] == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                                                                <option value="Cheque" <?= $payment['payment_method'] == 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                                                                                <option value="Card" <?= $payment['payment_method'] == 'Card' ? 'selected' : '' ?>>Card</option>
                                                                                <option value="Other" <?= $payment['payment_method'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Transaction Reference</label>
                                                                    <input type="text" name="transaction_reference" class="form-control" value="<?= esc($payment['transaction_reference'] ?? '') ?>" placeholder="Transaction ID / Reference Number">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <textarea name="remarks" class="form-control" rows="2"><?= esc($payment['remarks'] ?? '') ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-warning">
                                                                    <i class="fas fa-save"></i> Update Payment
                                                                </button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                            <?= form_close() ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No payments recorded yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Schedule -->
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt"></i> Payment Schedule
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#addScheduleModal">
                                    <i class="fas fa-plus"></i> Add Schedule
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($schedules)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($schedules as $schedule): ?>
                                                <tr>
                                                    <td><?= date('M d, Y', strtotime($schedule['expected_date'])) ?></td>
                                                    <td><strong>₹<?= number_format($schedule['expected_amount'], 2) ?></strong></td>
                                                    <td>
                                                        <?php
                                                        $statusColors = [
                                                            'pending' => 'warning',
                                                            'paid' => 'success',
                                                            'overdue' => 'danger',
                                                            'cancelled' => 'secondary'
                                                        ];
                                                        $badgeColor = $statusColors[$schedule['status']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge badge-<?= $badgeColor ?>">
                                                            <?= ucfirst($schedule['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($schedule['status'] != 'paid'): ?>
                                                            <button type="button" 
                                                                    class="btn btn-warning btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#editScheduleModal<?= $schedule['id'] ?>"
                                                                    title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <?= form_open('client-payment/delete-schedule/' . $schedule['id'], ['class' => 'd-inline', 'onsubmit' => "return confirm('Delete this schedule?')"]) ?>
                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?= form_close() ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                                <!-- Edit Schedule Modal -->
                                                <?php if ($schedule['status'] != 'paid'): ?>
                                                <div class="modal fade" id="editScheduleModal<?= $schedule['id'] ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning">
                                                                <h4 class="modal-title">Edit Payment Schedule</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <?= form_open('client-payment/edit-schedule/' . $schedule['id']) ?>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Expected Amount (₹) <span class="text-danger">*</span></label>
                                                                    <input type="number" name="expected_amount" class="form-control" step="0.01" value="<?= $schedule['expected_amount'] ?>" required>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Expected Date <span class="text-danger">*</span></label>
                                                                    <input type="date" name="expected_date" class="form-control" value="<?= $schedule['expected_date'] ?>" required>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Status <span class="text-danger">*</span></label>
                                                                    <select name="status" class="form-control" required>
                                                                        <option value="pending" <?= $schedule['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                        <option value="overdue" <?= $schedule['status'] == 'overdue' ? 'selected' : '' ?>>Overdue</option>
                                                                        <option value="cancelled" <?= $schedule['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                                    </select>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <textarea name="remarks" class="form-control" rows="2"><?= esc($schedule['remarks'] ?? '') ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-warning">
                                                                    <i class="fas fa-save"></i> Update Schedule
                                                                </button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                            <?= form_close() ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>

                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>No payment schedules</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('client') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Clients
            </a>

        </div>
    </section>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">Add Payment</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open('client-payment/add-payment/' . $client['id']) ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Type <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-control" required>
                                <option value="">-- Select Type --</option>
                                <option value="advance">Advance</option>
                                <option value="installment">Installment</option>
                                <option value="final">Final Payment</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="">-- Select Method --</option>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Card">Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Transaction Reference</label>
                    <input type="text" name="transaction_reference" class="form-control" placeholder="Transaction ID / Reference Number">
                </div>

                <div class="form-group">
                    <label>Link to Schedule (Optional)</label>
                    <select name="schedule_id" class="form-control">
                        <option value="">-- No Link --</option>
                        <?php foreach ($schedules as $schedule): ?>
                            <?php if ($schedule['status'] == 'pending' || $schedule['status'] == 'overdue'): ?>
                                <option value="<?= $schedule['id'] ?>">
                                    <?= date('M d, Y', strtotime($schedule['expected_date'])) ?> - ₹<?= number_format($schedule['expected_amount'], 2) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Payment
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Add Payment Schedule</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open('client-payment/add-schedule/' . $client['id']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label>Expected Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="expected_amount" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Expected Date <span class="text-danger">*</span></label>
                    <input type="date" name="expected_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> Save Schedule
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
