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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$config['title'];?></title>
<link href="themes/<?=$config['theme'];?>/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.3.2.min.js" type="text/javascript"></script>
<script src="scripts/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
<script src="scripts/jquery.tablealternate.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#ajax-working').ajaxStart(function(){ 
		$(this).show(); 
		$('#ajax-error').hide();
	}).ajaxStop(function(){
		$(this).hide();
	}).hide();
	
	$('#ajax-error').ajaxError(function(){
		$(this).show();
		$('#ajax-working').hide();
	}).hide();
	
 	$('#ajax-success').hide();
	
	$('#addTask_Submit').click(function(){
		$.post("addTask.php", {
			Type: $('#addTask_Type').val(),
			Priority: $('#addTask_Priority').val(),
			Status: $('#addTask_Status').val(),
			Description: $('#addTask_Description').val(),
			LongDescription: $('#addTask_LongDescription').val()
		}, function( data )
		{
			if( data.success == false )
			{
				ajaxError(data.error);
			}
			else
			{
				var TaskId = data.id;
				var TaskType = $('#addTask_Type').val();
				var TaskDate = data.date;
				var TaskPriority = $('#addTask_Priority').val();
				var TaskDescription = $('#addTask_Description').val();
				var TaskStatus = $('#addTask_Status').val();
				
				var RowHTML = '<tr id="task_'+TaskId+'" class="taskRow">'+
					'<td class="task_id" onClick="showDetails('+TaskId+')">'+TaskId+'</td>'+
					'<td class="task_date" onClick="showDetails('+TaskId+')">'+TaskDate+'</td>'+
					'<td class="task_type" onClick="showDetails('+TaskId+')">'+TaskType+'</td>'+
					'<td class="task_priority" onClick="showDetails('+TaskId+')"><img src="images/prio-'+TaskPriority.toLowerCase()+'.png" width="16" height="16" />'+TaskPriority+'</td>'+
					'<td class="task_status" onClick="showDetails('+TaskId+')"><img src="images/status-'+TaskStatus.toLowerCase()+'.png" width="16" height="16" />'+TaskStatus+'</td>'+
					'<td class="task_description" onClick="showDetails('+TaskId+')">'+TaskDescription+'</td>'+
					'<td class="task_delete"><button class="ui-button ui-state-default ui-corner-all" onClick="deleteTask('+TaskId+')">Delete</button></td></tr>';
				$('#taskTable tbody').append(RowHTML);
				$('#taskTable tbody tr').alternate();

				$('tr#task_'+TaskId).mouseover(function(){
					$(this).addClass('ui-state-hover');
				}).mouseout(function(){
					$(this).removeClass('ui-state-hover');
				});

				resetAddTaskForm();
				ajaxSuccess('Task added successfully.');
			}
		}, 'json');
	});
	
	$('.ui-button').mouseover(function(){
		$(this).addClass('ui-state-hover');
	}).mouseout(function(){
		$(this).removeClass('ui-state-hover');
		$(this).removeClass('ui-state-active');
	}).mousedown(function(){
		$(this).addClass('ui-state-active');
	}).mouseup(function(){
		$(this).removeClass('ui-state-active');
	});
	
	$('.taskRow').mouseover(function(){
		$(this).addClass('ui-state-hover');
	}).mouseout(function(){
		$(this).removeClass('ui-state-hover');
	});
	
	$('.table-alternate tbody tr').alternate();
	
	$('#dlgDeleteConfirm').dialog({
		modal: true,
		autoOpen: false,
		movable: false,
		sizable: false,
		buttons: {
			'Yes': function() {
				$(this).dialog('close');
				$.post("deleteTask.php", {
					TaskID: $('#deleteConfirm_TaskId').val()
				},function(data)
				{
					if( data.success == true )
					{
						$('#taskTable tbody tr#task_'+$('#deleteConfirm_TaskId').val()).remove();
						$('#taskTable tbody tr').alternate();
						ajaxSuccess('Task removed successfully.');
					}
					else
						ajaxError('Error removing task.');
				},'json');
			},
			'No': function() {
				$(this).dialog('close');
			}
		}
	});
	
	$('#dlgTaskDetails').dialog({
		modal: true,
		autoOpen: false,
		movable: false,
		sizable: true,
		autoSize: true,
		width: 500,
		buttons: {
			'Save': function() {
				var TaskId = $('#editTask_TaskId').val();
				var TaskType = $('#editTask_Type').val();
				var TaskPriority = $('#editTask_Priority').val();
				var TaskStatus = $('#editTask_Status').val();
				var TaskDescription = $('#editTask_Description').val();
				
				$.post("saveTaskDetails.php", {
					TaskID: TaskId,
					Type: TaskType,
					Priority: TaskPriority,
					Status: TaskStatus,
					Description: TaskDescription,
					LongDescription: $('#editTask_LongDescription').val()
				}, function( data ) {
					if( data.success == true )
					{
						ajaxSuccess('Successfully updated task.');
						var TaskPriorityHtml = '<img src="images/prio-'+TaskPriority.toLowerCase()+'.png" width="16" height="16" />'+TaskPriority;
						var TaskStatusHtml = '<img src="images/status-'+TaskStatus.toLowerCase()+'.png" width="16" height="16" />'+TaskStatus;
						$('#taskTable tbody tr#task_'+TaskId+' .task_date').text(data.datestamp);
						$('#taskTable tbody tr#task_'+TaskId+' .task_type').text(TaskType);
						$('#taskTable tbody tr#task_'+TaskId+' .task_priority').html(TaskPriorityHtml);
						$('#taskTable tbody tr#task_'+TaskId+' .task_status').html(TaskStatusHtml);
						$('#taskTable tbody tr#task_'+TaskId+' .task_description').text(TaskDescription);
					}
					else
					{
						ajaxError(data.error);
					}
				}, 'json');
				
				$(this).dialog('close');
			},
			'Cancel': function() {
				$(this).dialog('close');
			}
		}
	});
	
	$('#addTask_Reset').click(resetAddTaskForm);
});

function showDetails(taskId)
{
	$.post("getTaskDetails.php",{
		TaskID: taskId
	}, function(data) {
		if( data.success == true )
		{
			var task = data.task;
			$('#editTask_TaskId').val(taskId);
			$('#editTask_Type').val(task.type);
			$('#editTask_Priority').val(task.priority);
			$('#editTask_Status').val(task.status);
			$('#editTask_Description').val(task.description);
			$('#editTask_LongDescription').val(task.long_description);
			
			$('#dlgTaskDetails').dialog('open');
		}
		else
		{
			ajaxError('Error retrieving task information.');
		}
	}, 'json');
}

function ajaxError(msg)
{
	$('#ajax-error').text(msg).show();
	setTimeout("$('#ajax-error').fadeOut('slow')",5000);
}
function ajaxSuccess(msg)
{
	$('#ajax-success').text(msg).show();
	setTimeout("$('#ajax-success').fadeOut('slow')",5000);
}
function deleteTask(taskId)
{
	$('#deleteConfirm_TaskId').val(taskId);
	$('#dlgDeleteConfirm').dialog('open');
}
function resetAddTaskForm()
{
	$('#addTask_Priority').val('Medium');
	$('#addTask_Type').val('Feature Request');
	$('#addTask_Status').val('New');
	$('#addTask_Description').val('');
	$('#addTask_LongDescription').val('');
}
</script>
</head>

<body>
<span id="ajax-working" class="ui-state-highlight">Working...</span>
<span id="ajax-error" class="ui-state-error">AJAX Error...</span>
<span id="ajax-success" class="ui-state-highlight">Success!</span>

<!-- Dialogs -->
<div id="dlgDeleteConfirm" title="Confirm Delete">
<input type="hidden" id="deleteConfirm_TaskId" />
<p><img src="images/question.png" align="left" />Are you sure you want to delete this task?</p>
</div>

<div id="dlgTaskDetails" title="Task Details">
<input type="hidden" id="editTask_TaskId" />
<table>
<tbody>
	<tr>
    	<td class="field-header">Priority:</td>
        <td>
        	<select name="priority" id="editTask_Priority">
            <?php
				foreach( $config['priorities'] as $Priority )
				{
					if( $config['default_priority'] == $Priority )
						echo "<option value=\"$Priority\" selected=\"selected\">$Priority</option>";
					else
						echo "<option value=\"$Priority\">$Priority</option>";
				}
			?>
            </select>
        </td>
    </tr>
    <tr>
    	<td class="field-header">Type:</td>
        <td>
        	<select name="type" id="editTask_Type">
            <?php
				foreach( $config['types'] as $Type )
				{
					if( $config['default_type'] == $Type )
						echo "<option value=\"$Type\" selected=\"selected\">$Type</option>";
					else
						echo "<option value=\"$Type\">$Type</option>";
				}
			?>	
            </select>
        </td>
    </tr>
    <tr>
        <td class="field-header">Status:</td>
        <td>
            <select name="status" id="editTask_Status">
            <?php
				foreach($config['statuses'] as $Status )
				{
					if( $config['default_status'] == $Status )
						echo "<option value=\"$Status\" selected=\"selected\">$Status</option>";
					else
						echo "<option value=\"$Status\">$Status</option>";
				}
			?>
            </select>
        </td>
    </tr>
    <tr>
    	<td class="field-header">Description (255 chars max):</td>
        <td>
       	  <input type="text" maxlength="255" name="editTask_Description" id="editTask_Description" size="50" />
        </td>
    </tr>
    <tr>
    	<td class="field-header">Long Description:</td>
        <td>
       	  <textarea name="editTask_LongDescription" id="editTask_LongDescription" cols="49" rows="8"></textarea>
        </td>
    </tr>
</tbody>
</table>
</div>
<?php
$db = sqlite_open($config['dbpath']);
?>
<div id="header">
<img src="images/header-fg.png" />
</div>
<?=$config['verbiage'];?>
<hr/>
<table width="100%" id="taskTable" class="table-alternate">
<thead>
	<tr class="ui-widget-header">
    	<th width="5%">ID</th>
        <th width="10%">Date Modified</th>
        <th width="10%">Type</th>
        <th width="15%">Priority</th>
        <th width="15%">Status</th>
        <th>Description</th>
        <th width="5%">Delete</th>
    </tr>
</thead>
<tbody>
	<?php
	// $result = sqlite_query($db, "SELECT * FROM tasks");
	$tasks = sqlite_array_query($db, "SELECT * FROM tasks");
	foreach( $tasks as $task )
	{
		if( get_magic_quotes_gpc() )
		{
			foreach( array_keys($task) as $Key )
			{
				$task[$Key] = stripslashes($task[$Key]);
			}
		}
		echo "<tr class=\"taskRow\" id=\"task_$task[id]\">";
		echo "<td class=\"task_id\" onClick=\"showDetails($task[id])\">$task[id]</td>";
		echo "<td class=\"task_date\" onClick=\"showDetails($task[id])\">$task[date]</td>";
		echo "<td class=\"task_type\" onClick=\"showDetails($task[id])\">$task[type]</td>";
		$prioIconURL = "images/prio-".strtolower($task['priority']).".png";
		$statusIconURL = "images/status-".strtolower($task['status']).".png";
		echo "<td class=\"task_priority prio-$task[priority]\" onClick=\"showDetails($task[id])\"><img src=\"$prioIconURL\" height=\"16\" width=\"16\" />$task[priority]</td>";
		echo "<td class=\"task_status status-$task[status]\" onClick=\"showDetails($task[id])\"><img src=\"$statusIconURL\" height=\"16\" width=\"16\" />$task[status]</td>";
		echo "<td class=\"task_description\" onClick=\"showDetails($task[id])\">$task[description]</td>";
		echo "<td class=\"task_delete\"><button class=\"ui-button ui-state-default ui-corner-all\" type=\"button\" onClick=\"deleteTask($task[id])\">Delete</button>";
		echo "</tr>\n";
	}
	?>
</tbody>
</table>
<div class="ui-widget-header">
<strong>Add Task</strong>
</div>
<table>
<tbody>
	<tr>
    	<td class="field-header">Priority:</td>
        <td>
        	<select name="priority" id="addTask_Priority">
            <?php
				foreach( $config['priorities'] as $Priority )
				{
					if( $config['default_priority'] == $Priority )
						echo "<option value=\"$Priority\" selected=\"selected\">$Priority</option>";
					else
						echo "<option value=\"$Priority\">$Priority</option>";
				}
			?>
            </select>
        </td>
    </tr>
    <tr>
    	<td class="field-header">Type:</td>
        <td>
        	<select name="type" id="addTask_Type">
            <?php
				foreach( $config['types'] as $Type )
				{
					if( $config['default_type'] == $Type )
						echo "<option value=\"$Type\" selected=\"selected\">$Type</option>";
					else
						echo "<option value=\"$Type\">$Type</option>";
				}
			?>	
            </select>
        </td>
    </tr>
    <tr>
        <td class="field-header">Status:</td>
        <td>
            <select name="status" id="addTask_Status">
            <?php
				foreach($config['statuses'] as $Status )
				{
					if( $config['default_status'] == $Status )
						echo "<option value=\"$Status\" selected=\"selected\">$Status</option>";
					else
						echo "<option value=\"$Status\">$Status</option>";
				}
			?>
            </select>
        </td>
    </tr>
    <tr>
    	<td class="field-header">Description (255 chars max):</td>
        <td>
       	  <input type="text" maxlength="255" name="addTask_Description" id="addTask_Description" size="70" />
        </td>
    </tr>
    <tr>
    	<td class="field-header">Long Description:</td>
        <td>
       	  <textarea name="addTask_LongDescription" id="addTask_LongDescription" cols="70" rows="6"></textarea>
        </td>
    </tr>
    <tr>
    	<td colspan="2" style="text-align: center">
        	<button class="ui-button ui-state-default ui-corner-all" type="button" id="addTask_Submit">Submit</button>
            <button class="ui-button ui-state-default ui-corner-all" type="reset" id="addTask_Reset">Reset</button>
        </td>
    </tr>
</tbody>
</table>
<?php
sqlite_close($db);
?>
<div id="footer" class="ui-widget-content">
Powered by <a href="http://cdunlap.github.com/ctask-lite/" target="_blank">CTask-Lite</a> v1.0<br/>
<a href="http://www.gnu.org/licenses/gpl.html" target="_blank"><img src="images/gplv3-88x31.png" border="0" /></a>
</div>
</body>
</html>