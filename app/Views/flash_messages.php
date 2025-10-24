<?php
// C:\xampp\htdocs\bhaviclients\app\Views\flash_messages.php
// This view centralizes the display of flash session messages (success, error, warning, info)
$session = session();
?>

<!-- Success Message -->
<?php if ($session->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-check-circle mr-2"></i>
        <?= $session->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Error Message -->
<?php if ($session->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-exclamation-triangle mr-2"></i>
        <?= $session->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Warning Message (For general issues/notices) -->
<?php if ($session->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-exclamation mr-2"></i>
        <?= $session->getFlashdata('warning') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Info Message -->
<?php if ($session->getFlashdata('info')): ?>
    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-info-circle mr-2"></i>
        <?= $session->getFlashdata('info') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
