<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h3><?= esc($title) ?></h3>
    <form method="post" action="<?= base_url('employee-messages/store') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="department_id">Department</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <option value="">Select Department</option>
                <?php foreach($departments as $dept): ?>
                    <option value="<?= $dept['id'] ?>"><?= esc($dept['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="employee_id">Employee</label>
            <select class="form-control" id="employee_id" name="employee_id" required>
                <option value="">Select Employee</option>
                <?php foreach($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>" data-dept="<?= $emp['department_id'] ?>">
                        <?= esc($emp['first_name'].' '.$emp['last_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" name="message" id="message" required rows="3"></textarea>
        </div>
        <button class="btn btn-primary">Send</button>
    </form>
</div>

<!-- Live filter for employees on department change -->
<script>
document.getElementById('department_id').addEventListener('change', function() {
    var deptId = this.value;
    var empSelect = document.getElementById('employee_id');
    for (var i = 0; i < empSelect.options.length; i++) {
        var opt = empSelect.options[i];
        if (opt.value === '') { opt.style.display = ''; continue; }
        opt.style.display = (opt.getAttribute('data-dept') == deptId) ? '' : 'none';
    }
    empSelect.value = '';
});
</script>
<?= $this->endSection() ?>
