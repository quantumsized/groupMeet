function dbUpdate(data) {
	console.log(data);
	var success = function(data, textStatus, jqXHR) { // callback function
		console.log(data);
	};
	$.ajax({
		type: "POST",
		url: "edit_events.php",
		data: data,
		success: success
	});
}
function formatDate(date) {
	var dateAry = Array(date.getMonth()+1,date.getDate(),date.getFullYear());
	return dateAry.join("/");
}
function sqlTimestamp(time_obj) {
	return time_obj.getTime() / 1000;
}
function formatEvent(event_data) {
	var keys = ['id', 'title', 'start', 'end', 'allDay'];
	var e = {}, k;
	for(var i = 0; i < keys.length; i++) {
		k = keys[i];
		if(typeof(event_data[k]) != undefined)
			e[k] = event_data[k];
		if(e[k] && typeof(e[k].getTime) != 'undefined')
			e[k] = sqlTimestamp(e[k]);
	}
	return e;
}
$(document).ready(function() {
	// $("#event_details #start_date").datepicker();
	// $("#event_details #end_date").datepicker();
	$("#event_details").overlay({ closeOnClick: false });
	var setEvent = function(){
		var event_data = {
			start: new Date($("#event_details #start").val()),
			end: new Date($("#event_details #end").val()),
			title: $("#event_details #event_title").val(),
			allDay: $("#event_details #allday.input:checked") ? true : false
		}
		if (event_data.title) {
			calendar.fullCalendar('renderEvent', 
				event_data,
				true // make the event "stick"
			);
			// console.log(event_data);
			dbUpdate(formatEvent(event_data));
		}
		$("#event_details").overlay().close();
	};
	$("#event_details #set_event").click(setEvent);
	$("#event_details #event_title").keyup(function(e){
		if(e.keyCode == 13) {
			setEvent(e);
		}
	});
	var calendar = $('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek'
		},
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			$("#event_details #start").val(formatDate(start));
			$("#event_details #end").val(formatDate(end));
			$("#event_details").overlay().load();
			$("#event_details #event_title").focus();
			calendar.fullCalendar('unselect');
		},
		editable: true,
		
		events: "get_events.php",
		
		eventDrop: function(event, delta) {
			dbUpdate(formatEvent(event));
		},
		
		eventResize: function(event, delta) {
			dbUpdate(formatEvent(event));
		},
		
		loading: function(bool) {
			if (bool) $('#loading').show();
			else $('#loading').hide();
		},

		eventClick: function(event) {
			// opens events in a popup window
			window.open(event.url, 'width=400,height=280');
			return false;
		}
		
	});
	
});