<?php
/*
	CTask-Lite v1.0, a web-based task management system
    Copyright (C) 2009 Cale Dunlap (cale@caledunlap.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
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
{
	$task = $task[0];

	if( get_magic_quotes_gpc() )
	{
		foreach( array_keys($task) as $Key )
		{
			$task[$Key] = stripslashes($task[$Key]);
		}
	}
	echo json_encode(array("success"=>true, "task"=>$task));
}

?>