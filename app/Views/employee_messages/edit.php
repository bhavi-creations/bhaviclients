<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h3>Edit Message</h3>
    <form method="post" action="<?= base_url('employee-messages/update/'.$msg['id']) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Department</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <?php foreach($departments as $dept): ?>
                    <option value="<?= $dept['id'] ?>" <?= $dept['id']==$msg['department_id']?'selected':'' ?>>
                        <?= esc($dept['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Employee</label>
            <select class="form-control" id="employee_id" name="employee_id" required>
                <?php foreach($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= $emp['id']==$msg['employee_id']?'selected':'' ?>>
                        <?= esc($emp['first_name'].' '.$emp['last_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea class="form-control" name="message" required rows="3"><?= esc($msg['message']) ?></textarea>
        </div>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
<?= $this->endSection() ?>
