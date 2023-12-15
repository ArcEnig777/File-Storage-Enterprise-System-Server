<?php
$user="say526";
$pass="D7ZcwV9KT#bjp$6L";
$SID="8d257adafc6bc623862b2fd50a3d51a316489731";
$data='uid='.$user.'&sid='.$SID;
$ch=curl_init('https://cs4743.professorvaladez.com/api/request_all_documents');
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
	$tmp=explode(":",$cinfo[1]);
	
	echo "<pre>";
	print_r($cinfo);
	echo "</pre>";
}
else
{
	echo "<pre>";
	print_r($cinfo);
	echo "</pre>";
}

?>
