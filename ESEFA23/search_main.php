<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search Main</title>
<!-- BOOTSTRAP STYLES-->
<link href="assets/css/bootstrap.css" rel="stylesheet" />
<!-- FONTAWESOME STYLES-->
<link href="assets/css/font-awesome.css" rel="stylesheet" />
   <!--CUSTOM BASIC STYLES-->
<link href="assets/css/basic.css" rel="stylesheet" />
<!--CUSTOM MAIN STYLES-->
<link href="assets/css/custom.css" rel="stylesheet" />
<!-- PAGE LEVEL STYLES -->
<link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
<!-- PAGE LEVEL STYLES -->
<link href="assets/css/prettyPhoto.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="assets/css/print.css" media="print" />
<!--[if lt IE 9]><script src="scripts/flashcanvas.js"></script><![endif]-->
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<!-- METISMENU SCRIPTS -->
<script src="assets/js/jquery.metisMenu.js"></script>
   <!-- CUSTOM SCRIPTS <script src="assets/js/custom.js"></script>-->
<script src="assets/js/bootstrap-fileupload.js"></script>

<script src="assets/js/jquery.prettyPhoto.js"></script>
<script src="assets/js/galleryCustom.js"></script>
<style>
    .default {background-color:#E1E1E1;}
</style>
<script>
    function addFocus(div){
        document.getElementById(div).classList.remove("default");
    }
    function removeFocus(div){
        document.getElementById(div).classList.add("default");
    }
</script>
</head>
<body>
<?php
date_default_timezone_set('US/Central');
echo '<div id="page-inner">';
echo '<h1 class="page-head-line">Select the type to Upload</h1>';
echo '<div class="panel-body">';
echo '<div id="1" class="alert alert-info text-center default" onmouseover="addFocus(this.id)" onmouseout="removeFocus(this.id)">';
echo '<i class="fa fa-plus-circle fa-5x"></i><h3>Search by Loan Number</h3>';
echo '<a class="btn btn-primary" href="search_loan_number.php">Search by Loan Number</a>';
echo '</div>';
echo '<div id="2" class="alert alert-info text-center default" onmouseover="addFocus(this.id)" onmouseout="removeFocus(this.id)">';
echo '<i class="fa fa-file-o fa-5x"></i><h3>Search by File Type</h3>';
echo '<p><a class="btn btn-primary" href="search_file_type.php">Search by File Type</a></p>';
echo '</div>';
echo '<div id="3" class="alert alert-info text-center default" onmouseover="addFocus(this.id)" onmouseout="removeFocus(this.id)">';
echo '<i class="fa fa-file-o fa-5x"></i><h3>Search by File Date</h3>';
echo '<p><a class="btn btn-primary" href="search_file_date.php">Search by File Date</a></p>';
echo '</div>';
echo '<div id="4" class="alert alert-info text-center default" onmouseover="addFocus(this.id)" onmouseout="removeFocus(this.id)">';
echo '<i class="fa fa-file-o fa-5x"></i><h3>List All Files</h3>';
echo '<p><a class="btn btn-primary" href="list_all.php">List All Files</a></p>';
echo '</div>';
echo '</div>';
echo '</div>';
?>
</body>
</html>
