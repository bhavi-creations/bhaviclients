<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><?= esc($title) ?></h3>
        <a href="<?= base_url('employee-messages/create') ?>" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Send Message to Employee
        </a>
    </div>

    <form class="form-inline mb-3" method="get" action="<?= base_url('employee-messages/list') ?>" id="filterForm">
        <label for="department_id">Department&nbsp;</label>
        <select name="department_id" id="department_id" class="form-control mr-2">
            <option value="">All</option>
            <?php foreach($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>" <?= ($departmentId==$dept['id'])?'selected':'' ?>><?= esc($dept['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="employee_id">Employee&nbsp;</label>
        <select name="employee_id" id="employee_id" class="form-control mr-2">
            <option value="">All</option>
            <?php foreach($employees as $emp): ?>
                <option value="<?= $emp['id'] ?>" <?= ($employeeId==$emp['id'])?'selected':'' ?>>
                    <?= esc($emp['first_name'].' '.$emp['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="from_date">From&nbsp;</label>
        <input type="date" class="form-control mr-2" name="from_date" id="from_date" value="<?= esc($fromDate ?? '') ?>">
        <label for="to_date">To&nbsp;</label>
        <input type="date" class="form-control mr-2" name="to_date" id="to_date" value="<?= esc($toDate ?? '') ?>">
        <button type="button" class="btn btn-secondary ml-2" onclick="window.location.href='<?= base_url('employee-messages/list') ?>'">Reset</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Department</th>
                <th>Employee</th>
                <th>Message</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($messages as $msg): ?>
                <tr>
                    <td><?= esc($msg['department_name']) ?></td>
                    <td><?= esc($msg['first_name'].' '.$msg['last_name']) ?></td>
                    <td><?= esc($msg['message']) ?></td>
                    <td><small><?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?></small></td>
                    <td>
                        <a href="<?= base_url('employee-messages/edit/'.$msg['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="<?= base_url('employee-messages/delete/'.$msg['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
['department_id', 'employee_id', 'from_date', 'to_date'].forEach(function(id){
    document.getElementById(id).addEventListener('change', function(){
        document.getElementById('filterForm').submit();
    });
});
</script>
<?= $this->endSection() ?>
