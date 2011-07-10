<html>
<head>
	<title>phpTimecard</title>
	<link href="<?=base_url()?>css/common.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>js/common.js"></script>
</head>

<body onload="initialize();">

<div id="container">

<div id="main_left">

	<div id="taskList">
		Loading task list...
	</div>

	<div id="msg"></div>

	<div id="newtask"><a href="javascript: TT.newTask();">New Task</a></div>

</div>

<div id="main_right">

	<table class="hidden" id="log_row_template">
	<tr>
		<td class="start"></td>
		<td class="end"></td>
		<td class="time"></td>
		<td class="notes"></td>
		<td><img class="delete" src="img/delete.png" alt="Delete" border="0"></td>
	</tr>
	</table>

	<table id="log" class="worklog hidden">
	<thead><th>Start</th><th>End</th><th>Time</th><th>Notes</th><th>&nbsp;</th></thead>
	<tbody>
	</tbody>
	</table> 

</div>

<div id="hint">
	Double-click task name, or log start/end/notes entry to edit.
</div>

<h1 id="project_name">phpTimecard v0.2a1</h1>

<div id="footer"> 
	Copyright &copy; 2011 Michael Gagnon | v0.2a1 (we have bugs) | <a href="http://www.gnu.org/licenses/gpl-3.0.html">GPLv3 License</a> | <a href="http://tango.freedesktop.org/">Tango Colors and Icons</a>
</div>

</div>

</body>

</html>
