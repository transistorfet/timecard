<?

class Timedb extends CI_Model {

	/*
	function __construct() {

		$this->CI =& get_instance();
		//$this->CI->db->query('SET time_zone = "US/Pacific"');

	}
	*/
	
	function taskTotalTime($id) {

		$total=0;

		$total = $total + $this->taskTime($id);

		// determine child tasks to add into time.
		$query=$this->db->query('select id from tasks where parent=?', array($id));
		foreach($query->result_array() as $nfo) $total = $total + $this->taskTotalTime($nfo['id']);

		return $total;

	}

	function taskTime($id) {

		$query=$this->db->query('select sum(UNIX_TIMESTAMP(end)-UNIX_TIMESTAMP(start)) as time from worklog where taskid=? and end is not null', array($id));
		$nfo=$query->row_array();

		return $nfo['time'];

	}

	function task($id) {

		$query=$this->db->query('select * from tasks where id=?', array($id));
		return $query->row_array();
	
	}

	function taskList($parent=0) {

		$query=$this->db->query('select * from tasks where parent=? and archived=0 order by name', array($parent));

		echo $this->db->error()['message'];

		foreach($query->result_array() as $nfo) {
			$startedResult=$this->db->query('select * from worklog where end is null and taskid=? limit 1', array($nfo['id']));
			if($startedResult->num_rows()>0) $nfo['status']='started';
			else $nfo['status']='';
			$return[]=$nfo;
		}

		if(empty($return)) return FALSE;
		else return $return;

	}

	function startTask($id) {

		// see if there is already something started.
		$query=$this->db->query('select * from worklog where taskid=? and end is NULL', array($id));
		if($query->num_rows()>0) return FALSE;

		
		if($this->db->error()['code']) return FALSE;

		// start a new task.
		$this->db->query('insert into worklog set taskid=?, start=now()', array($id));

		if($this->db->error()['code']) return FALSE;
		
		$id=$this->db->insert_id();

		// get now();
		$query=$this->db->query('select id,start from worklog where id=?', array($id));
		$return=$query->row_array();
		return $return;

	}

	function endTask($id) {

		// see if there is already something started (required).
		$query=$this->db->query('select * from worklog where taskid=? and end is NULL', array($id));
		if($query->num_rows()==0) return TRUE;

		if($this->db->error()['code']) return FALSE;

		// end the task.
		$nfo=$query->row_array();
		$this->db->query('update worklog set end=now() where id=?', array($nfo['id']));

		if($this->db->error()['code']) return FALSE;

		return $this->logEntry($nfo['id']);

	}

	function worklog($id) {

		$query=$this->db->query('select *,(UNIX_TIMESTAMP(end)-UNIX_TIMESTAMP(start)) as time,UNIX_TIMESTAMP(end) as end_timestamp, UNIX_TIMESTAMP(start) as start_timestamp from worklog where taskid=? order by start', array($id));
		foreach($query->result_array() as $nfo) $return[]=$nfo;

		if(empty($return)) return FALSE;
		else return $return;

	}

	function logEntry($id) {
		$query=$this->db->query('select *,(UNIX_TIMESTAMP(end)-UNIX_TIMESTAMP(start)) as time from worklog where id=?', array($id));
		return $query->row_array();
	}

	function delLog($id) {
		$query=$this->db->query('delete from worklog where id=?', array($id));
		if($this->db->affected_rows()>0) return TRUE;
		else return FALSE;
	}

	function delTask($id) {

		$query=$this->db->query('select * from tasks where parent=?', array($id));
		foreach($query->result_array() as $nfo) $this->delTask($nfo['id']);

		$this->db->query('delete from tasks where id=?', array($id));
		$this->db->query('delete from worklog where taskid=?', array($id));

		return TRUE;

	}

	function newTask($name) {

		$query=$this->db->query('insert into tasks set name=?', array($name));
		return TRUE;

	}

	// return the top level parent of a task.
	function topParentTask($id) {

		$query=$this->db->query('select * from tasks where id=?', array($id));
		$nfo=$query->row_array();

		if($nfo['parent']==0) return $nfo;
		else return $this->topParentTask($nfo['parent']);

	}

	function moveTask($fromId,$toId) {

		if($fromId==$toId) return TRUE;

		// make sure we're not moving a task to it's own parent (or grandparent, etc).	
		$testId=$toId;
		while(TRUE) {
			$query=$this->db->query('select parent from tasks where id=?', array($testId));
			$nfo=$query->row_array();
			if($nfo['parent']==$fromId) return FALSE;
			if($nfo['parent']==0) break;
			$testId=$nfo['parent'];
		}
		

		$this->db->query('update tasks set parent = ? where id = ?', array($toId, $fromId));
		return TRUE;

	}

	function renameTask($id,$name) {

		$this->db->query('update tasks set name = ? where id = ?', array($name, $id));
		if($this->db->affected_rows()>0) return TRUE;
		else return FALSE;

	}

	function editLogNotes($id,$notes) {

		$this->db->query('update worklog set notes = ? where id = ?', array($notes, $id));
		if($this->db->affected_rows()>0) return TRUE;
		else return FALSE;
	}

	function editStartTime($id,$time) {
		$this->db->query('update worklog set start = ? where id = ?', array($time, $id));
		if($this->db->affected_rows()>0) return $this->logEntry($id);
		else return FALSE;
	}

	function editEndTime($id,$time) {
		$this->db->query('update worklog set end = ? where id = ?', array($time, $id));
		if($this->db->affected_rows()>0) return $this->logEntry($id);
		else return FALSE;
	}

}
