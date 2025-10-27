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
                        <li class="breadcrumb-item"><a href="<?= base_url('manager-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Work Updates</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Filter Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filter Work Updates
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('manager/work-updates') ?>" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Client</label>
                                    <select class="form-control live-filter" name="client_id">
                                        <option value="">-- All Clients --</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>" 
                                                <?= (isset($_GET['client_id']) && $_GET['client_id'] == $client['id']) ? 'selected' : '' ?>>
                                                <?= esc($client['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control live-filter" name="status">
                                        <option value="">-- All Status --</option>
                                        <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="In Progress" <?= (isset($_GET['status']) && $_GET['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                        <option value="Completed" <?= (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                        <option value="Review" <?= (isset($_GET['status']) && $_GET['status'] == 'Review') ? 'selected' : '' ?>>Review</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" 
                                           class="form-control live-filter" 
                                           name="from_date"
                                           value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" 
                                           class="form-control live-filter" 
                                           name="to_date"
                                           value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <a href="<?= base_url('manager/work-updates') ?>" class="btn btn-secondary btn-block">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Work Updates Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Client Work Updates</h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= count($tasks) ?> Updates</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">S.No</th>
                                        <th width="15%">Client</th>
                                        <th width="20%">Work Title</th>
                                        <th width="15%">Employee</th>
                                        <th width="12%">Department</th>
                                        <th width="10%">Status</th>
                                        <th width="13%">Date</th>
                                        <th width="10%">Files</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><?= esc($task['client_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($task['title']) ?></td>
                                            <td><?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></td>
                                            <td><?= esc($task['department_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'Pending' => 'warning',
                                                    'In Progress' => 'info',
                                                    'Completed' => 'success',
                                                    'Review' => 'primary'
                                                ];
                                                $class = $statusClass[$task['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $class ?>">
                                                    <?= esc($task['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <?= date('M d, Y', strtotime($task['submitted_at'])) ?><br>
                                                    <?= date('h:i A', strtotime($task['submitted_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if (!empty($task['files_upload'])): ?>
                                                    <?php $files = json_decode($task['files_upload'], true); ?>
                                                    <?php if (is_array($files) && count($files) > 0): ?>
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-paperclip"></i> <?= count($files) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No work updates found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Live filtering
document.querySelectorAll('.live-filter').forEach(function(element) {
    element.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>

<?= $this->endSection() ?>
