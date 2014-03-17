function dbUpdate(data, success) {
// 	console.log(data);
	if(!success) {
		success = function(data, textStatus, jqXHR) { // callback function
// 			console.log(data);
		};
	}
	$.ajax({
		type: "POST",
		url: "edit_event.php",
		data: data,
		success: success
	});
}
function formatDate(date) {
	var dateAry = Array(date.getMonth()+1,date.getDate(),date.getFullYear());
	for(var i = 0; i < dateAry.length; i++) dateAry[i] = zeroPad(dateAry[i]);
	return dateAry.join("/");
}
function formatTime(time) {
	var timeAry = Array(time.getHours(),time.getMinutes(),time.getSeconds());
	for(var i = 0; i < timeAry.length; i++) timeAry[i] = zeroPad(timeAry[i]);
	return timeAry.join(":");
}
function zeroPad(number) {
	if(parseFloat(number) < 10)
		number = "0" + number;
	return number;
}
function hideTimeInput(input, hide) {
	if(hide) {
		$(input).hide();
		$(input).attr('data-oldval', $(input).val());
		$(input).val('00:00:00');
	} else {
		var v = $(input).attr('data-oldval');
		if(v) {
			$(input).val(v);
			$(input).removeAttr('data-oldval');
		}
		$(input).show();
	}
}
function sqlTimestamp(time_obj) {
	return time_obj.getTime() / 1000;
}
function formatEvent(event_data) {
	var keys = ['id', 'title', 'start', 'end', 'allDay', 'url'];
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
function isOverElement(element, coords) {
	var e = $(element);
	var o = e.offset();
	var r = false;
	var bb = { // element bounding box
		top: o.top,
		left: o.left,
		bottom: o.top + e.outerHeight(true),
		right: o.left + e.outerWidth(true)
	};
	var r = (
		coords.x >= bb.left
		&& coords.x <= bb.right
		&& coords.y >= bb.top
		&& coords.y <= bb.bottom
	);
	return r;
}
$(document).ready(function() {
	// $("#event_details #start_date").datepicker();
	// $("#event_details #end_date").datepicker();
	$("#event_details #allday").change(function(e) {
		var event_start = "#event_details #start_time";
		var event_end = "#event_details #end_time";
		var hide = $("#event_details input#allday:checked").length > 0;
		hideTimeInput(event_start, hide);
		hideTimeInput(event_end, hide);
	});
	$("#event_info").overlay({ closeOnClick: false });
	$("#event_details").overlay({ closeOnClick: false });
	var setEvent = function(){
		var event_data = {
			url: $("#event_details #event_link").val(),
			start: new Date($("#event_details #start_date").val() + " " + $("#event_details #start_time").val()),
			end: new Date($("#event_details #end_date").val() + " " + $("#event_details #end_time").val()),
			title: $("#event_details #event_title").val(),
			allDay: $("#event_details input#allday:checked").length > 0
		}
		if (event_data.title) {
			dbUpdate(formatEvent(event_data), function(data, textStatus, jqXHR) {
				data = JSON.parse(data);
				if(data && data[0] && data[0]['id']) {
					event_data.id = data[0]['id'];
					calendar.fullCalendar('renderEvent', event_data, true);
				}
			});
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
			$("#event_details #start_date").val(formatDate(start));
			$("#event_details #start_time").removeAttr('data-oldval');
			$("#event_details #start_time").val(formatTime(start));
			$("#event_details #end_date").val(formatDate(end));
			$("#event_details #end_time").removeAttr('data-oldval');
			$("#event_details #end_time").val(formatTime(end));
			$("#event_details #event_title").val('');
			$("#event_details #allday").prop('checked', allDay);
			hideTimeInput("#event_details #start_time", allDay);
			hideTimeInput("#event_details #end_time", allDay);
			$("#event_details").overlay().load();
			$("#event_details #event_title").focus();
			calendar.fullCalendar('unselect');
		},
		editable: true,
		
		events: "get_events.php",
		timeFormat: 'h:mm tt { - h:mm tt} ',
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
				$("#event_info .title").html(event.title);
				$("#event_info .url").html(event.url);
				$("#event_info").overlay().load();
				return false;
			
		},

		dragRevertDuration: 200,

		eventMouseover: function (event, jsEvent) {
			$(this).mousemove(function (e) {
				var trashEl = $('#calendarTrash');
				if(isOverElement(trashEl, {x:e.pageX,y:e.pageY})) {
					if (!trashEl.hasClass("to-trash")) {
						trashEl.addClass("to-trash");
					}
				} else {
					if (trashEl.hasClass("to-trash")) {
						trashEl.removeClass("to-trash");
					}

				}
			});
		},

		eventDragStop: function (event, jsEvent, ui, view) {
			if (isOverElement('#calendarTrash', {x:jsEvent.pageX,y:jsEvent.pageY})) {
				var confirm_delete = confirm("Delete Event?");
				if(confirm_delete) {
					$.ajax({
						type: "POST",
						url: "remove_event.php",
						data: { id: event.id },
						success: function(data, textStatus, jqXHR) {
							return function(event_id) {
								calendar.fullCalendar('removeEvents', event_id);
							}(event.id);
						}
					});
				}
				var trashEl = $('#calendarTrash');
				if (trashEl.hasClass("to-trash")) {
					trashEl.removeClass("to-trash");
				}
			}
		}
		
	});
	$('.fc-header-left>:first-child').before('<div id="calendarTrash" class="calendar-trash"><img src="images/trashcan.png"></img></div>');
});