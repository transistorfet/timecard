<html>
<head>
	<title>Time Tracker</title>
	<link href="/timetrack/css/common.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="/timetrack/js/common.js"></script>
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
	Copyright &copy; 2010 PikaLabs/Michael Gagnon | No License | Internal Development Version
</div>

</body>

</html>
