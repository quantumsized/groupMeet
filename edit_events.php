<?php
include("db/sql.php");
// allowed request keys and their sanitization types
$keys = [
	'id' => FILTER_SANITIZE_NUMBER_INT,
	'title' => FILTER_SANITIZE_STRING,
	'start' => FILTER_SANITIZE_NUMBER_INT,
	'end' => FILTER_SANITIZE_NUMBER_INT,
	'allDay' => FILTER_SANITIZE_STRING
];
print_r($keys);
foreach($keys as $key => $filter) {
	$data[$key] = !empty($_REQUEST[$key]) ? filter_var($_REQUEST[$key], $filter) : '';
}
$db = new DB;
if(extract($data) == count($keys))
	print_r( $db->updateEvent($id, $title, $start, $end, $allDay) );
else // this shouldn't happen...
	echo "Error: invalid data!";
