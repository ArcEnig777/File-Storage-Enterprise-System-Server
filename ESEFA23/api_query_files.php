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
$data='uid='.$user.'&sid='.$sid;
$ch=curl_init('https://cs4743.professorvaladez.com/api/query_files');
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_HTTPHEADER, array(
	'content-type: application/x-www-form-urlencoded',
	'content-length: ' . strlen($data))
);
$time_start=microtime(true);
$result = curl_exec($ch);
$time_end=microtime(true);
$execution_time=($time_end-$time_start)/60;
curl_close($ch);
$cinfo=json_decode($result,true);
if($cinfo[0]=="Status: OK" && $cinfo[2]=="Action: Continue")
{
	$tmp=explode(":",$cinfo[1]);
	
	echo "<pre>";
	print_r($cinfo);
	echo "</pre>";
		
	if(empty($tmp[1]))
	{
		return $tmp[1];
	}
	
	$filesP=json_decode($tmp[1],true);
	$dblink=db_connect("Files");
	foreach($filesP as $file)
	{
		$filename=explode("/",$file);
		$fileList[]=$filename[4];
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		$sql = "INSERT INTO `downloaded_files`
		(`file_name`,`d_status`,`f_date`)
		VALUES ('$filename[4]','generated',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'))";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		echo "<h3>File: $filename[4] written to the list of generated files</h3>";
	}
	$time_end=microtime(true);
	$execution_time=($time_end-$time_start)/60;
	echo "Execution Time: $execution_time\r\n";
	return $files;
}
else
{
	echo "<pre>";
	print_r($cinfo);
	echo "</pre>";
	return $cinfo;
}



?>
