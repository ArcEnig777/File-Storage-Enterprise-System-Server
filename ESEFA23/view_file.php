<?php
include("functions.php");
$dblink=db_connect("Files");
if(isset($_REQUEST['err']) && ($_REQUEST['err'] == "error_query"))
{
	echo '<div class="alert alert-danger alert-dismissbale">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>';
	echo 'ERROR: Query for content has failed, please contact server admin and try again later (exit by going back on browser)</div>';
}
$fid=$_REQUEST['fid'];
$sql="SELECT `file_content`
			  FROM `file_contents_final`
			  WHERE `f_id`='$fid'";
$result=$dblink->query($sql);
if (strlen($dblink->error)>0)
{
	$sql = "INSERT INTO `errors`
			(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
			VALUES ('ERROR','$dblink->error','Try again',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'search_loan_number')";
	$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);

	redirect("https://ec2-3-15-170-107.us-east-2.compute.amazonaws.com/view_file.php?err=error_content");
}
$data=$result->fetch_array(MYSQLI_ASSOC);
header('Content-Type: application/pdf');
header('Content-Length: '.strlen($data['file_content']));
echo $data['file_content'];

?>