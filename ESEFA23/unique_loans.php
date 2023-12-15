<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Unique Loan Numbers</title>
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

$idc = 1;
echo '<p><b>Name: </b> Martin Sanabia</p>';
echo '<p><b>abc123: </b> say526</p>';
	
echo "<h3><b>1.Total number of unique loan numbers generated with a printout of those loan numbers</b></h3>";
echo '<p></p>';

while($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$l_num = $data['loan_id'];
	$sql="SELECT `file_id`
	  	  FROM `file_records_final`
		  WHERE `loan_number`='$l_num' AND `upload_type`='API' AND `date_created` >= '2023-11-01 00:00:00' AND `date_created` < '2023-11-20 00:00:00'";
	$rst=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	$aFiles = $rst->num_rows;
	
	if($aFiles <= 0)
	{
		continue;
	}
	else
	{
		$unique_id[] = $l_num;
	}
}
	
$unique_id_count = count($unique_id);
	
echo "<h3>Total Unique IDs: $unique_id_count</h3>";
	

echo "<h3><b>Loan ID List (from 11/1/2023 12AM to 11/19/2023 11:59 PM):</b></h3>";

foreach($unique_id as $id)
{
	echo '<p><b>'.$idc.'. Loan Number:</b> '.$id.'</p>';
	$idc++;	
	
}

?>
</body>
</html>
