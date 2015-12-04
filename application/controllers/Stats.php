<?

class Stats extends CI_Controller
{

	public function index()
	{
		redirect('stats/daysback/30');
	}

	public function daysback($days_back=0)
	{

		$rate_actual_cache = array();
		$rate_estimated_cache = array();
		$rate_potential_cache = array();

		// TODO - timezone setting required.  assuming PT for now.
 		date_default_timezone_set('America/Vancouver');

		$start_time = date('Y-m-d',strtotime('-'.$days_back.' days')).' 00:00:00';
		$end_time = date('Y-m-d').' 23:59:59';

		$query = $this->db->query('select *,UNIX_TIMESTAMP(start) as start_timestamp, UNIX_TIMESTAMP(end) as end_timestamp from worklog where start >= '.$this->db->escape($start_time).' and start <= '.$this->db->escape($end_time));

		$rows=array();

		// TODO rate classes should be a setting... able to add/remove rate classes to display.
		$total_rate_actual = 0.00;
		$total_rate_estimated = 0.00;
		$total_rate_potential = 0.00;

		$total_hours = 0.00;

		// TODO this could probably be made more efficient.
		foreach($query->result() as $entry)
		{

			if(!isset($rate_cache[$entry->taskid])) 
			{

				$this->db->where('id',$entry->taskid);
				$rate_query = $this->db->get('tasks');
				$rate_result = current($rate_query->result());
			
				// TODO - null means "check parent", 0 means 0... >0 means rate.  consider each rate class independantly.
				while($rate_result->rate_actual==null && $rate_result->rate_estimated==null && $rate_result->rate_potential==null && $rate_result->parent!=0)
				{

					$this->db->where('id',$rate_result->parent);
					$rate_query = $this->db->get('tasks');
					$rate_result = current($rate_query->result());

				}

				$rate_actual_cache[$entry->taskid]=$rate_result->rate_actual;
				$rate_estimated_cache[$entry->taskid]=$rate_result->rate_estimated;
				$rate_potential_cache[$entry->taskid]=$rate_result->rate_potential;

			}

			$num_hours = ($entry->end_timestamp - $entry->start_timestamp)/3600;

			$rate_actual = $rate_actual_cache[$entry->taskid]*$num_hours;
			$rate_estimated = $rate_estimated_cache[$entry->taskid]*$num_hours;
			$rate_potential = $rate_potential_cache[$entry->taskid]*$num_hours;

			$rows[] = array(
				'taskid'=>$entry->taskid,
				'notes'=>$entry->notes,
				'rate_actual_hourly'=>$rate_actual_cache[$entry->taskid],
				'rate_potential_hourly'=>$rate_potential_cache[$entry->taskid],
				'rate_estimated_hourly'=>$rate_estimated_cache[$entry->taskid],
				'hours'=>$num_hours,
				'rate_actual'=>$rate_actual,
				'rate_potential'=>$rate_potential,
				'rate_estimated'=>$rate_estimated
			);

			$total_hours += $num_hours;
			$total_rate_actual += $rate_actual;
			$total_rate_estimated += $rate_estimated;
			$total_rate_potential += $rate_potential;

		}

		$this->load->view('stats/today',array('days_back'=>$days_back,'total_hours'=>$total_hours,'rows'=>$rows,'total_rate_actual'=>$total_rate_actual,'total_rate_estimated'=>$total_rate_estimated,'total_rate_potential'=>$total_rate_potential));

	}

	public function editstats()
	{
		$this->load->model('billingdb');

		$attribs = array('rate_actual', 'rate_estimated', 'rate_potential');

		if(isset($_GET['i'])) $id=$_GET['i'];
		if(isset($_GET['a'])) $name=$_GET['a'];
		if(isset($_GET['v'])) $value=$_GET['v'];
		if(!isset($id) || !isset($name) || !in_array($name,$attribs) || !isset($value)) die('error');

		if($this->billingdb->editTaskAttribute($id,$name,$value)) echo 'ok';
		else echo 'error';
	}

}
	
