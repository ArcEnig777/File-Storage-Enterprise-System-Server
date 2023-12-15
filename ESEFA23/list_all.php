<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>List All Files</title>
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
<!--- JQUERY SCRIPTS --->
<script src= https://code.jquery.com/jquery-3.5.1.js></script>
<!--- BOOTSTRAP SCRIPTS --->
<script src= https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js></script>
<script src= https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<script>
$(document).ready(function () {
    $('#results').DataTable();
});
</script>
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
echo '<div id="page-inner">';
echo '<h1 class="page-head-line">List All Files</h1>';
echo '<div class="panel-body">';
if(isset($_REQUEST['err']) && ($_REQUEST['err'] == "error_query"))
{
	echo '<div class="alert alert-danger alert-dismissbale">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
	echo 'ERROR: Query for list_all has failed, please contact server admin and try again later</div>';
}
$_REQUEST = array();
	
$time_start=microtime(true);

$sql="SELECT `file_id`,`file_name`,`file_size`,`file_type`,`loan_number`,`date_created`
	  FROM `file_records_final`";
$result=$dblink->query($sql);
if (strlen($dblink->error)>0)
{
	$sql = "INSERT INTO `errors`
			(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
			VALUES ('ERROR','$dblink->error','Try again',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'list_all')";
	$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);

	redirect("https://ec2-3-15-170-107.us-east-2.compute.amazonaws.com/list_all.php?err=error_query");
}
	
	echo '<h3>List All Files:</h3>';
	echo '<table id="results" class="display" cellspacing="0" width="100%">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>Auto_ID</th>';
	echo '<th>Loan Number</th>';
	echo '<th>File Name</th>';
	echo '<th>File Size</th>';
	echo '<th>File Type</th>';
	echo '<th>Date Created</th>';
	echo '<th>Content</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	while($data=$result->fetch_array(MYSQLI_ASSOC))
	{
		echo '<tr>';
		echo '<td>'.$data['file_id'].'</td>';
		echo '<td>'.$data['loan_number'].'</td>';
		echo '<td>'.$data['file_name'].'</td>';
		echo '<td>'.$data['file_size'].' B</td>';
		echo '<td>'.$data['file_type'].'</td>';
		echo '<td>'.$data['date_created'].'</td>';
		echo '<td><a href="https://ec2-3-15-170-107.us-east-2.compute.amazonaws.com/view_file.php?
		fid='.$data['file_id'].'" target="_blaank">View</a></td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
	$time_end=microtime(true);
	$execution_time=($time_end-$time_start)/60;
	
	$t=time();
	$g_date=date("Y-m-d H:i:s",$t);
	
	$sql = "INSERT INTO `calls`
			(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
			VALUES ('list_all','$execution_time','$g_date','1')";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	

?>
</body>
</html>
