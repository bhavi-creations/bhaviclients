<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\task_management\index.php 
$request = \Config\Services::request();
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <!-- Filters Card -->
            <div class="card card-primary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Filters</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?= form_open(base_url('task-management'), ['method' => 'get']) ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Employee</label>
                                <select name="employee_id" class="form-control select2">
                                    <option value="">All Employees</option>
                                    <?php foreach ($employees as $emp): ?>
                                        <option value="<?= $emp['id'] ?>" <?= ($request->getGet('employee_id') == $emp['id']) ? 'selected' : '' ?>>
                                            <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department_id" class="form-control">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>" <?= ($request->getGet('department_id') == $dept['id']) ? 'selected' : '' ?>>
                                            <?= esc($dept['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Client</label>
                                <select name="client_id" class="form-control">
                                    <option value="">All Clients</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>" <?= ($request->getGet('client_id') == $client['id']) ? 'selected' : '' ?>>
                                            <?= esc($client['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="Pending" <?= ($request->getGet('status') == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= ($request->getGet('status') == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= ($request->getGet('status') == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="Review" <?= ($request->getGet('status') == 'Review') ? 'selected' : '' ?>>Review</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="">All Priority</option>
                                    <option value="Low" <?= ($request->getGet('priority') == 'Low') ? 'selected' : '' ?>>Low</option>
                                    <option value="Medium" <?= ($request->getGet('priority') == 'Medium') ? 'selected' : '' ?>>Medium</option>
                                    <option value="High" <?= ($request->getGet('priority') == 'High') ? 'selected' : '' ?>>High</option>
                                    <option value="Urgent" <?= ($request->getGet('priority') == 'Urgent') ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control" value="<?= $request->getGet('from_date') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" value="<?= $request->getGet('to_date') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="<?= base_url('task-management') ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="card shadow-lg">
                <div class="card-header border-0">
                    <h3 class="card-title">All Tasks</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('task-management/create') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle"></i> Assign New Task
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Task Title</th>
                                    <th>Employee</th>
                                    <th>Client</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tasks)): ?>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td><strong><?= esc($task['title']) ?></strong></td>
                                            <td><?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></td>
                                            <td><?= esc($task['client_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                $priorityBadge = [
                                                    'Low' => 'secondary',
                                                    'Medium' => 'info',
                                                    'High' => 'warning',
                                                    'Urgent' => 'danger'
                                                ];
                                                $badge = $priorityBadge[$task['priority']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= esc($task['priority']) ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($task['due_date'])): ?>
                                                    <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No deadline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadge = [
                                                    'Pending' => 'warning',
                                                    'In Progress' => 'info',
                                                    'Completed' => 'success',
                                                    'Review' => 'primary'
                                                ];
                                                $badge = $statusBadge[$task['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= esc($task['status']) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('task-management/view/' . $task['id']) ?>" 
                                                   class="btn btn-sm btn-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('task-management/edit/' . $task['id']) ?>" 
                                                   class="btn btn-sm btn-info" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" onclick="confirmDelete(<?= $task['id'] ?>)" 
                                                   class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center py-4">No tasks found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                   
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                   
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteForm').action = '<?= base_url("task-management/delete/") ?>' + id;
    $('#deleteModal').modal('show');
}

$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
});
</script>

<?= $this->endSection() ?>
