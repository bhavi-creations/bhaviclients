<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container mt-4">
    <h3><?= esc($title) ?></h3>
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3"><?= esc($report['title']) ?></h5>

            <div class="mb-3">
                <strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($report['created_at'])) ?>
            </div>
            <div class="mb-4"><?= nl2br(esc($report['remarks'])) ?></div>

            <?php if (!empty($files)): ?>
                <div class="mb-4">
                    <strong>Uploaded Files:</strong>
                    <div class="row">
                        <?php foreach ($files as $file):
                            $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $iconClass = 'fa-file';
                            $iconColor = 'text-secondary';
                            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $iconClass = 'fa-file-image';
                                $iconColor = 'text-info';
                            } elseif ($fileExt == 'pdf') {
                                $iconClass = 'fa-file-pdf';
                                $iconColor = 'text-danger';
                            } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                $iconClass = 'fa-file-word';
                                $iconColor = 'text-primary';
                            } elseif (in_array($fileExt, ['xls', 'xlsx'])) {
                                $iconClass = 'fa-file-excel';
                                $iconColor = 'text-success';
                            }
                            $viewUrl = base_url('uploads/client_reports/' . $file);
                            $downloadUrl = base_url('client/reports/download-file/' . $report['id'] . '/' . $file);
                        ?>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <a href="<?= $viewUrl ?>" target="_blank" title="Open/View">
                                            <i class="fas <?= $iconClass ?> fa-3x <?= $iconColor ?> mb-2"></i>
                                        </a>
                                        <div class="mb-2 text-truncate" title="<?= esc($file) ?>">
                                            <a href="<?= $viewUrl ?>" target="_blank"><?= esc(strlen($file) > 20 ? substr($file, 0, 20) . '...' : $file) ?></a>
                                        </div>
                                        <a href="<?= $downloadUrl ?>" class="btn btn-sm btn-primary btn-block mt-2">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="<?= base_url('client/reports') ?>" class="btn btn-secondary mt-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
