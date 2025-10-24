<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\dashboard\index.php 
?>
<!-- Load Layout Template -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- The $title variable should be passed from the controller (Home::index) -->
                    <h1><?= esc($title ?? 'Client Management Dashboard') ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content: This is where your dashboard widgets and content will go -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info-circle"></i> Welcome!</h5>
                        This is the main dashboard area. You can start adding widgets and quick links here.
                    </div>
                </div>
            </div>
            
            <!-- Example Widget Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>150</h3>
                            <p>New Clients</p>
                        </div>
                        <div class="icon"><i class="ion ion-bag"></i></div>
                        <a href="<?= base_url('client') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- Add more widgets here -->
            </div>
            
        </div>
    </section>
    <!-- /.content -->
</div>

<?= $this->endSection() ?>
