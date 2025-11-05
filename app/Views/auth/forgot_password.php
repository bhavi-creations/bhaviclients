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
                <p class="login-box-msg">Enter your email to reset password</p>

                <?php if (session()->getFlashdata('message')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <?= session()->getFlashdata('message') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?= form_open('forgot-password/send') ?>
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control <?= (session('validation') && session('validation')->hasError('email')) ? 'is-invalid' : '' ?>" 
                            placeholder="Email" value="<?= old('email') ?>" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <?php if (session('validation') && session('validation')->hasError('email')): ?>
                            <div class="invalid-feedback">
                                <?= session('validation')->getError('email') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
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
