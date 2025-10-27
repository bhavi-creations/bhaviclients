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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    <!-- Profile Image Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" 
                                     src="https://ui-avatars.com/api/?name=<?= urlencode($user['first_name'] . ' ' . $user['last_name']) ?>&size=128&background=007bff&color=fff" 
                                     alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center">
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </h3>

                            <p class="text-muted text-center"><?= esc($user['role_name'] ?? 'N/A') ?></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Email</b> <a class="float-right"><?= esc($user['email']) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone</b> <a class="float-right"><?= esc($user['phone']) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Department</b> <a class="float-right"><?= esc($user['department_name'] ?? 'N/A') ?></a>
                                </li>
                            </ul>

                            <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="<?= base_url('profile/change-password') ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Profile Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Profile Details</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-user mr-1"></i> Full Name</strong>
                            <p class="text-muted">
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </p>
                            <hr>

                            <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                            <p class="text-muted"><?= esc($user['email']) ?></p>
                            <hr>

                            <strong><i class="fas fa-phone mr-1"></i> Phone</strong>
                            <p class="text-muted"><?= esc($user['phone']) ?></p>
                            <hr>

                            <strong><i class="fas fa-id-badge mr-1"></i> Username</strong>
                            <p class="text-muted"><?= esc($user['username']) ?></p>
                            <hr>

                            <strong><i class="fas fa-user-tag mr-1"></i> Role</strong>
                            <p class="text-muted"><?= esc($user['role_name'] ?? 'N/A') ?></p>
                            <hr>

                            <strong><i class="fas fa-building mr-1"></i> Department</strong>
                            <p class="text-muted"><?= esc($user['department_name'] ?? 'N/A') ?></p>
                            <hr>

                           
                            <strong><i class="fas fa-calendar mr-1"></i> Member Since</strong>
                            <p class="text-muted"><?= date('F d, Y', strtotime($user['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
