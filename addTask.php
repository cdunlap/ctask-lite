<?php
require_once("settings.php");
if( strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) == FALSE )
	die("Invalid referrer.");
	
header("Content-Type: text/plain");
if( empty($_POST) || empty($_POST['Type']) || empty($_POST['Priority']) || empty($_POST['Status']) || empty($_POST['Description']) )
{
	echo json_encode(array("success"=>false,"error"=>"Missing one or more required fields."));
	exit;
}

extract($_POST);

$db = sqlite_open($config['dbpath']) or die("Could not open database.");

// Find the max # of ID's in the database and increment by one to generate a new ID sentinal value
$result = sqlite_query($db, "SELECT MAX(id) FROM tasks");
if( !$result )
{
	echo json_encode(array("success"=>false,"error"=>sqlite_error_string(sqlite_last_error($db))));
	exit;
}
$ID = sqlite_fetch_single($result) + 1;

$Type = sqlite_escape_string($Type);
$Priority = sqlite_escape_string($Priority);
$Status = sqlite_escape_string($Status);
$Description = sqlite_escape_string($Description);
$LongDescription = sqlite_escape_string(htmlentities($LongDescription));
$DateStamp = date($config['dateformat']);

$success = sqlite_exec($db,"INSERT INTO tasks (id, date, type, status, priority, description, long_description) VALUES($ID,'$DateStamp','$Type','$Status','$Priority','$Description','$LongDescription')", $errorMsg);

if( $success )
	echo json_encode(array("success"=>$success, "id"=>$ID,"date"=>$DateStamp));
else
	echo json_encode(array("success"=>false, "error"=>$errorMsg));

?>