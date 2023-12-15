<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload Main</title>
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
if(isset($_REQUEST['msg']) && ($_REQUEST['msg'] == "success"))
{
	echo '<div class="alert alert-success alert-dismissbale">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
	echo 'Document Successfully Uploaded!</div>';
}
echo '<div id="1" class="alert alert-info text-center default" onmouseover="addFocus(this.id)" onmouseout="removeFocus(this.id)">';
echo '<i class="fa fa-plus-circle fa-5x"></i><h3>Upload to a New Loan</h3>';
echo '<a class="btn btn-primary" href="upload_new.php">Upload New</a>';
echo '</div>';
echo '<div id="2" class="alert alert-info text-center default" onmouseover="addFocus(this.id)" onmouseout="removeFocus(this.id)">';
echo '<i class="fa fa-file-o fa-5x"></i><h3>Upload to an Existing Loan</h3>';
echo '<p><a class="btn btn-primary" href="upload_existing.php">Upload Existing Loan</a></p>';
echo '</div>';
echo '</div>';
echo '</div>';
?>
</body>
</html>
