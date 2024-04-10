<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>

    <title>Editors | Minia - Admin & Dashboard Template</title>
    <?php include 'layouts/head.php'; ?>
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
                            <h4 class="mb-sm-0 font-size-18">Rules</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Rules</a></li>
                                    <li class="breadcrumb-item active">URL Block</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add, Edit, Delete URL Below</h4>
                            <p class="card-title-desc">1 Line Per URL</p>
                        </div>
                        <div class="card-body">
                            <form id="myForm" action="/submit-url" method="post">
                                <textarea name="editor" id="ckeditor-classic"></textarea>
                                <button class="btn btn-outline-success mt-2">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <?php include 'layouts/footer.php'; ?>
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->



<!-- JAVASCRIPT -->

<?php include 'layouts/vendor-scripts.php'; ?>

<!-- ckeditor -->
<script src="assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>

<!-- init js -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
</script>
<script>
    ClassicEditor
        .create(document.querySelector('#ckeditor-classic'))
        .then((editor) => {
            $.ajax({
                url: 'readBlockURL.php',
                type: 'get',
                success: function(response){
                    // Set initial data
                    editor.setData(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    editor.setData('Lỗi khi tải dữ liệu: ' + textStatus);
                }
            });
        })
        .catch((error) => {
            console.error(error);
        });
</script>
<script src="assets/js/app.js"></script>

</body>

</html>