<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title><?php echo $language["Dashboard"]; ?> | DProxy</title>

    <?php include 'layouts/head.php'; ?>
    
    <link href="assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    
    <?php include 'layouts/head-style.php'; ?>
</head>

<?php include 'layouts/body.php'; ?>

<!-- Begin page -->
<div id="layout-wrapper">

    <?php include 'layouts/menu.php'; ?>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">DProxy</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card border border-primary">
                            <div class="card-header bg-transparent border-primary">
                                <h5 class="my-0 text-primary"><i class="mdi mdi-bullseye-arrow me-3"></i>Total bandwidth per IP Address.</h5>
                            </div>
                            <form action="read-log/readTotalBandwidthPerIP.php" method="post">
                                <button class="btn btn-outline-danger mt-2">Read</button>
                            </form>
                            <div class="card-body">
                                <p id="total-bandwidth-per-ip" class="card-text">Loading...</p>
                            </div>
                        </div>
                    </div><!-- end col -->

                    <div class="col-lg-4">
                        <div class="card border border-danger">
                            <div class="card-header bg-transparent border-danger">
                                <h5 class="my-0 text-danger"><i class="mdi mdi-block-helper me-3"></i>Denied access per IP Address</h5>
                            </div>
                            <form action="read-log/readDenied.php" method="post">
                                <button class="btn btn-outline-danger mt-2">Read Denied</button>
                            </form>
                            <div class="card-body">
                                <p class="card-text">Loading...</p>
                            </div>
                        </div>
                    </div><!-- end col -->

                    <div class="col-lg-4">
                        <div class="card border border-success">
                            <div class="card-header bg-transparent border-success">
                                <h5 class="my-0 text-success"><i class="mdi mdi-check-all me-3"></i>Total bandwidth per Website</h5>
                            </div>
                            <form action="read-log/readTotalBandwidthPerWeb.php" method="post">
                                <button class="btn btn-outline-danger mt-2">Read</button>
                            </form>
                            <div class="card-body">
                                <p class="card-text">Loading...</p>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div>
                </div><!-- end row -->
                <!-- end row -->
            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <?php include 'layouts/footer.php'; ?>
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->

<!-- JAVASCRIPT -->
<?php include 'layouts/vendor-scripts.php'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
</script>
<script>
$(document).ready(function(){
    $.ajax({
        url: 'read-log/readTotalBandwidthPerIP.php',
        type: 'GET',
        success: function(data) {
            $('#total-bandwidth-per-ip').html(data);
        },
        error: function() {
            $('#total-bandwidth-per-ip').html("Có lỗi xảy ra khi tải dữ liệu.");
        }
    });
});
</script>

<!-- apexcharts -->
<script src="assets/libs/apexcharts/apexcharts.min.js"></script>

<!-- Plugins js-->
<script src="assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js"></script>

<!-- dashboard init -->
<script src="assets/js/pages/dashboard.init.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
</script>

</body>

</html>