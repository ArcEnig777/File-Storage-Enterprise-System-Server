<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Total size and count of documents overall and per loan</title>
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
		$unique_id[] = $data['loan_id'];
	}
}
	
$unique_id_count = count($unique_id);

$sql="SELECT `file_size`
	  FROM `file_records_final`
	  WHERE `upload_type`='API' AND `date_created` >= '2023-11-01 00:00:00' AND `date_created` < '2023-11-20 00:00:00'";
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

echo '<h3><b>Report From: </b>11/1/2023 12AM to 11/19/2023 11:59 PM</b></h3>';
echo '<p></p>';

echo '<h3><b>2.The total size of all documents received from the API and the average size of all documents across all loans</b></h3>';
	
echo '<p><b>Total Size of All Documents:</b> '.$total_size.' Bytes</p>';
	
$average_size_loans = $total_size/$total_files;

echo '<p><b>Average Size of All Documents Across All Loans:</b> '.$average_size_loans.' Bytes</p>';

echo '<h3><b>3.The total count of all documents received from the API and the average number of documents across all loans</b></h3>';
echo '<p></p>';
	
echo '<p><b>Total Number of Documents:</b> '.$total_files.' Files</p>';
	
$average_files_loans = $total_files/$unique_id_count;

echo '<p><b>Average Number of Documents Across All Loans:</b> '.$average_files_loans.' Files</p>';

$idc = 1;
echo "<h3><b>4.For each loan number from number 1:</b></h3>";
echo "<ul>";
echo "<li>The total number of documents received </li>";
echo "<li>The size of all documents for the given loan number and state if this average is above or below the global average size from question 2 </li>";
echo "<li>the average number of documents across all loan numbers. Compare each loan number to the average and state if it is above or below average from question 3</li>";
echo "</ul>";
echo '<p></p>';

foreach($unique_id as $id)
{
	echo '<p><b>'.$idc.'. Loan Number </b> '.$id.':</p>';
	
	$sql="SELECT `file_size`
		  FROM `file_records_final`
		  WHERE `loan_number`='$id' AND (`date_created` >= '2023-11-01 00:00:00' AND `date_created` < '2023-11-20 00:00:00')";
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
	
	$avg_size_loan = $total_loan_size/$total_loan_files;
	
	if($avg_size_loan>$average_size_loans)
	{
		$loan_Saverage = "Above Average";
	}
	else
	{
		$loan_Saverage = "Below Average";
	}
	
	echo "<ul>";
	echo "<li>	<b>Documents Received:</b> $total_loan_files ($loan_average)</li>";
	echo "<li>	<b>Average Size of Loan Files:</b> $total_loan_size Bytes ($loan_Saverage)</li>";
	echo "</ul>";
	$idc++;
}



?>
</body>
</html>
