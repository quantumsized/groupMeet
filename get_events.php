<?php
include("db/sql.php");
$db = new DB;
$events = $db->fetchEvents($_REQUEST['start'], $_REQUEST['end']);
echo json_encode($events);
