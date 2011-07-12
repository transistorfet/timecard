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
			<td class="taskName" id="task<?=$task['id']?>Name" ondblclick="TT.updateTaskName(<?=$task['id']?>,'<?=addslashes(htmlentities($task['name']))?>');" onclick="TT.moveItemTo(<?=$task['id']?>);">
				<div><? for($count=0;$count<$level;$count++) echo '. . '; ?><?=htmlspecialchars($task['name'])?></div>
				<div class="taskLinks hidden" style="text-align: right;">
					<a href="javascript: TT.viewTaskLog(<?=$task['id']?>);">[view]</a> 
					<a href="javascript: TT.moveItemFrom(<?=$task['id']?>);">[move]</a>
					<a href="javascript: TT.deleteTask(<?=$task['id']?>);">[delete]</a>
				</div>
			</td>
			<td class="taskTime" id="task<?=$task['id']?>Time"><?=$this->secsToTime($this->CI->timedb->taskTime($task['id']))?></td>
			<td class="taskTime" id="task<?=$task['id']?>Total"><?=$this->secsToTime($this->CI->timedb->taskTotalTime($task['id']))?></td>
			<td id="task<?=$task['id']?>Actions" nowrap="nowarp">
				
				<a href="javascript: TT.startTask(<?=$task['id']?>);"><img src="img/start.png" alt="Start" border="0" /></a> 
				<a href="javascript: TT.endTask(<?=$task['id']?>);"><img src="img/stop.png" alt="Stop/Pause" border="0" /></a> 
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
			<? $this->taskList($task['id'],($level+1)) ?>
			<? } 

			if($parent==0) echo '</table>';
	
		}

	}


}
