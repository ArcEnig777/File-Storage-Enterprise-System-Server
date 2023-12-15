<?php
$user="say526";
$pass="D7ZcwV9KT#bjp$6L";
$sid="3ad884c1a071055978fe93dd059bbc4a9c446515";
$data="sid=$sid";
$ch=curl_init('https://cs4743.professorvaladez.com/api/close_session');
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

if($cinfo[0]=="Status: OK")
{
		echo "\r\nSession Closed Successfully!\r\n";
		echo "SID: $sid\r\n";
		echo "Execution Time: $execution_time\r\n";
}
else
{
		echo "<pre>";
		print_r($cinfo);
		echo "<pre>";
}



?>
