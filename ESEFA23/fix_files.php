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
include("functions.php");
$dblink=db_connect("Files");
date_default_timezone_set('US/Central');

$sql="SELECT `d_id`,`file_name`
	  FROM `downloaded_files`";
$result=$dblink->query($sql) or 
	die("Something went wrong with: $sql<br>".$dblink->error);

while($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$dID = $data['d_id'];
	$fname = $data['file_name'];
	
	$metadata=explode("-",$fname);
	$loan_id=$metadata[0];
	$f_type=$metadata[1];
	$tmp=explode(".",$metadata[2]);
	$date_meta=explode("_",$tmp[0]);
	$date=''.substr($date_meta[0],0,4).'-'.substr($date_meta[0],4,2).'-'.substr($date_meta[0],6,2);
	$time="$date_meta[1]:$date_meta[2]:$date_meta[3]";
	$date_time="$date $time";
	
	$sql="SELECT `file_content`
	  	  FROM `file_contents`
		  WHERE `f_id`='$dID'";
	$rst=$dblink->query($sql) or 
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	$dta=$rst->fetch_array(MYSQLI_ASSOC);
	
	$file_size=strlen($dta['file_content']);
	
	$sql="UPDATE `file_records`
	  	  SET `file_name`='$fname',`file_size`='$file_size',`file_type`='$f_type',`date_created`='$date_time',`loan_number`='$loan_id'
		  WHERE `file_id`='$dID'";
	$r=$dblink->query($sql) or 
		die("Something went wrong with: $sql<br>".$dblink->error);
}
?>
</body>
</html>
