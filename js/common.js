var moveFrom=false;

var TT = new function()
{

	this.ajax = function(action,params,callback)
	{

		$.get('index.php/ajax/'+action, params, function(data)
		{
			callback(data);
		},'html');

	}

	this.updateTaskList = function() 
	{

		this.ajax('tasklist',{},function(data) { 
			$('#taskList').html(data);
		});

	}

	this.startTask = function(id) 
	{

		this.ajax('start',{'i': id},function(request)
		{

			if (request != 'error') {

				document.getElementById('task'+id+'Clock').style.display='inline';

				logRow = document.getElementById('task'+id+'LogRow');
				logTableBody = document.getElementById('task'+id+'LogTableBody');

				// only update if visible.
				if(logRow.style.display == 'table-row') {
		
					update_info=request.split(",");
					var row = document.createElement("tr");
					var td_start = document.createElement("td");
					var td_end = document.createElement("td");
					var td_time = document.createElement("td");
					var td_notes = document.createElement("td");
					var td_actions = document.createElement("td");

					row.id='log'+update_info[0];
		
					td_start.id='log'+update_info[0]+'Start';
					td_end.id='log'+update_info[0]+'End';
					td_time.id='log'+update_info[0]+'Time';
					td_notes.id='log'+update_info[0]+'Notes';
					td_actions.id='log'+update_info[0]+'Actions';

					td_start.className='logDateTime';
					td_end.className='logDateTime';
					td_time.className='logTime';
					td_notes.className='logNotes';
					td_actions.className='logActions';

					td_start.setAttribute('ondblclick','TT.updateLogStart('+id+','+update_info[0]+',"'+update_info[1]+'");');
					td_notes.setAttribute('ondblclick','TT.updateLogNotes('+id+','+update_info[0]+',"");');

					td_start.innerHTML=update_info[1];
					td_actions.innerHTML='<a href="javascript: TT.deleteLog('+update_info[0]+');"><img src="img/delete.png" alt="Delete" border="0" /></a>';
				 
					row.appendChild(td_start);
					row.appendChild(td_end);
					row.appendChild(td_time);
					row.appendChild(td_notes);
					row.appendChild(td_actions);

					logTableBody.appendChild(row);

				}

			}

		});

	}

	this.endTask = function(id)
	{

		this.ajax('end',{'i':id},function(request)
		{

			if (request != 'error') {
				document.getElementById('task'+id+'Clock').style.display='none';
				update_rows = request.split("\n");

				endData=update_rows.shift();

				// update end date.
				logRow = document.getElementById('task'+id+'LogRow');
				if(logRow.style.display == 'table-row') {
					endDataArray=endData.split(",");
					document.getElementById('log'+endDataArray[0]+'End').innerHTML=endDataArray[1];
					document.getElementById('log'+endDataArray[0]+'End').setAttribute('ondblclick','TT.updateLogEnd('+id+','+endDataArray[0]+',"'+endDataArray[1]+'");');
					document.getElementById('log'+endDataArray[0]+'Time').innerHTML=endDataArray[2];
				}

				// update times of this task and parent tasks.
				for ( var i in update_rows ) {

					items = update_rows[i].split(",");

					document.getElementById('task'+items[0]+'Time').innerHTML=items[1];
					document.getElementById('task'+items[0]+'Total').innerHTML=items[2];

				}
			}

		});

	}

	this.toggleTaskLog = function(id)
	{

		logRow = document.getElementById('task'+id+'LogRow');

		// hide row.
		if(logRow.style.display == 'table-row') {
			logRow.style.display = 'none';
		}

		// show row, then update row.
		else {
			logRow.style.display = 'table-row';
			this.updateTaskLog(id);
		}

	}

	this.updateTaskLog = function(id)
	{
		logRow = document.getElementById('task'+id+'LogRow');
		logArea = document.getElementById('task'+id+'LogArea');

		// don't bother updating if it's not being displayed anyway.	(will update when display requested).
		if(logRow.style.display == 'table-row') {
				this.ajax('log',{'i':id},function(data)
				{
					logArea.innerHTML=data;
				});
		}
	}

	this.deleteLog = function(id)
	{

		if(confirm('Delete this work log row?')) {

			this.ajax('dellog',{'i':id},function(request)
			{

				if(request != 'error') {

					document.getElementById('log'+id).style.display='none';

					update_rows = request.split("\n");

					for ( var i in update_rows ) {

			items = update_rows[i].split(",");

			document.getElementById('task'+items[0]+'Time').innerHTML=items[1];
			document.getElementById('task'+items[0]+'Total').innerHTML=items[2];

					}

				}

			});

		}

	}

	this.newTask = function()
	{
		taskName=prompt('Enter task name:');

		if(taskName) {

			this.ajax('newtask',{'v':taskName},function(request)
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

			this.ajax('deltask',{'i':id},function(request)
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

			this.ajax('movetask',{'f':moveFrom,'t':id},function(request)
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

			this.ajax('renametask',{'i':id,'n':newName},function(request)
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

			this.ajax('lognotes',{'i':id,'n':newNotes},function(request)
			{

				if(request == 'ok') {
					document.getElementById('log'+id+'Notes').innerHTML=newNotes;
					document.getElementById('log'+id+'Notes').setAttribute('ondblclick','TT.updateLogNotes('+taskid+','+id+',"'+addslashes(newNotes)+'");');
				}

			});

		}

	}

	this.updateLogStart = function(taskid,id,defaulttime)
	{

		newTime = prompt('Enter new start time: ',defaulttime);

		if(newTime) {

			this.ajax('logstart',{'i':id,'t':newTime},function(request)
			{

				if(request != 'error') {

					update_rows = request.split("\n");
					update_log_entry=update_rows.shift();

					update_info=update_log_entry.split(',');
					update_start=update_info[0];
					update_time=update_info[1];

					start_td=document.getElementById('log'+id+'Start');
					time_td=document.getElementById('log'+id+'Time');
					end_td=document.getElementById('log'+id+'End');

					start_td.innerHTML=update_start;
					document.getElementById('log'+id+'Start').setAttribute('ondblclick','TT.updateLogStart('+taskid+','+id+',"'+update_start+'");');

					if(end_td.innerHTML!='') {
			time_td.innerHTML=update_time;
					}

					for ( var i in update_rows ) {

			items = update_rows[i].split(",");

			document.getElementById('task'+items[0]+'Time').innerHTML=items[1];
			document.getElementById('task'+items[0]+'Total').innerHTML=items[2];

					}

				}

			});

		}

	}

	this.updateLogEnd = function(taskid,id,defaulttime)
	{

		newTime = prompt('Enter new end time: ',defaulttime);

		if(newTime) {

			this.ajax('logend',{'i':id,'t':newTime},function(request)
			{

				if(request != 'error') {

					update_rows = request.split("\n");
					update_log_entry=update_rows.shift();

					update_info=update_log_entry.split(',');
					update_end=update_info[0];
					update_time=update_info[1];

					end_td=document.getElementById('log'+id+'End');
					time_td=document.getElementById('log'+id+'Time')
		
					end_td.innerHTML=update_end;
					end_td.setAttribute('ondblclick','TT.updateLogEnd('+taskid+','+id+',"'+update_end+'");');

					time_td.innerHTML=update_time;

					for ( var i in update_rows ) {

			items = update_rows[i].split(",");

			document.getElementById('task'+items[0]+'Time').innerHTML=items[1];
			document.getElementById('task'+items[0]+'Total').innerHTML=items[2];

					}

				}

			});

		}

	}

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
