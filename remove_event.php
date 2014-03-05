<?php
include("db/postgresql.php");
// allowed request keys and their sanitization types
$keys = [
	'id' => FILTER_SANITIZE_NUMBER_INT
];
foreach($keys as $key => $filter) {
	$data[$key] = !empty($_REQUEST[$key]) ? filter_var($_REQUEST[$key], $filter) : '';
}
$db = new DB;
if(extract($data) == count($keys))
	echo json_encode($db->removeEvent($id));
else // this shouldn't happen...
	echo "Error: invalid data!";
