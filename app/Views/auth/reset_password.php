<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | Clients Management</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="<?= base_url() ?>"><b>Bhavi</b>Clients</a>
        </div>
        
        <div class="card card-outline card-primary shadow-lg">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Enter your new password</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?= form_open('reset-password/update') ?>
                    <input type="hidden" name="token" value="<?= esc($token) ?>">
                    
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control <?= (session('validation') && session('validation')->hasError('password')) ? 'is-invalid' : '' ?>" 
                            placeholder="New Password" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <?php if (session('validation') && session('validation')->hasError('password')): ?>
                            <div class="invalid-feedback">
                                <?= session('validation')->getError('password') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="input-group mb-3">
                        <input type="password" name="confirm_password" class="form-control <?= (session('validation') && session('validation')->hasError('confirm_password')) ? 'is-invalid' : '' ?>" 
                            placeholder="Confirm Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <?php if (session('validation') && session('validation')->hasError('confirm_password')): ?>
                            <div class="invalid-feedback">
                                <?= session('validation')->getError('confirm_password') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                        </div>
                    </div>
                <?= form_close() ?>

                <p class="mt-3 mb-1">
                    <a href="<?= base_url('login') ?>">Back to Login</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
