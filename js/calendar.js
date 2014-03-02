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
$(document).ready(function() {
	// $("#event_details #start_date").datepicker();
	// $("#event_details #end_date").datepicker();
	$("#event_details").overlay({ closeOnClick: false });
	var setEvent = function(){
			var start = new Date($("#event_details #start").val());
			var end = new Date($("#event_details #end").val());
			var title = $("#event_details #event_title").val();
			var allDay = true;
			if (title) {
				calendar.fullCalendar('renderEvent', {
						title: title,
						start: start,
						end: end,
						allDay: allDay
					},
					true // make the event "stick"
				);
				var event_data = {
					title: title,
					start: start.getTime() / 1000,
					end: end.getTime() / 1000,
					allDay: allDay
				}
				// console.log(event_data);
				dbUpdate(event_data);
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
			alert(event.title + ' was moved ' + delta + ' days\n');
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