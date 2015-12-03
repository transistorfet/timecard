<?

class Billingdb extends CI_Model
{
	function getTaskList($id)
	{
		if (!isset($this->timedb))
			$this->load->model('timedb');

		$tasks = $this->timedb->taskList($id);

		$return = array();
		if($tasks) {
			foreach($tasks as $task) {
				$entry = array();
				$entry['id'] = $task['id'];
				$entry['parent'] = $task['parent'];
				$entry['name'] = htmlspecialchars($task['name']);
				$entry['rate_actual'] = $task['rate_actual'];
				$entry['total_budget'] = $task['total_budget'];
				$entry['total_time'] = $this->timedb->taskTotalTime($task['id']);
				$entry['total_done'] = $entry['total_time'] / 3600 * $task['rate_actual'];
				$entry['total_invoiced'] = $this->billingdb->totalTaskInvoiced($task['id']);
				$return[] = $entry;
				$return = array_merge($return, $this->getTaskList($task['id']));
			}
		}
		return $return;
	}

	function totalTaskInvoiced($id)
	{
		$total = 0;

		$total = $total + $this->taskInvoiced($id);

		// determine child tasks to add into time.
		$query = $this->db->query('SELECT id FROM tasks WHERE parent=? AND archived=0', array($id));
		foreach ($query->result_array() as $nfo) $total = $total + $this->totalTaskInvoiced($nfo['id']);

		return $total;
	}

	function taskInvoiced($id)
	{
		$query = $this->db->query('SELECT SUM(amount) AS amount FROM invoices WHERE taskid=?', array($id));
		$nfo = $query->row_array();

		return $nfo['amount'];
	}

	function editTaskAttribute($id,$name,$value)
	{
		$this->db->where('id', $id);
		$this->db->update('tasks', array($name => $value));
		if ($this->db->affected_rows()>0) return TRUE;
		else return FALSE;
	}


	function getInvoiceList($id)
	{
		$query = $this->db->query('SELECT * FROM invoices WHERE taskid=? ORDER BY issued', array($id));

		echo $this->db->error()['message'];

		foreach ($query->result_array() as $nfo) {
			//$startedResult = $this->db->query('SELECT * FROM worklog WHERE end IS NULL AND taskid=? LIMIT 1', array($nfo['id']));
			//if ($startedResult->num_rows()>0) $nfo['status'] = 'started';
			//else $nfo['status'] = '';
			$return[] = $nfo;
		}

		if (empty($return)) return array();
		else return $return;
	}

	function newInvoice($id,$number)
	{
		$query = $this->db->query('INSERT INTO invoices SET taskid=?, number=?, issued=CURDATE()', array($id,$number));
		return TRUE;
	}

	function delInvoice($id)
	{
		$this->db->query('DELETE FROM invoices WHERE id=?', array($id));
		return TRUE;
	}

	function editInvoiceAttribute($id,$name,$value)
	{
		$this->db->where('id', $id);
		$this->db->update('invoices', array($name => $value));
		if ($this->db->affected_rows() > 0) return TRUE;
		else return FALSE;
	}

}
