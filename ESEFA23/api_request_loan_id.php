<?php
$user="say526";
$pass="D7ZcwV9KT#bjp$6L";
$SID="c835f812ce43022d6f7a48c42dc4e46adeb80eab";
$loan="97631280";
$data='uid='.$user.'&sid='.$SID.'&lid='.$loan;
$ch=curl_init('https://cs4743.professorvaladez.com/api/request_file_by_loan');
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
