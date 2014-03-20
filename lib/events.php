<?php
include("../db/mysql.php");
// include("../db/postgresql.php");

$events = new EVENTS;

switch($_REQUEST['action']) {
	case 'get':
		echo json_encode($events->getEvents());
		break;
	case 'insert':
	case 'update':
	case 'edit':
		echo json_encode($events->updateEvent());
		break;
	case 'remove':
	case 'delete':
		echo json_encode($events->removeEvent());
		break;
}

class EVENTS {
	// allowed request keys and their sanitization types
	private $keys = [
		'id'			=> FILTER_SANITIZE_NUMBER_INT,
		'title'		=> FILTER_SANITIZE_STRING,
		'start' 	=> FILTER_SANITIZE_NUMBER_INT,
		'end'			=> FILTER_SANITIZE_NUMBER_INT,
		'allDay'	=> FILTER_SANITIZE_STRING,
		'url'			=> FILTER_SANITIZE_URL
	];
	
	private function filter($raw_input, $select_keys) {
		$output = [];
		$c = count($select_keys);
		foreach($select_keys as $key) {
			if(!isset($this->keys[$key])) continue;
			$filter = $this->keys[$key];
			$output[$key] = !empty($raw_input[$key]) ? filter_var($raw_input[$key], $filter) : '';
		}
		return $output;
	}
	
	public function getEvents() {
		$db = new DB;
		$events = $db->fetchEvents($this->filter($_REQUEST, ['start', 'end']));
		if(empty($events) || (count($events) == 1 && empty($events[0])))
			return "";
		foreach($events as $key => $event) {
			$events[$key]['start'] = strToTime($event['start']);
			if(!empty($event['end'])) $events[$key]['end'] = strToTime($event['end']);
			$events[$key]['allDay'] = $event['allDay'] == 0 ? false : true;
		}
		return $events;
	}

	public function updateEvent() {
		$update_keys = [];
		foreach($this->keys as $key => $value) {
			if(!empty($_REQUEST[$key]))
				$update_keys[] = $key;
		}
		$db = new DB;
		return $db->updateEvent($this->filter($_REQUEST, $update_keys));
	}
	
	public function removeEvent() {
		$db = new DB;
		return $db->removeEvent($this->filter($_REQUEST, ['id']));
	}
}