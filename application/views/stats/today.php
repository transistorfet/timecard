<!DOCTYPE html>
<html>
<head>
	<title>phpTimecard Stats</title>
	<link href="<?=base_url()?>css/common.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>js/common.js"></script>
</head>

<body>

<div id="container">

	<table border="1" cellspacing="0" cellpadding="4">
	<thead>
		<th>Notes</th>
		<th>Rate Actual Hourly</th>
		<th>Rate Estimated Hourly</th>
		<th>Rate Potential Hourly</th>
		<th>Hours</th>
		<th>Earned Actual</th>
		<th>Earned Estimated</th>
		<th>Earned Potential</th>
	</thead>
	<tbody>
	<? foreach($rows as $row) { ?>
	<tr>
		<td><?=htmlspecialchars($row['notes'])?></td>
		<td>$<?=money_format('%!i',$row['rate_actual_hourly'])?></td>
		<td>$<?=money_format('%!i',$row['rate_estimated_hourly'])?></td>
		<td>$<?=money_format('%!i',$row['rate_potential_hourly'])?></td>
		<td><?=round($row['hours'],2)?></td>
		<td>$<?=money_format('%!i',$row['rate_actual'])?></td>
		<td>$<?=money_format('%!i',$row['rate_estimated'])?></td>
		<td>$<?=money_format('%!i',$row['rate_potential'])?></td>
	</tr>
	<? } ?>
	<tr>
		<td colspan="4" align="right">Totals:</td>  
	  <td><?=round($total_hours,2)?></td>
		<td>$<?=money_format('%!i',$total_rate_actual)?></td>
		<td>$<?=money_format('%!i',$total_rate_estimated)?></td>
		<td>$<?=money_format('%!i',$total_rate_potential)?></td>
	</tr>
	<tr>
		<td colspan="7" align="right"><strong>Grand Total:</td>
		<td>$<?=money_format('%!i',$total_rate_actual + $total_rate_estimated + $total_rate_potential)?></td>
	</tr>
	</tbody>
	</table>

</div>

</body>
</html>
