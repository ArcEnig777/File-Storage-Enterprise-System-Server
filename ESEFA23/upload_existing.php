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
</head>
<body>
<?php
include("functions.php");
$dblink=db_connect("Files");
date_default_timezone_set('US/Central');
echo '<div id="page-inner">';
echo '<h1 class="page-head-line">Upload a new file for an existing Loan to FileStorage</h1>';
echo '<div class="panel-body">';
if(isset($_REQUEST['err']) && ($_REQUEST['err'] == "error_ftype"))
{
	echo '<div class="alert alert-danger alert-dismissbale">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
	echo 'ERROR: File uploaded is not a pdf, please try again</div>';
	
	$t=time();
	$g_date=date("Y-m-d H:i:s",$t);
	
	$sql = "INSERT INTO `errors`
			(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
			VALUES ('ERROR','File uploaded is not a pdf','Try again',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'upload_existing')";
	$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
}
else if(isset($_REQUEST['err']) && ($_REQUEST['err'] == "error_efile"))
{
	echo '<div class="alert alert-danger alert-dismissbale">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
	echo 'ERROR: There was no file uploaded, please try again</div>';
	
	$t=time();
	$g_date=date("Y-m-d H:i:s",$t);
	
	$sql = "INSERT INTO `errors`
			(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
			VALUES ('ERROR','There was no file uploaded','Try again',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'upload_existing')";
	$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
}
echo '<form method="post" enctype="multipart/form-data" action="">';
echo '<input type="hidden" name="uploadedby" value="user@testmail.com">';
echo '<input type="hidden" name="MAX_FILE_SIZE" value="10000000">';
echo '<div class="form-group">';
echo '<label for="loanNum" class="control-label">Loan Number</label>';
echo '<select class="form-control" name="loanNum">';
$sql="SELECT `loan_id`
	  FROM `loans`
	  WHERE 1";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
while($data=$result->fetch_array(MYSQLI_ASSOC))
{
	echo '<option value="'.$data['loan_id'].'">'.$data['loan_id'].'</option>';
}
echo '</select>';
echo '</div>';
echo '<div class="form-group">';
echo '<label for="ftype" class="control-label">Document Type</label>';
echo '<select class="form-control" name="ftype">';
$sql="SELECT `file_type`
	  FROM `doc_type`
	  WHERE 1";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
while($data=$result->fetch_array(MYSQLI_ASSOC))
{
	echo '<option value="'.$data['file_type'].'">'.$data['file_type'].'</option>';
}
echo '</select>';
echo '</div>';
echo '<div class="form-group">';
echo '<label class="control-label col-lg-4">File Upload</label>';
echo '<div class="">';
echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
echo '<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px"></div>';
echo '<div class="row">';
echo '<div class="col-md-2">';
echo '<span class="btn btn-file btn-primary">';
echo '<span class="fileupload-new">Select File</span>';
echo '<span class="fileupload-exists">Change</span>';
echo '<input name="userfile" type="file"></span></div>';
echo '<div class="col-md-2"><a href="#" class="btn btn-danger fileupload-exists"
data-dismiss="fileupload">Remove</a></div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '<hr>';
echo '<button type="submit" name="submit" value="submit" class="btn btn-lg btn-block btn-success">Upload File</button>';
echo '</form>';
echo '</div>';
echo '</div>';
if(isset($_POST['submit']))
{
	$uploadDName=date("Ymd_H_i_s");
	$normalDate=date("Y-m-d H:i:s");
	$l_num=$_POST['loanNum'];
	$fType=$_POST['ftype'];
	$tmpName=$_FILES['userfile']['tmp_name'];
	
	$f_size=$_FILES['userfile']['size'];
	$f_type=$_FILES['userfile']['type'];
	
	if($_FILES["userfile"]["error"] != 0) 
	{
		redirect("https://ec2-3-15-170-107.us-east-2.compute.amazonaws.com/upload_existing.php?err=error_efile");
	} 
	else if(mime_content_type("$tmpName") != "application/pdf")
	{
		redirect("https://ec2-3-15-170-107.us-east-2.compute.amazonaws.com/upload_existing.php?err=error_ftype");
	}
	else
	{
		$time_start=microtime(true);
		$fp=fopen($tmpName,'r');
		$content=fread($fp,filesize($tmpName));
		fclose($fp);
		$cleanContent=addslashes($content);
		$fileName="$l_num-$fType-$uploadDName.pdf";
		
		$sql = "INSERT INTO `downloaded_files`
			(`file_name`,`d_status`,`f_date`)
			VALUES ('$fileName','web_upload','$normalDate')";
		$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
		
		$sql = "INSERT INTO `file_records_final`
				(`file_name`,`file_size`,`file_type`,`date_created`,`loan_number`,`upload_type`)
				VALUES ('$fileName','$f_size','$fType','$normalDate','$l_num','web_api')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$sql = "INSERT INTO `file_contents_final`
				(`file_content`)
				VALUES ('$cleanContent')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$time_end=microtime(true);
		$execution_time=($time_end-$time_start)/60;
		
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		
		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('web_upload_existing','$execution_time','$g_date','1')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		redirect("https://ec2-3-15-170-107.us-east-2.compute.amazonaws.com/upload_main.php?msg=success");
	}
	
	
}
?>
</body>
</html>
