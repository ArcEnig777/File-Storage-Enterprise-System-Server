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

$sql="SELECT `loan_id`
	  FROM `loans_final`";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
	
$unique_id = $result->num_rows;

$sql="SELECT `file_size`
	  FROM `file_records_final`
	  WHERE `date_created` >= '2023-11-01 00:00:00' AND `date_created` < '2023-11-20 00:00:00'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);

$total_files = $result->num_rows;

$total_size = 0;
	
while($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$total_size += $data['file_size'];
}
echo '<p><b>Name: </b> Martin Sanabia</p>';
echo '<p><b>abc123: </b> say526</p>';
echo '<h3><b>Report From: </b>11/1/2023 12AM to 11/19/2023 11:59 PM</h3>';
	
echo '<h3><b>Total Size of All Documents:</b> '.$total_size.' Bytes</h3>';
	
$average_size_loans = $total_size/$unique_id;

echo '<h3><b>Average Size of All Documents Across All Loans:</b> '.$average_size_loans.' Bytes</h3>';
	
echo '<h3><b>Total Number of Documents:</b> '.$total_files.' Files</h3>';
	
$average_files_loans = $total_files/$unique_id;

echo '<h3><b>Average Number of Documents Across All Loans:</b> '.$average_files_loans.' Files</h3>';

$sql="SELECT `loan_id`
	  FROM `loans_final`";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);

$idc = 1;
echo "<h3><b>Loans Report:</b></h3>";

while($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$l_num = $data['loan_id'];
	echo '<p><b>'.$idc.'. Loan Number </b> '.$l_num.':</p>';
	
	$sql="SELECT `file_size`
		  FROM `file_records_final`
		  WHERE `loan_number`='$l_num' AND (`date_created` >= '2023-11-01 00:00:00' AND `date_created` < '2023-11-20 00:00:00')";
	$rst=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	$total_loan_files = $rst->num_rows;
	$total_loan_size = 0;
	
	if($total_loan_files>$average_files_loans)
	{
		$loan_average = "Above Average";
	}
	else
	{
		$loan_average = "Below Average";
	}
	
	while($dt=$rst->fetch_array(MYSQLI_ASSOC))
	{
		$total_loan_size += $dt['file_size'];	
	}
	
	$a_loan_size = $total_loan_size/$total_loan_files;
	
	if($a_loan_size>$average_size_loans)
	{
		$loan_Saverage = "Above Average";
	}
	else
	{
		$loan_Saverage = "Below Average";
	}
	
	echo "<ul>";
	echo "<li>	<b>Documents Received:</b> $total_loan_files ($loan_average)</li>";
	echo "<li>	<b>Total Size of Loan Files:</b> $total_loan_size Bytes ($loan_Saverage)</li>";
	echo "</ul>";
	$idc++;
}



?>
</body>
</html>
