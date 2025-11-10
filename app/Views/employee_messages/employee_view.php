<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h3><?= esc($title) ?></h3>
    
    <form class="form-inline mb-3" method="get" action="<?= base_url('employee-messages/my-messages') ?>" id="filterForm">
        <label for="from_date">From&nbsp;</label>
        <input type="date" class="form-control mr-2" name="from_date" id="from_date" value="<?= esc($fromDate ?? '') ?>">
        <label for="to_date">To&nbsp;</label>
        <input type="date" class="form-control mr-2" name="to_date" id="to_date" value="<?= esc($toDate ?? '') ?>">
        <button type="button" class="btn btn-secondary ml-2" onclick="window.location.href='<?= base_url('employee-messages/my-messages') ?>'">Reset</button>
    </form>
    
    <div class="card">
        <div class="card-body">
            <?php if ($messages): ?>
                <?php foreach($messages as $msg): ?>
                    <div class="mb-4 pb-2 border-bottom">
                        <div>
                            <strong>Message:</strong> <?= esc($msg['message']) ?>
                        </div>
                        <div>
                            <small class="text-muted"><i class="far fa-clock"></i> <?= date('d M Y, h:i A', strtotime($msg['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-muted">No messages yet!</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
['from_date','to_date'].forEach(function(id){
    document.getElementById(id).addEventListener('change', function(){
        document.getElementById('filterForm').submit();
    });
});
</script>
<?= $this->endSection() ?>
