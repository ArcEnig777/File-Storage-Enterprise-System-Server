<?php
include("functions.php");
date_default_timezone_set('US/Central');

$s_id=Create_Session();

if(empty($s_id))
{
	echo "Session could not be created; Exiting script\r\n";
	exit();
}

$pending_f=Check_for_previous();

if(empty($pending_f))
{
	echo "No files to Transfer; Exiting script\r\n";
	Close_Session($s_id);
	exit();
}

Request_Files($s_id,$pending_f);

Close_Session($s_id);

?>
