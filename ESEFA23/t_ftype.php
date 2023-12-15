<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Total files per file type</title>
</head>
<body>
<?php
include("functions.php");
$dblink=db_connect("Files");
date_default_timezone_set('US/Central');
$doc_types = ["Financial","Title","Other","Internal","Credit","Personal","Legal","Closing"];
print('<p><b>Name: </b> Martin Sanabia</p>');
echo '<p><b>abc123: </b> say526</p>';
echo '<h3><b>Report From: </b>11/1/2023 12AM to 11/19/2023 11:59 PM</b></h3>';
	
echo "<h3><b>6.List the total number of each document received across all loan numbers</b></h3>";
	
foreach($doc_types as $type)
{
	$sql="SELECT `file_id`
		  FROM `file_records_final`
		  WHERE `file_type`='$type' AND (`date_created` >= '2023-11-01 00:00:00' AND `date_created` < '2023-11-20 00:00:00') AND `upload_type`='API'";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);

	$ftype_total = $result->num_rows;

	echo '<p><b>Total Files of File Type '.$type.':</b> '.$ftype_total.'</p>';

}



?>
</body>
</html>
