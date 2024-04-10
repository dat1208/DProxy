<header id="page-topbar">
</script>
</script>
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="index.php" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.svg" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-sm.svg" alt="" height="24"> <span class="logo-txt">DProxy</span>
                    </span>
                </a>

                <a href="index.php" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.svg" alt="" height="24">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-sm.svg" alt="" height="24"> <span class="logo-txt">DProxy</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
            <div>
                
            </div>
                            <!-- App Header -->

            <div class="d-flex mt-2">
             <!-- Start Service -->
            <div class="d-flex flex-wrap gap-2 mx-1">
                <form action="startCommand.php" method="post">
                    <button class="btn btn-success waves-effect btn-label waves-light">
                    <i class="bx bx-play label-icon"></i> Start Service
                    </button>
                </form>
            </div>
             <!-- Restart Service -->
            
            <div class="d-flex flex-wrap gap-2 mx-1">
                <form action="restartCommand.php" method="post">
                    <button class="btn btn-primary waves-effect btn-label waves-light">
                    <i class="bx bx-reset label-icon"></i> Restart Service
                    </button>
                </form>
                
            </div>  
             <!-- Stop Service --> 
             <div class="d-flex flex-wrap gap-2 mx-1">
                <form action="restartCommand.php" method="post">
                    <button class="btn btn-danger waves-effect btn-label waves-light">
                    <i class="bx bx-block label-icon"></i> Stop Service
                    </button>
                </form>
                
            </div>  
             
            </div>          
        </div>

        <div class="d-flex">   
        <div class="app-search d-none d-lg-block mx-2">
            <div class="position-relative">
                <?php $status = shell_exec('sudo systemctl is-active squid'); ?>
                <input type="text" class="form-control" placeholder="<?php echo $status; ?>">
            </div>
            </div>
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item" id="page-header-search-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i data-feather="search" class="icon-lg"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="<?php echo $language["Search"]; ?>" aria-label="Search Result">

                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="https://static.vecteezy.com/system/resources/previews/011/490/381/non_2x/happy-smiling-young-man-avatar-3d-portrait-of-a-man-cartoon-character-people-illustration-isolated-on-white-background-vector.jpg"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">Đạt.</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="logout.php"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> <?php echo $language["Logout"]; ?></a>
                </div>
            </div>

        </div>
        
    </div>
</header>

<!-- ========== Left Sidebar Start ========== -->
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu"><?php echo $language["Menu"]; ?></li>

                <li>
                    <a href="index.php">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard"><?php echo $language["Dashboard"]; ?></span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="grid"></i>
                        <span data-key="t-apps">Proxy</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="proxy-list.php">
                                <span data-key="t-calendar">List</span>
                            </a>
                        </li>
        
                        <li>
                            <a href="proxy-configfile.php">
                                <span data-key="t-chat">Config File</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="lock"></i>
                        <span data-key="t-authentication">Rules</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="rules-blockurl.php" data-key="t-login">Block URL</a></li>
                    
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="lock"></i>
                        <span data-key="t-authentication">Linux Config</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="rules-blockurl.php" data-key="t-login">System</a></li>
                    
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->