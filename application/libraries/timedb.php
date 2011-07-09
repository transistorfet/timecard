<?

class timedb {

	function __construct() {

		$this->CI =& get_instance();
		$this->CI->db->query('SET time_zone = "US/Pacific"');

	}
	
	function taskTotalTime($id) {

		$total=0;

		$total = $total + $this->taskTime($id);

		// determine child tasks to add into time.
		$result=mysql_query('select id from tasks where parent="'.mysql_real_escape_string($id).'"');
		while($nfo=mysql_fetch_assoc($result)) $total = $total + $this->taskTotalTime($nfo['id']);

		return $total;

	}

	function taskTime($id) {

		$result=mysql_query('select sum(UNIX_TIMESTAMP(end)-UNIX_TIMESTAMP(start)) as time from worklog where taskid="'.mysql_real_escape_string($id).'" and end is not null');
		$nfo=mysql_fetch_array($result);

		return $nfo['time'];

	}

	function task($id) {

		$result = mysql_query('select * from tasks where id = "'.mysql_real_escape_string($id).'"');
		return mysql_fetch_assoc($result);
	
	}

	function taskList($parent=0) {

		$result=mysql_query('select * from tasks where parent="'.mysql_real_escape_string($parent).'" order by name');

		echo mysql_error();

		while($nfo=mysql_fetch_assoc($result)) {
			$startedResult=mysql_query('select * from worklog where end is null and taskid="'.mysql_real_escape_string($nfo['id']).'" limit 1');
			if(mysql_num_rows($startedResult)>0) $nfo['status']='started';
			else $nfo['status']='';
			$return[]=$nfo;
		}

		if(empty($return)) return FALSE;
		else return $return;

	}

	function startTask($id) {

		// see if there is already something started.
		$result=mysql_query('select * from worklog where taskid="'.mysql_real_escape_string($id).'" and end is NULL');
		if(mysql_num_rows($result)>0) return FALSE;

		
		if(mysql_error()) return FALSE;

		// start a new task.
		mysql_query('insert into worklog set taskid="'.mysql_real_escape_string($id).'", start=now()');

		if(mysql_error()) return FALSE;
		
		$id=mysql_insert_id();

		// get now();
		$result=mysql_query('select id,start from worklog where id="'.mysql_real_escape_string($id).'"');
		$return=mysql_fetch_assoc($result);
		return $return;

	}

	function endTask($id) {

		// see if there is already something started (required).
		$result=mysql_query('select * from worklog where taskid="'.mysql_real_escape_string($id).'" and end is NULL');
		if(mysql_num_rows($result)==0) return TRUE;

		if(mysql_error()) return FALSE;

		// end the task.
		$nfo=mysql_fetch_assoc($result);
		mysql_query('update worklog set end=now() where id="'.mysql_real_escape_string($nfo['id']).'"');

		if(mysql_error()) return FALSE;

		return $this->logEntry($nfo['id']);

	}

	function worklog($id) {

		$result=mysql_query('select *,(UNIX_TIMESTAMP(end)-UNIX_TIMESTAMP(start)) as time,UNIX_TIMESTAMP(end) as end_timestamp, UNIX_TIMESTAMP(start) as start_timestamp from worklog where taskid="'.mysql_real_escape_string($id).'" order by start');
		while($nfo=mysql_fetch_assoc($result)) $return[]=$nfo;

		if(empty($return)) return FALSE;
		else return $return;

	}

	function logEntry($id) {
		$result = mysql_query('select *,(UNIX_TIMESTAMP(end)-UNIX_TIMESTAMP(start)) as time from worklog where id = "'.mysql_real_escape_string($id).'"');
		return mysql_fetch_assoc($result);
	}

	function delLog($id) {
		$result = mysql_query('delete from worklog where id="'.mysql_real_escape_string($id).'"');
		if(mysql_affected_rows()>0) return TRUE;
		else return FALSE;
	}

	function delTask($id) {

		$result = mysql_query('select * from tasks where parent="'.mysql_real_escape_string($id).'"');
		while($nfo=mysql_fetch_assoc($result)) $this->delTask($nfo['id']);

		mysql_query('delete from tasks where id="'.mysql_real_escape_string($id).'"');
		mysql_query('delete from worklog where taskid="'.mysql_real_escape_string($id).'"');

		return TRUE;

	}

	function newTask($name) {

		$result = mysql_query('insert into tasks set name = "'.mysql_real_escape_string($name).'"');
		return TRUE;

	}

	// return the top level parent of a task.
	function topParentTask($id) {

		$result = mysql_query('select * from tasks where id = "'.mysql_real_escape_string($id).'"');
		$nfo=mysql_fetch_assoc($result);

		if($nfo['parent']==0) return $nfo;
		else return $this->topParentTask($nfo['parent']);

	}

	function moveTask($fromId,$toId) {

		if($fromId==$toId) return TRUE;

		// make sure we're not moving a task to it's own parent (or grandparent, etc).	
		$testId=$toId;
		while(TRUE) {
			$result = mysql_query('select parent from tasks where id="'.mysql_real_escape_string($testId).'"');
			$nfo=mysql_fetch_assoc($result);
			if($nfo['parent']==$fromId) return FALSE;
			if($nfo['parent']==0) break;
			$testId=$nfo['parent'];
		}
		

		mysql_query('update tasks set parent = "'.mysql_real_escape_string($toId).'" where id = "'.mysql_real_escape_string($fromId).'"');
		return TRUE;

	}

	function renameTask($id,$name) {

		mysql_query('update tasks set name = "'.mysql_real_escape_string($name).'" where id = "'.mysql_real_escape_string($id).'"');
		if(mysql_affected_rows()>0) return TRUE;
		else return FALSE;

	}

	function editLogNotes($id,$notes) {

		mysql_query('update worklog set notes = "'.mysql_real_escape_string($notes).'" where id = "'.mysql_real_escape_string($id).'"');
		if(mysql_affected_rows()>0) return TRUE;
		else return FALSE;
	}

	function editStartTime($id,$time) {
		mysql_query('update worklog set start = "'.mysql_real_escape_string($time).'" where id = "'.mysql_real_escape_string($id).'"');
		if(mysql_affected_rows()>0) return $this->logEntry($id);
		else return FALSE;
	}

	function editEndTime($id,$time) {
		mysql_query('update worklog set end = "'.mysql_real_escape_string($time).'" where id = "'.mysql_real_escape_string($id).'"');
		if(mysql_affected_rows()>0) return $this->logEntry($id);
		else return FALSE;
	}

}
