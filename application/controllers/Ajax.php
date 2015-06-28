<?

class Ajax extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('timeview');
		$this->load->model('timedb');
	}

	public function taskList()
	{

		$this->timeview->taskList(0);

	}

	public function start()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		else die('error');

		$loginfo=$this->timedb->startTask($id);

		if($loginfo) echo $loginfo['id'].','.$loginfo['start'];
		else echo 'error';

	}

	public function end()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		else die('error');

		$endTask=$this->timedb->endTask($id);

		if($endTask) {

			echo $endTask['id'].','.$endTask['end'].','.$this->timeview->secsToTime($endTask['time'])."\n";

			$task = $this->timedb->task($id);

			while(TRUE) {
				echo $task['id'].','.$this->timeview->secsToTime($this->timedb->taskTime($task['id'])).','.$this->timeview->secsToTime($this->timedb->taskTotalTime($task['id']));
				if($task['parent']==0) break;
				else { $task = $this->timedb->task($task['parent']); echo "\n"; }
			}

		}

		else echo 'error';	

	}

	public function log()
	{

		$id = $this->input->get('i');

		$this->db->order_by('start','desc');
		$this->db->select('*,UNIX_TIMESTAMP(start) as start_timestamp,UNIX_TIMESTAMP(end) as end_timestamp');
		$this->db->where('taskid',$id);
		$query = $this->db->get('worklog');

		$worklog = $query->result();		

		echo json_encode(array('log'=>$worklog));

	}

	public function dellog()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		else die('error');

		$logentry=$this->timedb->logEntry($id);

		if($this->timedb->delLog($id)) {
	 
			$task = $this->timedb->task($logentry['taskid']);

			while(TRUE) {
				echo $task['id'].','.$this->timeview->secsToTime($this->timedb->taskTime($task['id'])).','.$this->timeview->secsToTime($this->timedb->taskTotalTime($task['id']));
				if($task['parent']==0) break;
				else { $task = $this->timedb->task($task['parent']); echo "\n"; }
			}
		}

		else echo 'error';

	}
	
	public function newtask()
	{

		if(!empty($_GET['v'])) $value=$_GET['v'];
		else die('error');
		
		if($this->timedb->newTask($value)) echo 'ok';
		else echo 'error';

	}

	public function deltask()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		else die('error');

		if($this->timedb->delTask($id)) echo 'ok';
		else echo 'error';	

	}

	public function movetask()
	{

		if(!empty($_GET['f'])) $fromId=$_GET['f'];
		if(eregi('^[0-9]+$',$_GET['t'])) $toId=$_GET['t'];
		if(empty($fromId) || !eregi('^[0-9]+$',$toId)) die('error');

		if($this->timedb->moveTask($fromId,$toId)) echo 'ok';
		else echo 'error';

	}

	public function renametask()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		if(!empty($_GET['n'])) $name=$_GET['n'];
		if(empty($id) || empty($name)) die('error');

		if($this->timedb->renameTask($id,$name)) echo 'ok';
		else echo 'error';

	}

	public function lognotes()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		if(!empty($_GET['n'])) $notes=$_GET['n'];
		if(empty($id) || empty($notes)) die('error');

		if($this->timedb->editLogNotes($id,$notes)) echo 'ok';
		else echo 'error';

	}

	public function logstart()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		if(!empty($_GET['t'])) $time=$_GET['t'];
		if(empty($id) || empty($time)) die('error');

		$data = $this->timedb->editStartTime($id,$time);

		if($data) { 

			echo $data['start'].','.$this->timeview->secsToTime($data['time'])."\n";

			$logentry= $this->timedb->logEntry($id);
			$task = $this->timedb->task($logentry['taskid']);

			while(TRUE) {
				echo $task['id'].','.$this->timeview->secsToTime($this->timedb->taskTime($task['id'])).','.$this->timeview->secsToTime($this->timedb->taskTotalTime($task['id']));
				if($task['parent']==0) break;
				else { $task = $this->timedb->task($task['parent']); echo "\n"; }
			}

		}
		else echo 'error';

	}

	public function logend()
	{

		if(!empty($_GET['i'])) $id=$_GET['i'];
		if(!empty($_GET['t'])) $time=$_GET['t'];
		if(empty($id) || empty($time)) die('error');

		$data = $this->timedb->editEndTime($id,$time);

		if($data) {

			echo $data['end'].','.$this->timeview->secsToTime($data['time'])."\n";

			$logentry= $this->timedb->logEntry($id);
			$task = $this->timedb->task($logentry['taskid']);

			while(TRUE) {
				echo $task['id'].','.$this->timeview->secsToTime($this->timedb->taskTime($task['id'])).','.$this->timeview->secsToTime($this->timedb->taskTotalTime($task['id']));
				if($task['parent']==0) break;
				else { $task = $this->timedb->task($task['parent']); echo "\n"; }
			}

		}

		else echo 'error';

	}

	public function index()
	{

	}
	
}

