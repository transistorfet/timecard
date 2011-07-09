<?

class timeview {

	function __construct() {

		$this->CI =& get_instance();

	}

	function secsToTime($tmp) {

		if($tmp<0) { $neg='-'; $tmp*=-1; }
		else $neg='';

		$tmp=round($tmp);

		$seconds = $tmp % 60;
		
		$tmp -= $seconds;
		$tmp /= 60;

		$minutes = $tmp % 60;
	
		$tmp -= $minutes;
		$tmp /= 60;

		$hours = $tmp;

		return $neg.$hours.':'.($minutes < 10 ? '0' : '').$minutes.':'.($seconds < 10 ? '0' : '').$seconds;

	}
	
	function taskRow($task,$level) {

	?>
			<td class="taskStatus" id="task<?=$task['id']?>Status"><img<? if($task['status']=='started') echo ' style="display: inline;"'; ?> class="taskClock" id="task<?=$task['id']?>Clock" src="img/clock.png" /></td>
			<td id="task<?=$task['id']?>Name" ondblclick="TT.updateTaskName(<?=$task['id']?>,'<?=addslashes(htmlentities($task['name']))?>');" onclick="TT.moveItemTo(<?=$task['id']?>);"><? for($count=0;$count<$level;$count++) echo '. . '; ?><?=htmlspecialchars($task['name'])?></td>
			<td class="taskTime" id="task<?=$task['id']?>Time"><?=$this->secsToTime($this->CI->timedb->taskTime($task['id']))?></td>
			<td class="taskTime" id="task<?=$task['id']?>Total"><?=$this->secsToTime($this->CI->timedb->taskTotalTime($task['id']))?></td>
			<td id="task<?=$task['id']?>Actions">
				<a href="javascript: TT.toggleTaskLog(<?=$task['id']?>);"><img src="img/log.png" alt="View Log" border="0" /></a> 
				<a href="javascript: TT.startTask(<?=$task['id']?>);"><img src="img/start.png" alt="Start" border="0" /></a> 
				<a href="javascript: TT.endTask(<?=$task['id']?>);"><img src="img/stop.png" alt="Stop/Pause" border="0" /></a> 
				<a href="javascript: TT.moveItemFrom(<?=$task['id']?>);"><img src="img/move.png" alt="Move" border="0" /></a>
				<a href="javascript: TT.deleteTask(<?=$task['id']?>);"><img src="img/delete.png" alt="Delete" border="0" /></a> 
			</td>
	<?

	}

	function taskList($parent=0,$level=0) {

		if($tasks=$this->CI->timedb->taskList($parent)) {

			if($parent==0) echo <<<EOD
<table id="taskListTable">
	<tr><th>&nbsp;</th><th onclick="TT.moveItemTo(0);">Task Name</th><th>Time</th><th>Total</th><th>&nbsp;</th></tr>
EOD;

			foreach($tasks as $task) { ?>
		 <tr id="task<?=$task['id']?>">
				<? $this->taskRow($task,$level); ?>
		 </tr>
		 <tr id="task<?=$task['id']?>LogRow" class="taskLogRow">
				<td class="taskLogArea" id="task<?=$task['id']?>LogArea" colspan="5"></td>
		 </tr>
			<? $this->taskList($task['id'],($level+1)) ?>
			<? } 

			if($parent==0) echo '</table>';
	
		}

	}

	function worklog($taskid) {

		$data=$this->CI->timedb->worklog($taskid);

?>
<table class="worklog" id="task<?=$taskid?>LogTable">
<thead><th>Start</th><th>End</th><th>Time</th><th>Notes</th><th>&nbsp;<th></thead>
<tbody id="task<?=$taskid?>LogTableBody">
<?

		if($data) foreach($data as $row) {
			?>
			<tr id="log<?=$row['id']?>">
	<td class="logDateTime" id="log<?=$row['id']?>Start" ondblclick="TT.updateLogStart(<?=$taskid?>,'<?=$row['id']?>','<?=addslashes(htmlentities($row['start']))?>');"><?=htmlspecialchars($row['start'])?></td>
	<td class="logDateTime" id="log<?=$row['id']?>End"<? if(!empty($row['end'])) { ?> ondblclick="TT.updateLogEnd(<?=$taskid?>,'<?=$row['id']?>','<?=addslashes(htmlentities($row['end']))?>');"<? } ?>><?=htmlspecialchars($row['end'])?></td>
	<td class="logTime" id="log<?=$row['id']?>Time"><? if(!empty($row['end'])) echo $this->secsToTime($row['time']); ?></td>
	<td class="logNotes" id="log<?=$row['id']?>Notes" ondblclick="TT.updateLogNotes(<?=$taskid?>,'<?=$row['id']?>','<?=addslashes(htmlentities($row['notes']))?>');"><?=htmlspecialchars($row['notes'])?></td>
	<td class="logActions" id="log<?=$row['id']?>Actions"><a href="javascript: TT.deleteLog(<?=$row['id']?>);"><img src="img/delete.png" alt="Delete" border="0" /></a></td>
			</tr>
			<?
		}

		?></tbody></table><?

	}

}
