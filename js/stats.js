
var TS = new function()
{
	this.ajaxHtml = function(action, params, callback)
	{
		$.get(base_url+'index.php/stats/'+action, params, function(data)
		{
			callback(data);
		},'html');
	}

	this.promptStatsAttribute = function (element, name, message)
	{
		var id = $(element).closest('tr').attr('data-id');
		var value = prompt(message, $(element).text().replace(/^\$/,''));
		this.ajaxHtml('editstats',{'i':id,'a':name,'v':value},function(request)
		{
			if(request == 'ok') {
				window.location.reload();
			}
		});
	}

}

$(document).ready(function()
{

	$('#days_back').change(function () {
		window.location = base_url+'index.php/stats/daysback/'+$(this).val();
	});

	$('#stats td.rate_actual').dblclick(function () { TS.promptStatsAttribute(this, 'rate_actual', 'Enter new actual hourly rate: '); });
	$('#stats td.rate_estimated').dblclick(function () { TS.promptStatsAttribute(this, 'rate_estimated', 'Enter new estimated hourly rate: '); });
	$('#stats td.rate_potential').dblclick(function () { TS.promptStatsAttribute(this, 'rate_potential', 'Enter new potential hourly rate: '); });

});
