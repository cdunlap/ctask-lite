<?php
require_once("settings.php");

if( strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) == FALSE )
	die("Invalid referrer.");

header("Content-Type: text/plain");
if( empty($_POST) || empty($_POST['TaskID']) )
{
	echo json_encode(array("success"=>false,"error"=>"Missing one or more required fields."));
	exit;
}

extract($_POST);

$db = sqlite_open($config['dbpath']) or die("Could not open database.");

$task = sqlite_array_query($db,"SELECT * FROM tasks WHERE id=$TaskID", SQLITE_ASSOC);

if( !$task )
	echo json_encode(array("success"=>false,"error"=>sqlite_error_string(sqlite_last_error($db))));
else
	echo json_encode(array("success"=>true, "task"=>$task));

?>