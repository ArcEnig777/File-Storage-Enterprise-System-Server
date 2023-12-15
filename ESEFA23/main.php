<?php
include("functions.php");
date_default_timezone_set('US/Central');

set_time_limit(300);

$s_id=Create_Session();

if(empty($s_id))
{
	echo "Session could not be created; Exiting script\r\n";
	exit();
}

Check_for_errors($s_id);

$pending_f=Check_for_previous();

$g_files=Query_Files($s_id);

if(empty($pending_f) && empty($g_files))
{
	echo "No files to Transfer; Exiting script\r\n";
	Close_Session($s_id);
	exit();
}

$full_list=array_merge($pending_f,$g_files);

Request_Files($s_id,$full_list);

Close_Session($s_id);

?>
