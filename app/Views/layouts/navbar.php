<!-- Navbar -->
<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!-- Start navbar links (Left Side) -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <!-- You can uncomment these if you need them later
            <li class="nav-item d-none d-md-block">
                <a href="<?= base_url('/') ?>" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="#" class="nav-link">Contact</a>
            </li> -->
        </ul>
        <!-- End navbar links -->

        <!-- START MARQUEE FOR CENTER ALIGNMENT -->
        <!-- <div class="d-none d-sm-block mx-auto flex-grow-1" style="max-width: 60%;">
            <marquee behavior="scroll" direction="left" scrollamount="6" style="color: #007bff; font-weight: 600; padding: 5px 0;">
                **HOSPITAL ANNOUNCEMENT:** Our new digital patient record system is now live! Please report any issues to IT immediately. Thank you for your cooperation.
            </marquee>
        </div> -->
        <!-- END MARQUEE -->

        <!-- Right Side: User Menu and Icons -->
        <ul class="navbar-nav ms-auto">
            <!-- Navbar Search (Commented out) -->
            <!-- <li class="nav-item">...</li> -->

            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <!-- Placeholder for user avatar or icon if needed -->
                    <i class="bi bi-person-circle"></i> 
                    <!-- <span class="d-none d-md-inline">Alexander Pierce</span> -->
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!-- User image -->
                    <li class="user-header text-bg-primary">
                        <p>
                             User Name - Role
                             <small>Member since Nov. 2023</small>
                        </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body">
                        <!--begin::Row-->
                        <div class="row">
                            <div class="col-4 text-center">
                                <a href="#">Followers</a>
                            </div>
                            <div class="col-4 text-center">
                                <a href="#">Sales</a>
                            </div>
                            <div class="col-4 text-center">
                                <a href="#">Friends</a>
                            </div>
                        </div>
                        <!--end::Row-->
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                        <a href="#" class="btn btn-default btn-flat float-end">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <!--end::Container-->
</nav>
<!-- /.navbar -->
