<?php

class Billing extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('billingdb');
		$this->load->helper('time');
	}

	public function index()
	{
		redirect('billing/summary');
	}

	public function summary()
	{
		$this->load->view('billing');
	}

	public function invoices($id)
	{
		$this->load->view('invoices', array('taskid'=>$id));
	}


	/// Ajax Requests ///

	public function billinglist()
	{
		$tasks=$this->billingdb->getTaskList(0);
		$total_invoiced=$this->billingdb->totalTaskInvoiced(0);

		$total_done=0;
		$total_budget=0;
		foreach($tasks as $task) {
			$total_budget += $task['total_budget'];
			if($task['parent']==0)
				$total_done += $task['total_done'];
		}

		echo json_encode(array('tasks'=>$tasks,'total_budget'=>$total_budget,'total_done'=>$total_done,'total_invoiced'=>$total_invoiced));
	}

	public function editbilling()
	{
		$attribs = array('rate_actual', 'total_budget');

		if(isset($_GET['i'])) $id=$_GET['i'];
		if(isset($_GET['a'])) $name=$_GET['a'];
		if(isset($_GET['v'])) $value=$_GET['v'];
		if(!isset($id) || !isset($name) || !in_array($name,$attribs) || !isset($value)) die('error');

		if($this->billingdb->editTaskAttribute($id,$name,$value)) echo 'ok';
		else echo 'error';
	}


	public function invoicelist()
	{
		if(!empty($_GET['i'])) $id=$_GET['i'];
		if(empty($id)) die('error');

		$invoices=$this->billingdb->getInvoiceList($id);
		echo json_encode(array('invoices'=>$invoices));
	}

	public function newinvoice()
	{
		if(!empty($_GET['i'])) $id=$_GET['i'];
		if(!empty($_GET['n'])) $number=$_GET['n'];
		if(empty($id) || empty($number)) die('error');
		
		if($this->billingdb->newInvoice($id,$number)) echo 'ok';
		else echo 'error';
	}

	public function delinvoice()
	{
		if(!empty($_GET['i'])) $id=$_GET['i'];
		else die('error');

		if($this->billingdb->delInvoice($id)) echo 'ok';
		else echo 'error';	
	}

	public function editinvoice()
	{
		$attribs = array('number', 'issued', 'amount', 'notes', 'paid');

		if(isset($_GET['i'])) $id=$_GET['i'];
		if(isset($_GET['a'])) $name=$_GET['a'];
		if(isset($_GET['v'])) $value=$_GET['v'];
		if(!isset($id) || !isset($name) || !in_array($name,$attribs) || !isset($value)) die('error');

		if($this->billingdb->editInvoiceAttribute($id,$name,$value)) echo 'ok';
		else echo 'error';
	}

}

