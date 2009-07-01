<?php
$config = array(
	"dbpath" => "ctask.db",
	"title" => "CTask",
	"theme" => "dark-hive",
	"verbiage" => "<p>Enter tasks here. They will be completed by priority or the order they are entered.</p>",
	"dateformat" => "m/d/Y H:i \(\G\M\T P\)",
	"priorities" => array("Low","Medium","High"),
	"default_priority" => "Medium",
	"types" => array("Feature Request", "Change Request", "Bug"),
	"default_type" => "Feature Request",
	"statuses" => array("New", "In Progress", "Complete", "Duplicate", "Fixed", "Can't Reproduce"),
	"default_status" => "New"
);
?>