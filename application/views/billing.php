<!DOCTYPE html>
<html>
<head>
	<title>phpTimecard Billing</title>
	<link href="<?=base_url()?>css/common.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript">base_url='<?=base_url()?>';</script>
	<script type="text/javascript" src="<?=base_url()?>js/billing.js"></script>
</head>

<body>

<div id="container">

	<h2 id="project_name">phpTimecard v0.3</h2>

	<div id="links_container">
		<a href="<?=base_url()?>">Worklog</a> | Billing | <a href="<?=base_url()?>index.php/stats/daysback/30">Stats</a>
	</div>

	<div id="billing_container">

		<table class="hidden" id="billing_row_template">
		<tr>
			<td class="name"><a class="open-invoices" href="#"></a></td>
			<td class="total_time"></td>
			<td class="rate_actual"></td>
			<td class="total_budget"></td>
			<td class="total_done"></td>
			<td class="total_invoiced"></td>
			<td class="total_unbilled"></td>
			<td class="total_remaining"></td>
			<td class="percent_remaining"></td>
		</tr>
		</table>

		<table class="hidden" id="billing_totals_row_template">
		<tr>
			<td colspan="4" align="right">Totals:</td>  
			<td class="total_done"></td>
			<td class="total_invoiced"></td>
			<td class="total_unbilled"></td>
			<td class="total_remaining"></td>
			<td></td>
		</tr>
		</table>

		<table id="billing">
		<thead>
			<th>Task</th>
			<th>Hours</th>
			<th>Hourly Rate*</th>
			<th>Budget*</th>
			<th>Done</th>
			<th>Invoiced</th>
			<th>Uninvoiced</th>
			<th>Budget Left</th>
			<th>Percent Billed</th>
		</thead>
		<tbody>
		<? /*
		<? foreach($tasks as $task) { ?>
		<tr data-id="<?=$task['id']?>">
			<td class="name"><a class="open-invoices" href="#"><?=htmlspecialchars($task['name'])?></a></td>
			<td class="total_time"><?=secsToTime($task['total_time'])?></td>
			<td class="rate_actual">$<?=money_format('%!i',$task['rate_actual'])?></td>
			<td class="total_budget">$<?=money_format('%!i',$task['total_budget'])?></td>
			<td class="total_done">$<?=money_format('%!i',$task['total_done'])?></td>
			<td class="total_invoiced">$<?=money_format('%!i',$task['total_invoiced'])?></td>
			<td class="total_unbilled">$<?=money_format('%!i', $task['total_done'] - $task['total_invoiced'])?></td>
			<td class="total_remaining">$<?=money_format('%!i',$task['total_budget'] ? $task['total_budget']-$task['total_invoiced'] : 0)?></td>
			<td class="percent_remaining"><?=$task['total_budget'] ? round(($task['total_invoiced'] / $task['total_budget']) * 100, 0) : 0?>%</td>
		</tr>
		<? } ?>
		<tr>
			<td colspan="4" align="right">Totals:</td>  
			<td>$<?=money_format('%!i',$total_done)?></td>
			<td>$<?=money_format('%!i',$total_invoiced)?></td>
			<td>$<?=money_format('%!i',$total_done-$total_invoiced)?></td>
		</tr>
		*/ ?>
		</tbody>
		</table>

	</div>

	<div id="invoices_container" class="hidden">

		<table class="hidden" id="invoices_row_template">
		<tr>
			<td class="number"></td>
			<td class="issued"></td>
			<td class="amount"></td>
			<td class="notes"></td>
			<td class="paid"><img src="<?=base_url()?>img/check.png" alt="Paid" border="0"></td>
			<td class="buttons"><img class="delete" src="<?=base_url()?>img/delete.png" alt="Delete" border="0"></td>
		</tr>
		</table>

		<table id="invoices" class="invoices">
		<thead>
			<th class="number">#</th>
			<th class="issued">Issued</th>
			<th class="amount">Amount</th>
			<th class="notes">Notes</th>
			<th class="paid">Paid</th>
			<th class="buttons">&nbsp;</th>
		</thead>
		<tbody>
		</tbody>
		</table> 

		<div id="newinvoice"><a href="javascript: TI.newInvoice();">New Invoice</a></div>
	</div>

	<div id="hint">
		Double-click starred(*) columns to edit.
	</div>

	<div id="footer"> 
		Copyright &copy; 2011 Ellie Brook Gagnon | <? include('VERSION'); ?> | <a href="http://www.gnu.org/licenses/gpl-3.0.html">GPLv3 License</a> | <a href="http://tango.freedesktop.org/">Tango Colors and Icons</a>
	</div>

</div>

</body>

</html>
