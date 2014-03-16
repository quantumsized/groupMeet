<?php
$DEBUG = true;

date_default_timezone_set('America/Los_Angeles');
if($DEBUG) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}
/**
 * A class to handle database operations
 *
 * @username  String - the username to connect to the database with
 * @password  String - the password to connect to the database with
 * @host      String - the host to attempt the database connection with
 * @port      String - the port to attempt the database connection with
 * @database  String - the name of the database to use for operations
 * @table     String - the name of the table to use for operations
 * @resource  Object - the internally used postgresql resource object
 */
class DB
{
	private $username = "test";
	private $password = "pass";
	private $host = "localhost";
	private $port = "5432";
	private $database = "test";
	private $table = "calendar";
	private $resource;
	
	/**
	 * Convert a unix timestamp to a postgresql compatable date string
	 *
	 * @unix_timestamp  Integer - a unix timestamp (duh...)
	 * @return          String - a postgresql compatable date string
	 */
	private function sqlDate($unix_timestamp) {
		return date("Y-m-d H:i:s", $unix_timestamp);
	}

	/**
	 * Do the actual database query
	 *
	 * @query   String - the query to execute
	 * @return  Array - and array of whatever the query returns, if anything
	 */
	private function doQuery($query) {
		global $DEBUG;
		$result;
		$conn_str = array(
			"host=$this->host",
			"port=$this->port",
			"dbname=$this->database",
			"user=$this->username",
			"password=$this->password"
		);
		$conn_str = implode(" ",$conn_str);
		if(!$this->resource = @pg_connect($conn_str)) {
			if($DEBUG)
				die(pg_last_error($this->resource));
			else
				die("Unable to connect to database!");
		}
		if(!$result = @pg_query($this->resource, $query)) {
			$err = pg_last_error($this->resource);
			pg_close($this->resource);
			if($DEBUG)
				die($err);
			else
				die("Database update failed!");
		}
		$rows = @pg_fetch_all($result);
		pg_close($this->resource);
		return $rows;
	}

	/**
	 * Fetch events between Start and End dates/times
	 *
	 * @start   Integer - start date to look for events as unix timestamp
	 * @end     Integer - end date to look for events as unix timestamp
	 * @return  Array - an array of whatever the query returns, if anything
	 */
	public function fetchEvents($start, $end) {
		$start = $this->sqlDate($start);
		$end = $this->sqlDate($end);
		$query = "SELECT * FROM ".$this->table." WHERE \"start\">='$start' AND \"end\"<='$end'";
		return $this->doQuery($query);
	}

	/**
	 * Update Calendar Events in the Database
	 *
	 * @id      Integer - id of the event to update if applicable
	 * @title   String - new title of event (max length 255)
	 * @start   Integer - start date of event as unix timestamp
	 * @end     Integer - end date of event, if different then start, as unix timestamp
	 * @allDay  Boolean - does this event run all day, or not? (default: true)
	 * @return  Array - an array of whatever the query returns, if anything
	 */
	public function updateEvent($id, $title, $start, $end, $allDay, $url) {
		// compact() doesn't work here so we have to do it manually :(
		$items = [
			'url' => addslashes($url),
			'title' => $title,
			'start' => $this->sqlDate($start),
			'allDay' => $allDay == 'false' ? '0' : '1'
		];
		if(!empty($end))
			$items['end'] = $this->sqlDate($end);
		$update = [];
		$insert_keys = [];
		$insert_values = [];
		foreach($items as $key => $value) {
			$update[] = "\"$key\"='$value'";
			$insert_keys[] = "\"$key\"";
			$insert_values[] = "'$value'";
		}
		if(empty($id)) {
			$query = "INSERT INTO ".$this->table."(".implode(",", $insert_keys).") VALUES (".implode(",", $insert_values).") RETURNING id";
		} else {
			$query = "UPDATE ".$this->table." SET ".implode(",", $update)." WHERE id = $id";
		}
		return $this->doQuery($query);
	}

	/**
	 * Remove calendar event
	 *
	 * @id      Integer - the id of the event
	 * @return  Array - an array of whatever the query returns, if anything
	 */
	public function removeEvent($id) {
		$query = "DELETE FROM ".$this->table." WHERE id=$id";
		return $this->doQuery($query);
	}
}
?>