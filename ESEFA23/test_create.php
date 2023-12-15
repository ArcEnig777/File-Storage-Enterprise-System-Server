<?php
include("functions.php");

$s_id=Create_Session();

echo $s_id;

Close_Session($s_id);

?>
