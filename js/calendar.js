var event_pages = {
	get:		"lib/events.php?action=get",
	edit:		"lib/events.php?action=update",
	remove: "lib/events.php?action=delete"
};

// calendar trashcan
var trash = {
	id: "calendarTrash",
	selector: "#", // the 'id' is added to this later to make it actually useful
	class: "calendar-trash",
	image: "images/trashcan.png"
};

// These are the only keys let throught the filter. Add more keys here if needed
var event_keys = ['id', 'title', 'start', 'end', 'allDay', 'url'];

// these are the tags for the overlay for creating and updating events
var event_update_overlay = {
	container:	"#event_details",
	start_date:	"#start_date",
	start_time:	"#start_time",
	start:			(function() { return new Date($(this.start_date).val() + " " + $(this.start_time).val()) }),
	end_date:		"#end_date",
	end_time:		"#end_time",
	end:				(function() { return new Date($(this.end_date).val() + " " + $(this.end_time).val()) }),
	title:			"#event_title",
	allday:			"input#allday",
	allDay:			(function() { return $(this.allday + ":checked").length > 0; }),
	url:				"#event_url",
	submit:			"#set_event"
};
var event_info_overlay = {
	container:	"#event_info",
	title:			".event_title",
	start:			".start",
	end:				".end",
	link:				"a.link"
};

$(document).ready(function() {
	// initialize (aka 'fix') the trash selector
	trash.selector += trash.id;
	
	// initialize (aka 'fix') some object variables
	initOverlayVar(event_update_overlay);
	initOverlayVar(event_info_overlay);
	
	// initialize overlays
	$(event_update_overlay.container).overlay({ closeOnClick: false });
	$(event_info_overlay.container).overlay({ closeOnClick: false });
	
	// add a few things to the onchange function with the allday checkbox
	$(event_update_overlay.allday).change(function(e) {
		var hide = $(event_update_overlay.allday + ":checked").length > 0;
		hideTimeInput(event_update_overlay.start_time, hide);
		hideTimeInput(event_update_overlay.end_time, hide);
	});
	
	// create a 'setEvent()' function
	var setEvent = function() {
		var event_data = {};
		for(var key in event_update_overlay) {
			event_data[key] = typeof(event_update_overlay[key]) == 'function' ? event_update_overlay[key]() : $(event_update_overlay[key]).val();
		}
		if(event_data.title) {
			dbUpdate(formatEvent(event_data), function(data, textStatus, jqXHR) {
				data = JSON.parse(data);
				if(data && data[0] && data[0]['id']) {
					event_data.id = data[0]['id'];
					calendar.fullCalendar('renderEvent', event_data, true);
				}
			});
		}
		$(event_update_overlay.container).overlay().close();
	};
	
	// bind the 'setEvent()' function to things that make it useful ;)
	$(event_update_overlay.submit).click(setEvent);
	for(key in event_update_overlay) {
		if(typeof(event_update_overlay[key]) == 'string'
			&& $(event_update_overlay[key]).attr('type') == 'text'
		)
		bindEnter(event_update_overlay[key], setEvent);
	}
});

function initCalendar() {
	calendar = $('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek'
		},
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			calendar.fullCalendar('unselect');
		},
		
		events: event_pages.get,
		timeFormat: 'h:mm tt{ - h:mm tt}',
		
		loading: function(bool) {
			if (bool) {
				$('#loading').show();
			} else {
				$('#loading').hide();
			}
		},

		eventClick: function(event) {
			$(event_info_overlay.title).html(event.title);
			$(event_info_overlay.start).html(event.allDay ? formatDate(event.start) : formatDateTime(event.start));
			if(event.end == null)
				$(event_info_overlay.end).html("All Day");
			else
				$(event_info_overlay.end).html(event.allDay ? formatDate(event.end) : formatDateTime(event.end));
			if(event.url) {
				$(event_info_overlay.link).parent().show();
				$(event_info_overlay.link).html(event.url);
				$(event_info_overlay.link).attr("href", event.url);
			} else {
				$(event_info_overlay.link).parent().hide();
			}
			$(event_info_overlay.container).overlay().load();
			return false;
		},

		dragRevertDuration: 200
	});
}

// This calendar does not support changing most values after initialization
// so we need a seperate function for an editable version :(
function initEditableCalendar() {
	calendar = $('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek'
		},
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			$(event_update_overlay.start_date).val(formatDate(start));
			$(event_update_overlay.start_time).removeAttr('data-oldval');
			$(event_update_overlay.start_time).val(formatTime(start));
			$(event_update_overlay.end_date).val(formatDate(end));
			$(event_update_overlay.end_time).removeAttr('data-oldval');
			$(event_update_overlay.end_time).val(formatTime(end));
			$(event_update_overlay.title).val('');
			$(event_update_overlay.allday).prop('checked', allDay);
			hideTimeInput(event_update_overlay.start_time, allDay);
			hideTimeInput(event_update_overlay.end_time, allDay);
			$(event_update_overlay.container).overlay().load();
			$(event_update_overlay.title).focus();
			calendar.fullCalendar('unselect');
		},
		editable: true,
		
		events: event_pages.get,
		timeFormat: 'h:mm tt{ - h:mm tt}',
		eventDrop: function(event, delta) {
			dbUpdate(formatEvent(event));
		},
		
		eventResize: function(event, delta) {
			dbUpdate(formatEvent(event));
		},
		
		loading: function(bool) {
			if (bool) {
				$('#loading').show();
			} else {
				$('#loading').hide();
				$('.fc-header-left>:first-child').before('<div id="'+trash.id+'" class="'+trash.class+'"><img src="'+trash.image+'"></img></div>');
			}
		},

		eventClick: function(event) {
			$(event_info_overlay.title).html(event.title);
			$(event_info_overlay.start).html(event.allDay ? formatDate(event.start) : formatDateTime(event.start));
			if(event.end == null)
				$(event_info_overlay.end).html("All Day");
			else
				$(event_info_overlay.end).html(event.allDay ? formatDate(event.end) : formatDateTime(event.end));
			if(event.url) {
				$(event_info_overlay.link).parent().show();
				$(event_info_overlay.link).html(event.url);
				$(event_info_overlay.link).attr("href", event.url);
			} else {
				$(event_info_overlay.link).parent().hide();
			}
			$(event_info_overlay.container).overlay().load();
			return false;
		},

		dragRevertDuration: 200,

		eventMouseover: function (event, jsEvent) {
			$(this).mousemove(function (e) {
				var trashEl = $(trash.selector);
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
			if (isOverElement(trash.selector, {x:jsEvent.pageX,y:jsEvent.pageY})) {
				var confirm_delete = confirm("Delete Event?");
				if(confirm_delete) {
					$.ajax({
						type: "POST",
						url: event_pages.remove,
						data: { id: event.id },
						success: function(data, textStatus, jqXHR) {
							return function(event_id) {
								calendar.fullCalendar('removeEvents', event_id);
							}(event.id);
						}
					});
				}
				var trashEl = $(trash.selector);
				if (trashEl.hasClass("to-trash")) {
					trashEl.removeClass("to-trash");
				}
			}
		}
		
	});
}

/* library type functions */
function bindEnter(html_obj, callback) {
	$(html_obj).keyup(function(e) {
		if(e.keyCode == 13) {
			callback(e);
		}
	});
}
function initOverlayVar(obj) {
	var c = obj.container;
	for(key in obj) {
		if(typeof(obj[key]) == 'string' && obj[key] != c)
			obj[key] = c + " " + obj[key];
	}
}
function dbUpdate(data, success) {
	if(!success) {
		success = function(data, textStatus, jqXHR) {};
	}
	$.ajax({
		type: "POST",
		url: event_pages.edit,
		data: data,
		success: success
	});
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
function formatEvent(event_data) {
	var e = {}, k;
	for(var i = 0; i < event_keys.length; i++) {
		k = event_keys[i];
		if(typeof(event_data[k]) != 'undefined')
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
function formatDateTime(date_obj) {
	return formatDate(date_obj) + " " + formatTime(date_obj);
}
function zeroPad(number) {
	if(parseFloat(number) < 10)
		number = "0" + number;
	return number;
}
function sqlTimestamp(time_obj) {
	return time_obj.getTime() / 1000;
}