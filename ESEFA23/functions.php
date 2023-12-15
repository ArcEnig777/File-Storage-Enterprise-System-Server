<?php
$user="say526";
$pass="D7ZcwV9KT#bjp$6L";
date_default_timezone_set('US/Central');

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

function redirect($uri)
{
	?>
		<script type="text/javascript">
		<!--
		document.location.href="<?php echo $uri; ?>"
		-->
		</script>
	<?php die;
}

function Create_Session()
{
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
	
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; Session could not be created");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR" && $cinfo[1]!="MSG: Previous Session Found")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		$dblink=db_connect("Files");
		if($e->getMessage()=="No response from server/null response; Session could not be created")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'create')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'create')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
	}
	$time_end=microtime(true);
	$execution_time=($time_end-$time_start)/60;

	if($cinfo[0]=="Status: OK" && $cinfo[1]=="MSG: Session Created")
	{
		$sid=$cinfo[2];
		$dblink=db_connect("Files");
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		
		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('create','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$sid')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$sql = "INSERT INTO `sessions`
				(`session`,`s_status`)
				VALUES ('$sid','Active')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		echo "\r\nSession Created Successfully!\r\n";
		echo "SID: $sid\r\n";
		echo "Execution Time: $execution_time\r\n";
		return $sid;
	}
	else
	{
		if($cinfo[0]=="Status: ERROR" && $cinfo[1]=="MSG: Previous Session Found")
		{
			$s_active=array();
			$dblink=db_connect("Files");
			
			$sql = "SELECT `session`
					FROM `sessions`
					WHERE `s_status`='Active'";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			
			while($dr=$result->fetch_array(MYSQLI_NUM))
			{
				$s_active[]=$dr[0];
			}
			
			if(empty($s_active) || sizeof($s_active)>=2)
			{
				$sql = "UPDATE `sessions`
				SET `s_status`='Inactive'
				WHERE `s_status`='Active'";
				$result=$dblink->query($sql) or
					die("Something went wrong with: $sql<br>".$dblink->error);

				Clear_Session();
				return Create_Session();
			}
			else
			{
				$sid=$s_active[0];
				return $sid;
			}
			
		}
		
	}
}

function Close_Session($SID)
{
	$data="sid=$SID";
	$ch=curl_init('https://cs4743.professorvaladez.com/api/close_session');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; Session could not be closed");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		$dblink=db_connect("Files");
		if($e->getMessage()=="No response from server/null response; Session could not be closed")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'close')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'close')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
	}
	$time_end=microtime(true);
	$execution_time=($time_end-$time_start)/60;
	if($cinfo[0]=="Status: OK")
	{
		$dblink=db_connect("Files");
		
		$sql = "SELECT `session`
				FROM `sessions`
				WHERE `s_status`='Active'
				LIMIT 1";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
			
		$dr=$result->fetch_array(MYSQLI_NUM);
		
		$csid=$dr[0];
		
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		
		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('close','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$csid')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$sql = "UPDATE `sessions`
				SET `s_status`='Inactive'
				WHERE `session`='$SID'";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		echo "\r\nSession Closed Successfully!\r\n";
		echo "SID: $SID\r\n";
		echo "Execution Time: $execution_time\r\n";
	}
}

function Clear_Session()
{
	$user="say526";
	$pass="D7ZcwV9KT#bjp$6L";
	$data='username='.$user.'&password='.$pass;
	$ch=curl_init('https://cs4743.professorvaladez.com/api/clear_session');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; Session could not be cleared");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		$dblink=db_connect("Files");
		if($e->getMessage()=="No response from server/null response; Session could not be cleared")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'clear')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'clear')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
	}
	$time_end=microtime(true);
	$execution_time=($time_end-$time_start)/60;
	if($cinfo[0]=="Status: OK")
	{
		$dblink=db_connect("Files");
		
		$sql = "SELECT `session`
				FROM `sessions`
				WHERE `s_status`='Active'
				LIMIT 1";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
			
		$dr=$result->fetch_array(MYSQLI_NUM);
		
		$csid=$dr[0];
		
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		
		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('clear','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$csid')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		echo "\r\nSession Cleared Successfully!\r\n";
		echo "Execution Time: $execution_time\r\n";
	}
}

function Query_Files($SID)
{
	$user="say526";
	$data='uid='.$user.'&sid='.$SID;
	$dblink=db_connect("Files");
	$ch=curl_init('https://cs4743.professorvaladez.com/api/query_files');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; returning empty array");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		$dblink=db_connect("Files");
		if($e->getMessage()=="No response from server/null response; returning empty array")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'query')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage().'\r\n';
			return array();
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'query')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return array();
		}
	}

	if($cinfo[0]=="Status: OK" && $cinfo[2]=="Action: Continue")
	{
		$tmp=explode(":",$cinfo[1]);
		
		if(empty($tmp[1]))
		{
			return $tmp[1];
		}
		
		$filesP=json_decode($tmp[1],true);
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
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		
		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('query','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$SID')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		echo "Execution Time: $execution_time\r\n";
		return $fileList;
	}
	else if ($cinfo[0]=="Status: OK" && $cinfo[1]=="MSG: No new files found" && $cinfo[2]=="Action: None")
	{
		echo "No new files found: no action is required at this time\r\n";
	}
}

function Request_Files($SID, $f_list)
{
	$err_flag=false;
	$user="say526";
	$dblink=db_connect("Files");
	foreach($f_list as $file)
	{
		$data='uid='.$user.'&sid='.$SID.'&fid='.$file;
		$ch=curl_init('https://cs4743.professorvaladez.com/api/request_file');
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array(
			'content-type: application/x-www-form-urlencoded',
			'content-length: ' . strlen($data))
		);
		try
		{
			$time_start=microtime(true);
			$result = curl_exec($ch);
			curl_close($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if($httpCode=="504" || empty($result))
			{
				throw new Exception("No response from server/null response; try downloading at a later date");
			}
			else if(is_array($result))
			{
				$cinfo=json_decode($result,true);
				throw new Exception("Call error");	
			}
			else
			{
				$c_content=addslashes($result);
				$file_size=strlen($result);
			}
			
			
		}
		catch(Exception $e)
		{
			$dblink=db_connect("Files");
			if($e->getMessage()=="No response from server/null response; try downloading at a later date")
			{
				$t=time();
				$g_date=date("Y-m-d H:i:s",$t);
				$sql = "INSERT INTO `errors`
						(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
						VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request')";
				$result=$dblink->query($sql) or
					die("Something went wrong with: $sql<br>".$dblink->error);
				echo 'Message: ' .$e->getMessage();
				
				$c_content=NULL;
				$file_size=0;
				$err_flag=true;
				continue;
			}
			else
			{
				$t=time();
				$g_date=date("Y-m-d H:i:s",$t);
				$sql = "INSERT INTO `errors`
						(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
						VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request')";
				$result=$dblink->query($sql) or
					die("Something went wrong with: $sql<br>".$dblink->error);
				echo 'Message: ' .$e->getMessage();
				continue;
			}
			
		}
		
		$metadata=explode("-",$file);
		$loan_id=$metadata[0];
		$f_type=$metadata[1];
		$tmp=explode(".",$metadata[2]);
		$date_meta=explode("_",$tmp[0]);
		$date=''.substr($date_meta[0],0,4).'-'.substr($date_meta[0],4,2).'-'.substr($date_meta[0],6,2);
		$time="$date_meta[1]:$date_meta[2]:$date_meta[3]";
		$date_time="$date $time";
		
		$sql = "INSERT INTO `file_records_backup`
				(`file_name`,`file_size`,`file_type`,`date_created`,`loan_number`)
				VALUES ('$file','$file_size','$f_type',STR_TO_DATE('$date_time', '%Y-%m-%d %H:%i:%s'),'$loan_id')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$sql = "INSERT INTO `file_contents_backup`
				(`file_content`)
				VALUES ('$c_content')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$sql = "SELECT `file_id`
				FROM `file_records_backup` 
				ORDER BY `file_id` 
				DESC LIMIT 1";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$transfer_id=$result->fetch_array(MYSQLI_NUM);
		
		if($err_flag)
		{
			$sql = "UPDATE `downloaded_files`
					SET `d_status`='error'
					WHERE `file_name`='$file'";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo "<h3>File: $file written to the list of transferred files</h3>";
		}
		else
		{
			$sql = "UPDATE `downloaded_files`
					SET `d_status`='transferred'
					WHERE `file_name`='$file'";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo "<h3>File: $file written to the list of transferred files</h3>";
		}
		

		$f_counter++;	
		
	}
	
	$time_end=microtime(true);
	$execution_time=($time_end-$time_start)/60;
	$t=time();
	$g_date=date("Y-m-d H:i:s",$t);
	
	$sql = "INSERT INTO `calls`
			(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
			VALUES ('request','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$SID')";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	echo "Execution Time: $execution_time\r\n";
	echo "Files Downloaded: $f_counter\r\n";
}

function Check_for_previous()
{
	$prev_files=array();
	$dblink=db_connect("Files");
	
	$sql = "SELECT `file_name`
			FROM `downloaded_files` 
			WHERE `d_status`='generated'
			LIMIT 200";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	while($dr=$result->fetch_array(MYSQLI_NUM))
	{
		$prev_files[]=$dr[0];
	}
	
	return $prev_files;
}

function Check_for_errors($SID)
{
	$user="say526";
	$err_files=array();
	$dblink=db_connect("Files");
	
	$sql = "SELECT `d_id`,`file_name`
			FROM `downloaded_files` 
			WHERE `d_status`='error'";
	$result=$dblink->query($sql) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	
	while($dr=$result->fetch_array(MYSQLI_ASSOC))
	{
		$fid=$dr['file_name'];
		$e_id=$dr['d_id'];
		$data='uid='.$user.'&sid='.$SID.'&fid='.$fid;
		$ch=curl_init('https://cs4743.professorvaladez.com/api/request_file');
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array(
			'content-type: application/x-www-form-urlencoded',
			'content-length: ' . strlen($data))
		);
		try
		{
			$time_start=microtime(true);
			$result = curl_exec($ch);
			curl_close($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if($httpCode=="504" || empty($result))
			{
				throw new Exception("No response from server/null response; try downloading at a later date");
			}
			else if(is_array($result))
			{
				$cinfo=json_decode($result,true);
				throw new Exception("Call error");	
			}
			else
			{
				$c_content=addslashes($result);
				$file_size=strlen($result);
			}
			
		}
		catch(Exception $e)
		{
			if($e->getMessage()=="No response from server/null response; try downloading at a later date")
			{
				$t=time();
				$g_date=date("Y-m-d H:i:s",$t);
				$sql = "INSERT INTO `errors`
						(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
						VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'check_error')";
				$result=$dblink->query($sql) or
					die("Something went wrong with: $sql<br>".$dblink->error);
				echo 'Message: ' .$e->getMessage();
				
				$c_content=NULL;
				$file_size=0;
				$err_flag=true;
			}
			else
			{
				$t=time();
				$g_date=date("Y-m-d H:i:s",$t);
				$sql = "INSERT INTO `errors`
						(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
						VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'check_error')";
				$result=$dblink->query($sql) or
					die("Something went wrong with: $sql<br>".$dblink->error);
				echo 'Message: ' .$e->getMessage();
				continue;
			}
			
		}
		$sql = "UPDATE `file_contents`
				SET `file_content`='$c_content'
				WHERE `f_id`='$e_id";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		$sql = "UPDATE `file_records`
				SET `file_size`='$file_size'
				WHERE `file_id`='$e_id";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		if($c_content==NULL)
		{
			$sql = "UPDATE `downloaded_files`
					SET `d_status`='error'
					WHERE `d_id`='$e_id'";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);

			echo "<h3>File: $file failed to transfer</h3>";

		}
		else
		{
			$sql = "UPDATE `downloaded_files`
					SET `d_status`='transferred'
					WHERE `d_id`='$e_id'";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);

			echo "<h3>File: $file written to the list of transferred files</h3>";
	
		}
	}
	
}

function Request_all($SID)
{
	$user="say526";
	$pass="D7ZcwV9KT#bjp$6L";
	$data='uid='.$user.'&sid='.$SID;
	$ch=curl_init('https://cs4743.professorvaladez.com/api/request_all_documents');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; try auditing at a later date");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		$dblink=db_connect("Files");
		if($e->getMessage()=="No response from server/null response; try auditing at a later date")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_all')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_all')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
	}
	
	if($cinfo[0]=="Status: OK" && $cinfo[2]=="Action: Done")
	{
		$tmp=explode(":",$cinfo[1]);
		
		if(empty($tmp[1]))
		{
			return $tmp[1];
		}
		
		$filesP=json_decode($tmp[1],true);
		$dblink=db_connect("Files");
		foreach($filesP as $file)
		{
			$sql = "SELECT 1
					FROM `downloaded_files` 
					WHERE `file_name`='$file'
					LIMIT 1";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			
			if($result->num_rows<=0)
			{
				$t=time();
				$g_date=date("Y-m-d H:i:s",$t);
				$sql = "INSERT INTO `downloaded_files`
				(`file_name`,`d_status`,`f_date`)
				VALUES ('$file','generated',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'))";
				$result=$dblink->query($sql) or
					die("Something went wrong with: $sql<br>".$dblink->error);
				echo "<h3>File: $file written to the list of generated files</h3>";
			}
			else
			{
				echo "<h3>File: $file already exist on queue/has been transferred</h3>";
			}
		}
		
		$time_end=microtime(true);
		$execution_time=($time_end-$time_start)/60;
		
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);

		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('request_all','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$SID')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);

		echo "Execution Time: $execution_time\r\n";
	}
}

function Request_all_loans($SID)
{
	$user="say526";
	$pass="D7ZcwV9KT#bjp$6L";
	$dblink=db_connect("Files");
	$data='uid='.$user.'&sid='.$SID;
	$ch=curl_init('https://cs4743.professorvaladez.com/api/request_all_loans');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; try auditing loan ids at a later date");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		
		if($e->getMessage()=="No response from server/null response; try auditing loan ids at a later date")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_all_loans')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return array();
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_all_loans')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return array();
		}
	}
	
	if($cinfo[0]=="Status: OK" && $cinfo[2]=="Action: Done")
	{
		$tmp=explode(":",$cinfo[1]);
		
		$IDL=json_decode($tmp[1],true);
		
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		
		$time_end=microtime(true);
		$execution_time=($time_end-$time_start)/60;
		
		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('request_all_loans','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$SID')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		return $IDL;
	}
	
}

function Request_loan_files($SID, $loan)
{
	$user="say526";
	$pass="D7ZcwV9KT#bjp$6L";
	$data='uid='.$user.'&sid='.$SID.'&lid='.$loan;
	$ch=curl_init('https://cs4743.professorvaladez.com/api/request_file_by_loan');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; try requesting the files of this loan id at a later date");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		$dblink=db_connect("Files");
		if($e->getMessage()=="No response from server/null response; try requesting the files of this loan id at a later date")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_loan_files')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return array();
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_loan_files')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return array();
		}
	}
	$time_end=microtime(true);
	$execution_time=($time_end-$time_start)/60;

	if($cinfo[0]=="Status: OK" && $cinfo[2]=="Action: Done")
	{
		$tmp=explode(":",$cinfo[1]);
		
		$filesP=json_decode($tmp[1],true);
		
		$t=time();
		$g_date=date("Y-m-d H:i:s",$t);
		
		$time_end=microtime(true);
		$execution_time=($time_end-$time_start)/60;
		
		$sql = "INSERT INTO `calls`
				(`call_type`,`e_time`,`c_datetime`,`c_s_id`)
				VALUES ('request','$execution_time',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'$SID')";
		$result=$dblink->query($sql) or
			die("Something went wrong with: $sql<br>".$dblink->error);
		
		return $filesP;
	}
	
}

function Request_all_len($SID)
{
	$user="say526";
	$pass="D7ZcwV9KT#bjp$6L";
	$dblink=db_connect("Files");
	$data='uid='.$user.'&sid='.$SID;
	$ch=curl_init('https://cs4743.professorvaladez.com/api/request_all_documents');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	try
	{
		$time_start=microtime(true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode=="504")
		{
			throw new Exception("No response from server/null response; try auditing at a later date");
		}
		
		$cinfo=json_decode($result,true);
		
		if($cinfo[0]=="Status: ERROR")
		{
			throw new Exception("Call error");	
		}
	}
	catch(Exception $e)
	{
		$dblink=db_connect("Files");
		if($e->getMessage()=="No response from server/null response; try auditing at a later date")
		{
			$err=$e->getMessage();
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('ERROR','$err','Try again later',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_all')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
		else
		{
			$t=time();
			$g_date=date("Y-m-d H:i:s",$t);
			$sql = "INSERT INTO `errors`
					(`e_status`,`e_msg`,`e_action`,`e_date`,`e_call`)
					VALUES ('$cinfo[0]','$cinfo[1]','$cinfo[2]',STR_TO_DATE('$g_date', '%Y-%m-%d %H:%i:%s'),'request_all')";
			$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
			echo 'Message: ' .$e->getMessage();
			return;
		}
	}
	
	if($cinfo[0]=="Status: OK" && $cinfo[2]=="Action: Done")
	{
		$tmp=explode(":",$cinfo[1]);
		
		if(empty($tmp[1]))
		{
			return $tmp[1];
		}
		
		$filesP=json_decode($tmp[1],true);
		
		$sql = "SELECT `file_name`
				FROM `downloaded_files`";
		$result=$dblink->query($sql) or
				die("Something went wrong with: $sql<br>".$dblink->error);
		
		while($data=$result->fetch_array(MYSQLI_ASSOC))
		{
			$fNames[] = $data['file_name'];
		}
		
		foreach($filesP as $file)
		{
			if(!in_array($file, $fNames))
			{
				echo "<h3>$file</h3>";
			}
		}
		
		return count($filesP);
		
	}
}

?>
