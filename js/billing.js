
var TI = new function()
{
	this.ajaxHtml = function(action, params, callback)
	{
		$.get(base_url+'index.php/billing/'+action, params, function(data)
		{
			callback(data);
		},'html');
	}

	this.ajax = function(action, params, callback)
	{
		$.get(base_url+'index.php/billing/'+action, params, function(data)
		{
			callback(data);
		},'json');
	}


	this.updateBillingList = function()
	{
		var opened = $('#billing tr.opened').attr('data-id');

		$('#billing tbody').html('');

		this.ajax('billinglist',{},function(data)
		{
		
			$.each(data.tasks,function(index,entry)
			{

				var $row = $('#billing_row_template tr').parent().clone();
		
				$row.find('tr').attr('data-id',entry.id);	
		
				$row.find('.name a').text(entry.name);
				$row.find('.total_time').text(secsToTime(entry.total_time));
				$row.find('.rate_actual').text(formatMoney(entry.rate_actual));
				$row.find('.total_budget').text(formatMoney(entry.total_budget));
				$row.find('.total_done').text(formatMoney(entry.total_done));
				$row.find('.total_invoiced').text(formatMoney(entry.total_invoiced));
				$row.find('.total_unbilled').text(formatMoney(entry.total_done - entry.total_invoiced));
				$row.find('.total_remaining').text(formatMoney(entry.total_budget > 0 ? entry.total_budget - entry.total_invoiced : 0));
				$row.find('.percent_remaining').text((entry.total_budget > 0 ? entry.total_invoiced / entry.total_budget * 100.0 : 0).toFixed(0) + '%');

				$('#billing tbody').append($row.html());

			});

			var $row = $('#billing_totals_row_template tr').parent().clone();

			$row.find('.total_done').text(formatMoney(data.total_done));
			$row.find('.total_invoiced').text(formatMoney(data.total_invoiced));
			$row.find('.total_unbilled').text(formatMoney(data.total_done - data.total_invoiced));
			$row.find('.total_remaining').text(formatMoney(data.total_budget > data.total_invoiced ? data.total_budget - data.total_invoiced : 0));

			$('#billing tbody').append($row.html());

			if (opened) $('#billing tr[data-id="'+opened+'"]').addClass('opened');

		});

	}

	this.editBillingAttribute = function (id, name, value)
	{
		this.ajaxHtml('editbilling',{'i':id,'a':name,'v':value},function(request)
		{
			if(request == 'ok') {
				TI.updateBillingList();
			}
		});
	}

	this.promptBillingAttribute = function(element, name, message)
	{
		var id = rowId(element);
		var value = prompt(message, $(element).text().replace(/^\$/,''));
		if(value) this.editBillingAttribute(id, name, value);
	}


	this.viewInvoices = function(id)
	{
		this.taskid = id;
		this.updateInvoiceList();
	}

	this.updateInvoiceList = function()
	{

		$('#invoices tbody').html('');

		// highlight the task of which we are viewing the invoices of
		$('#billing tr').removeClass('opened');
		$('#billing tr[data-id="'+this.taskid+'"]').addClass('opened');

		this.ajax('invoicelist',{'i':this.taskid},function(data)
		{
		
			$.each(data.invoices,function(index, entry)
			{

				var $row = $('#invoices_row_template tr').parent().clone();
		
				$row.find('tr').attr('data-id', entry.id);	
		
				$row.find('.number').text(entry.number);
				$row.find('.issued').text(entry.issued);
				$row.find('.amount').text(formatMoney(entry.amount));
				$row.find('.notes').text(entry.notes);
				if(entry.paid != 1) $row.find('.paid').html('');

				$('#invoices tbody').append($row.html());

			});

			$('#invoices_container').show();

		});

	}

	this.newInvoice = function()
	{
		invoiceNumber=prompt('Enter invoice number: ');

		if(invoiceNumber) {
			this.ajaxHtml('newinvoice',{'i':this.taskid,'n':invoiceNumber},function(request)
			{
				if(request == 'ok') {
					TI.updateInvoiceList();
				}
			});
		}
	}

	this.deleteInvoice = function(id)
	{
		if(confirm('Delete this invoice?')) {
			this.ajaxHtml('delinvoice',{'i':id},function(request)
			{
				if(request == 'ok') {
					TI.updateInvoiceList();
				}
			});
		}
	}

	this.editInvoiceAttribute = function(id, name, value)
	{
		this.ajaxHtml('editinvoice',{'i':id,'a':name,'v':value},function(request)
		{
			if(request == 'ok') {
				TI.updateInvoiceList();
			}
		});
	}

	this.promptInvoiceAttribute = function(element, name, message)
	{
		var id = rowId(element);
		var value = prompt(message, $(element).text().replace(/^\$/,''));
		if(value) this.editInvoiceAttribute(id, name, value);
	}

}


function timepad(val)
{
	while(val.toString().length<2) val = '0'+val;
	return val;
}

function secsToTime(d)
{
	if(d<0) { var neg = '-'; d*=-1; }
	else var neg = '';

	d = Number(d);
	var h = Math.floor(d / 3600);
	var m = Math.floor(d % 3600 / 60);
	var s = Math.floor(d % 3600 % 60);

	return neg+timepad(h)+':'+timepad(m)+':'+timepad(s);
}

function formatMoney(a)
{
	return '$'+Number(a).toFixed(2);
}

function rowId(element)
{
	return $(element).closest('tr').attr('data-id');
}

$(document).ready(function()
{
	var $billing = $('#billing');

	$billing.on('click', 'a.open-invoices', function() { TI.viewInvoices(rowId(this)); });

	$billing.on('dblclick', 'td.rate_actual', function () { TI.promptBillingAttribute(this, 'rate_actual', 'Enter new hourly rate: '); });
	$billing.on('dblclick', 'td.total_budget', function () { TI.promptBillingAttribute(this, 'total_budget', 'Enter new budget: '); });


	var $invoices = $('#invoices');

	$invoices.on('dblclick', '.number', function() { TI.promptInvoiceAttribute(this, 'number', 'Enter new invoice number: '); });
	$invoices.on('dblclick', '.issued', function() { TI.promptInvoiceAttribute(this, 'issued', 'Enter new issued date: '); });
	$invoices.on('dblclick', '.amount', function() { TI.promptInvoiceAttribute(this, 'amount', 'Enter new invoice amount: '); });
	$invoices.on('dblclick', '.notes', function() { TI.promptInvoiceAttribute(this, 'notes', 'Enter new notes: '); });
	$invoices.on('dblclick', '.paid', function() { TI.editInvoiceAttribute(rowId(this), 'paid', $(this).html() ? 0 : 1); });
	$invoices.on('dblclick', '.delete', function() { TI.deleteInvoice(rowId(this)); });

	TI.updateBillingList();
});
