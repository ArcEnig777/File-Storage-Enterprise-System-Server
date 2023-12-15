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
$user="say526";
$pass="D7ZcwV9KT#bjp$6L";
$sid="44ec9473e1ae8f3a499a49c9a6bebfc6cb3ef038";
$f_list = array("62170098-Other-20231029_14_49_37.pdf", "62170098-Credit-20231029_14_49_46.pdf");
$dblink=db_connect("Files");
foreach($f_list as $file)
{
	$data='uid='.$user.'&sid='.$sid.'&fid='.$file;
	$ch=curl_init('https://cs4743.professorvaladez.com/api/request_file');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	$time_start=microtime(true);
	$result = curl_exec($ch);
	curl_close($ch);
	$c_content=addslashes($result);
	$file_size=strlen($result);
	$metadata=explode("-",$file);
	$loan_id=$metadata[0];
	$f_type=$metadata[1];
	$tmp=explode(".",$metadata[2]);
	$date_meta=explode("_",$tmp[0]);
	$date=''.substr($date_meta[0],0,4).'-'.substr($date_meta[0],4,2).'-'.substr($date_meta[0],6,2);
	$time="$date_meta[1]:$date_meta[2]:$date_meta[3]";
	$date_time="$date $time";
	
	$sql = "INSERT INTO `file_records`
			(`file_name`,`file_size`,`file_type`,`date_created`,`loan_number`)
			VALUES ('$file','$file_size','$f_type',STR_TO_DATE('$date_time', '%Y-%m-%d %H:%i:%s'),'$loan_id')";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	$sql = "INSERT INTO `file_contents`
			(`file_content`)
			VALUES ('$c_content')";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	$sql = "SELECT `file_id`
			FROM `file_records` 
			ORDER BY `file_id` 
			DESC LIMIT 1";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	$transfer_id=$result->fetch_array(MYSQLI_NUM);
	
	$sql = "UPDATE `downloaded_files`
			SET `d_status`='transferred'
			WHERE `d_id`='$transfer_id[0]'";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	echo "<h3>File: $file written to the list of generated files</h3>";
	
}

$time_end=microtime(true);
$execution_time=($time_end-$time_start)/60;
echo "Execution Time: $execution_time\r\n";



?>
