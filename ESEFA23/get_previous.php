<?php
function db_connect($db)
{
	$hostname="localhost";
	$username="webuser";
	$password="pLyE@jzi-8hlMK[/";
	$dblink=new mysqli($hostname,$username,$password,$db);
	if(mysqli_connect_errno())
	{
		die("Error connecting to the database: ".mysqli_connect_errno());
	}
	return $dblink;
}
$prev_files=array();
$dblink=db_connect("Files");
$time_start=microtime(true);
$sql = "SELECT `file_name`
		FROM `downloaded_files` 
		WHERE `d_status`='generated'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);

while($data=$result->fetch_array(MYSQLI_NUM))
{
	$prev_files[]=$data[0];
}

$f_list = array("62170098-Other-20231029_14_49_37.pdf", "62170098-Credit-20231029_14_49_46.pdf");

$full_list=array_merge($prev_files,$f_list);

$time_end=microtime(true);
$execution_time=($time_end-$time_start)/60;

echo "<pre>";
print_r($full_list);
echo "</pre>";
echo "Execution Time: $execution_time\r\n";
echo "Execution Time: $execution_time\r\n";

?>
