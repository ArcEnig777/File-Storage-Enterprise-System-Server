<?php
include("functions.php");
date_default_timezone_set('US/Central');

$s_id=Create_Session();

Request_all($s_id);

Close_Session($s_id);

?>
