<?php
$user="say526";
$pass="D7ZcwV9KT#bjp$6L";
$data='username='.$user.'&password='.$pass;
$ch=curl_init('https://cs4743.professorvaladez.com/api/create_session');
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

if($cinfo[0]=="Status: OK" && $cinfo[1]=="MSG: Session Created")
{
	$sid=$cinfo[2];
	$data="sid=$sid&uid=$user";
	echo "\r\nSession Created Successfully!\r\n";
	echo "SID: $sid\r\n";
	echo "Execution Time: $execution_time\r\n";
}
else
{
	if($cinfo[0]=="Status: ERROR" && $cinfo[1]=="MSG: Previous Session Found")
	{
			$data='username='.$user.'&password='.$pass;
			$ch=curl_init('https://cs4743.professorvaladez.com/api/clear_session');
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
				echo "\r\nSession Cleared Successfully!\r\n";
				echo "SID: $sid\r\n";
				echo "Execution Time: $execution_time\r\n";
			}
			else
			{
				echo "<pre>";
				print_r($cinfo);
				echo "<pre>";
			}
			$data='username='.$user.'&password='.$pass;
			$ch=curl_init('https://cs4743.professorvaladez.com/api/create_session');
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

			if($cinfo[0]=="Status: OK" && $cinfo[1]=="MSG: Session Created")
			{
				$sid=$cinfo[2];
				$data="sid=$sid&uid=$user";
				echo "\r\nSession Created Successfully!\r\n";
				echo "SID: $sid\r\n";
				echo "Execution Time: $execution_time\r\n";
			}
	}
	else
	{
		echo "<pre>";
		print_r($cinfo);
		echo "<pre>";
	}
}

?>
