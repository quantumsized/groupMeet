<?php
include("db/postgresql.php");
$db = new DB;
$events = $db->fetchEvents($_REQUEST['start'], $_REQUEST['end']);
foreach($events as $key => $event) {
	$events[$key]['start'] = strToTime($event['start']);
	if(!empty($event['end'])) $events[$key]['end'] = strToTime($event['end']);
	$events[$key]['allDay'] = $event['allDay'] == 0 ? false : true;
	$events[$key]['url'] = stripslashes($event['url']);
}
echo json_encode($events);
?>