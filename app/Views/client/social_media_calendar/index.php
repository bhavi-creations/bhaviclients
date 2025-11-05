<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client\social_media_calendar\index.php
use App\Models\SocialMediaCalendarModel;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-alt"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <!-- Info Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> About Social Media Calendars</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-0">
                                <i class="fas fa-share-alt"></i> 
                                Social media calendars uploaded by our team for 
                                <strong><?= esc($client['name']) ?></strong>. Download the calendars to view your upcoming posts and campaigns.
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <span class="badge badge-primary badge-lg p-3">
                                <i class="fas fa-calendar-check"></i> 
                                <?= count($calendars) ?> Calendar(s) Available
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendars in Card Format -->   
            <?php if (!empty($calendars)): ?>
                <div class="row">
                    <?php foreach ($calendars as $calendar): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-gradient-info">
                                    <h3 class="card-title text-white">
                                        <i class="fas fa-calendar-day"></i>
                                        <strong><?= SocialMediaCalendarModel::getMonthName($calendar['calendar_month']) ?> <?= $calendar['calendar_year'] ?></strong>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- File Icon -->
                                    <div class="text-center mb-3">
                                        <?php
                                        $ext = strtolower($calendar['file_extension']);
                                        $iconClass = 'fa-file';
                                        $iconColor = 'text-secondary';
                                        
                                        if ($ext == 'pdf') {
                                            $iconClass = 'fa-file-pdf';
                                            $iconColor = 'text-danger';
                                        } elseif (in_array($ext, ['doc', 'docx'])) {
                                            $iconClass = 'fa-file-word';
                                            $iconColor = 'text-primary';
                                        } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                            $iconClass = 'fa-file-excel';
                                            $iconColor = 'text-success';
                                        } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                            $iconClass = 'fa-file-image';
                                            $iconColor = 'text-info';
                                        }
                                        ?>
                                        <i class="fas <?= $iconClass ?> <?= $iconColor ?> fa-4x"></i>
                                    </div>
                                    
                                    <!-- File Name -->
                                    <h5 class="text-center mb-3"><?= esc($calendar['original_name']) ?></h5>
                                    
                                    <!-- File Size -->
                                    <p class="text-center text-muted mb-3">
                                        <i class="fas fa-hdd"></i> <?= number_format($calendar['file_size'] / 1024, 2) ?> KB
                                    </p>
                                    
                                    <!-- Remarks -->
                                    <?php if (!empty($calendar['remarks'])): ?>
                                        <div class="alert alert-light border mb-3">
                                            <strong><i class="fas fa-sticky-note"></i> Remarks:</strong><br>
                                            <small><?= nl2br(esc($calendar['remarks'])) ?></small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Upload Date -->
                                    <p class="text-muted text-center mb-0">
                                        <small>
                                            <i class="fas fa-clock"></i> 
                                            Uploaded: <?= date('d M Y', strtotime($calendar['uploaded_at'])) ?>
                                        </small>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group-vertical d-flex" role="group">
                                        <!-- View Button -->
                                        <a href="<?= base_url('my-social-media-calendar/view/' . $calendar['id']) ?>" 
                                           class="btn btn-info btn-sm"
                                           target="_blank"
                                           title="View Calendar">
                                            <i class="fas fa-eye"></i> View Calendar
                                        </a>
                                        
                                        <!-- Download Button -->
                                        <a href="<?= base_url('my-social-media-calendar/download/' . $calendar['id']) ?>" 
                                           class="btn btn-primary btn-sm mt-1" 
                                           title="Download Calendar">
                                            <i class="fas fa-download"></i> Download Calendar
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
                            <i class="fas fa-calendar-times fa-4x mb-3 text-info"></i>
                            <h5>No Social Media Calendars Available</h5>
                            <p>Calendars uploaded by our team will appear here</p>
                            <small class="text-muted">
                                Contact our team if you need assistance with your social media calendar.
                            </small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
