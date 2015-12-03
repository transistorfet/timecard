<!DOCTYPE html>
<html>
<head>
	<title>Work Log</title>
	<link href="<?=base_url()?>css/common.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>js/common.js"></script>
</head>

<script>
	$(document).ready(function () {
		TT.viewTaskLog(<?=$id?>);
	});
</script>

<body>

<div id="container">

	<div id="worklog_container">

		<table class="hidden" id="worklog_row_template">
		<tr>
			<td class="start"></td>
			<td class="end"></td>
			<td class="time"></td>
			<td class="notes"></td>
			<td class="buttons"><img class="delete" src="img/delete.png" alt="Delete" border="0"></td>
		</tr>
		</table>

		<table id="worklog" class="worklog hidden">
		<thead><th class="start">Start</th><th class="end">End</th><th class="time">Time</th><th class="notes">Notes</th><th class="buttons">&nbsp;</th></thead>
		<tbody>
		</tbody>
		</table> 

	</div>

</div>

</body>

</html>
