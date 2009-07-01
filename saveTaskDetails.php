<?php
require_once("settings.php");

if( strstr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) == FALSE )
	die("Invalid referrer.");

header("Content-Type: text/plain");
if( empty($_POST) || empty($_POST['TaskID']) || empty($_POST['Priority']) || empty($_POST['Type']) || empty($_POST['Status']) || empty($_POST['Description']) )
{
	echo json_encode(array("success"=>false,"error"=>"Missing one or more required fields."));
	exit;
}

extract($_POST);

$db = sqlite_open($config['dbpath']) or die("Could not open database.");

$DateStamp = date($config['dateformat']);

$Type = sqlite_escape_string($Type);
$Priority = sqlite_escape_string($Priority);
$Status = sqlite_escape_string($Status);
$Description = sqlite_escape_string($Description);
$LongDescription = sqlite_escape_string(htmlentities($LongDescription));
$DateStamp = date($config['dateformat']);

$success = sqlite_exec($db,"UPDATE tasks SET date='$DateStamp', type='$Type', priority='$Priority', status='$Status', description='$Description', long_description='$LongDescription' WHERE id=$TaskID", $errorMsg);

if( !$success )
	echo json_encode(array("success"=>false,"error"=>$errorMsg));
else
{
	echo json_encode(array("success"=>true, "datestamp"=>$DateStamp));
}

?>