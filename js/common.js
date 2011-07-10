var moveFrom=false;

var TT = new function()
{

	this.ajaxHtml = function(action,params,callback)
	{

		$.get('index.php/ajax/'+action, params, function(data)
		{
			callback(data);
		},'html');

	}

	this.ajax = function(action,params,callback)
	{

		$.get('index.php/ajax/'+action, params, function(data)
		{
			callback(data);
		},'json');

	}

	this.updateTaskList = function() 
	{

		this.ajaxHtml('tasklist',{},function(data) { 
			$('#taskList').html(data);

			$('#taskList tr').hover(
				function() 
				{
					$(this).find('.taskLinks').show();
				},
				function()
				{
					$(this).find('.taskLinks').hide();
				});
		});

	}

	this.startTask = function(id) 
	{

		this.ajaxHtml('start',{'i': id},function(request)
		{

			if (request != 'error') {
				document.getElementById('task'+id+'Clock').style.display='inline';
				TT.viewTaskLog(id);		
			}

		});

	}

	this.endTask = function(id)
	{

		this.ajaxHtml('end',{'i':id},function(request)
		{

			if (request != 'error') {
				document.getElementById('task'+id+'Clock').style.display='none';
				TT.viewTaskLog(id);
			}

		});

	}

	this.viewTaskLog = function(id)
	{

		$('#log tbody').html('');

		this.ajax('log',{'i':id},function(data)
		{
		
			$.each(data.log,function(index,entry)
			{

				var $row = $('#log_row_template tr').parent().clone();
		
				$row.find('tr').attr('data-id',entry.id);	
		
				$row.find('.start').text(entry.start);
				$row.find('.end').text(entry.end);
				if(entry.end_timestamp && entry.start_timestamp) $row.find('.time').text(secsToTime(entry.end_timestamp - entry.start_timestamp));
				$row.find('.notes').text(entry.notes);

				$('#log tbody').append($row.html());

				$row = $('#log tr[data-id='+entry.id+']');

				$row.find('.start').dblclick(function() { TT.updateLogStart(id,entry.id,entry.start); });
				$row.find('.end').dblclick(function() { TT.updateLogEnd(id,entry.id,entry.end); });
				$row.find('.notes').dblclick(function() { TT.updateLogNotes(id,entry.id,entry.notes); });
				$row.find('.delete').click(function() { TT.deleteLog(id,entry.id); });

			});

			$('#log').show();

		});

	}

	this.deleteLog = function(taskid,id)
	{

		if(confirm('Delete this work log row?')) {

			this.ajaxHtml('dellog',{'i':id},function(request)
			{

				if(request != 'error') {

					TT.viewTaskLog(taskid);

				}

			});

		}

	}

	this.newTask = function()
	{
		taskName=prompt('Enter task name:');

		if(taskName) {

			this.ajaxHtml('newtask',{'v':taskName},function(request)
			{
				if(request == 'ok') {

					TT.updateTaskList();

				}
			});

		}
	}

	this.deleteTask = function(id)
	{

		if(confirm('Delete this task?	Subtasks and log entries will also be deleted.')) {

			this.ajaxHtml('deltask',{'i':id},function(request)
			{

				if(request == 'ok') {
					TT.updateTaskList();
				}

			});

		}

	}

	this.moveItemFrom = function(id) 
	{
		moveFrom = id;	
		document.getElementById('msg').innerHTML = 'Move to where?	Click task name, or "task name" in header for top-level.';
	}


	this.moveItemTo = function(id)
	{

		if(moveFrom) {

			document.getElementById('msg').innerHTML='';

			this.ajaxHtml('movetask',{'f':moveFrom,'t':id},function(request)
			{

				if(request == 'ok') {
					TT.updateTaskList();
				}

				moveFrom=false;

			});

		}

	}

	this.updateTaskName = function(id,defaultname)
	{

		newName = prompt('Enter new task name: ',defaultname);

		if(newName) {

			this.ajaxHtml('renametask',{'i':id,'n':newName},function(request)
			{

				if(request == 'ok') {
					document.getElementById('task'+id+'Name').innerHTML=newName;
					document.getElementById('task'+id+'Name').setAttribute('ondblclick','TT.updateTaskName('+id+',"'+addslashes(newName)+'");');
				}

			});

		}

	}

	this.updateLogNotes = function(taskid,id,defaultnotes)
	{

		newNotes = prompt('Enter new notes: ',defaultnotes);

		if(newNotes) {

			this.ajaxHtml('lognotes',{'i':id,'n':newNotes},function(request)
			{

				if(request == 'ok') {
					TT.viewTaskLog(taskid);
				}

			});

		}

	}

	this.updateLogStart = function(taskid,id,defaulttime)
	{

		newTime = prompt('Enter new start time: ',defaulttime);

		if(newTime) {

			this.ajaxHtml('logstart',{'i':id,'t':newTime},function(request)
			{

				if(request != 'error') {

					TT.viewTaskLog(taskid);

				}

			});

		}

	}

	this.updateLogEnd = function(taskid,id,defaulttime)
	{

		newTime = prompt('Enter new end time: ',defaulttime);

		if(newTime) {

			this.ajaxHtml('logend',{'i':id,'t':newTime},function(request)
			{

				if(request != 'error') {

					TT.viewTaskLog(taskid);

				}

			});

		}

	}

}

// str pad with length = 2, fill = '0', left-fill.
function timepad(val)
{

	while(val.toString().length<2) val = '0'+val;
	return val;

}

function secsToTime(d) {

	if(d<0) { var neg = '-'; d*=-1; }
	else var neg = '';

	d = Number(d);
	var h = Math.floor(d / 3600);
	var m = Math.floor(d % 3600 / 60);
	var s = Math.floor(d % 3600 % 60);

	return neg+timepad(h)+':'+timepad(m)+':'+timepad(s);
}




function urlencode(s) {
	s = encodeURIComponent(s);
	return s.replace(/~/g,'%7E').replace(/%20/g,'+');
}

function addslashes(str) {
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}


function initialize() {

	TT.updateTaskList();

}
