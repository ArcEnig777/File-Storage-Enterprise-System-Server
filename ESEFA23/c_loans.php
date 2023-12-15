<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Missing Empty and Complete Loans</title>
</head>
<body>
<?php
include("functions.php");
$dblink=db_connect("Files");
date_default_timezone_set('US/Central');
$doc_types = ["Financial","Title","Other","Internal","Credit","Personal","Legal","Closing"];
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
	
$complete_loans = [];
$empty_loans = [];
echo '<p><b>Name: </b> Martin Sanabia</p>';
echo '<p><b>abc123: </b> say526</p>';	
echo '<h3><b>Report From: </b>11/1/2023 12AM to 11/19/2023 11:59 PM</b></h3>';

echo "<h3><b>5.A complete loan is one that has at least one of the following documents: credit, closing, title, financial, personal, internal, legal, other:</b></h3>";
echo "<ul>";
echo "<li>A list of all loan numbers that are missing at least one of these documents and which document(s) is missing</li>";
echo "<li>A list of all loan numbers that have all documents</li>";
echo "<li>A list of all loan numbers that received 0 documents</li>";
echo "</ul>";
	
echo "<h3><b>Loans Missing Documents:</b></h3>";

foreach($unique_id as $id)
{
	$loan_ftypes = [];
	$doc_types = ["Financial","Title","Other","Internal","Credit","Personal","Legal","Closing"];
	
	$sql="SELECT `file_type`
		  FROM `file_records_final`
		  WHERE `loan_number`='$id' AND (`date_created` >= '2023-11-01 00:00:00' AND `date_created` < '2023-11-20 00:00:00') AND `upload_type`='API'";
	$rst=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	if($rst->num_rows<=0)
	{
		$empty_loans[] = $l_num;
		continue;
	}
	else
	{
		while($dt=$rst->fetch_array(MYSQLI_ASSOC))
		{
			$ftype = $dt['file_type'];
			
			if(in_array($ftype,$doc_types) && !in_array($ftype,$loan_ftypes))
			{
				$loan_ftypes[] = $ftype;
			}
		}
		
		$comp = array_diff($doc_types, $loan_ftypes);
		
		if(count($comp)<=0)
		{
			$complete_loans[] = $id;
			continue;
		}
		else
		{
			echo '<p><b>Loan Number: </b>'.$id.'</p>';
			echo "<ul>";
			echo "<li><b>Missing Files:</b></li>";
			echo "<li>	<ul>";
			foreach($comp as $type)
			{
				echo "<li>$type</li>";	
			}
			echo "</ul>";
			echo "</li>";
			echo "</ul>";
		}
	}
	
}
echo "<h3><b>Complete Loans:</b></h3>";
echo "<ul>";
foreach($complete_loans as $complete)
{
	echo '<p><b>Loan Number: </b>'.$complete.'</p>';
}
echo "</ul>";

echo "<h3><b>Empty Loans:</b></h3>";

if (count($empty_loans)<=0)
{
	echo '<p>No empty loans found</p>';
}
else
{
	echo "<ul>";
	foreach($empty_loans as $empty)
	{
		echo '<p><b>Loan Number: </b>'.$empty.'</p>';
	}
	echo "</ul>";
}



?>
</body>
</html>
