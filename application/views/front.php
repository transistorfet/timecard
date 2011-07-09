<html>
<head>
	<title>Time Tracker</title>
	<link href="<?=base_url()?>css/common.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>js/common.js"></script>
</head>

<body onload="initialize();">
<h1>Time Tracker</h1>

<div id="msg"></div>

<div id="newtask"><a href="javascript: TT.newTask();">New Task</a></div>

<div id="taskList">
  Loading task list...
</div>

<div id="hint">
  Double-click task name, or log start/end/notes entry to edit.
</div>

<div id="footer"> 
	Copyright &copy; 2011 Michael Gagnon | <a href="http://www.gnu.org/licenses/gpl-3.0.html">GPLv3 License</a> | v0.2a1 (we have bugs)
</div>

</body>

</html>
