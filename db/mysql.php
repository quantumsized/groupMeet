<?php
date_default_timezone_set('America/Los_Angeles');
/**
 * A class to handle database operations
 *
 * @username  String - the username to connect to the database with
 * @password  String - the password to connect to the database with
 * @host      String - the host to attempt the database connection with
 * @database  String - the name of the database to use for operations
 * @table     String - the name of the table to use for operations
 * @resource  Object - the internally used mysql resource object
 */
class DB
{
	private $username = "test";
	private $password = "pass";
	private $host = "localhost";
	private $database = "test";
	private $table = "calendar";
	private $resource;
	
	/**
	 * Convert a unix timestamp to a mysql compatable date string
	 *
	 * @unix_timestamp  Integer - a unix timestamp (duh...)
	 * @return          String - a mysql compatable date string
	 */
	private function mysql_date($unix_timestamp) {
		return date("Y-m-d H:i:s", $unix_timestamp);
	}

	/**
	 * Do the actual database query
	 *
	 * @query   String - the query to execute
	 * @return  Array - and array of whatever the query returns, if anything
	 */
	private function doQuery($query) {
		$result;
		if(!$this->resource = @mysql_connect($this->host, $this->username, $this->password)) {
			die("Unable to connect to database!");
		}
		if(!@mysql_select_db($this->database, $this->resource)) {
			mysql_close($this->resource);
			die("Unable to select database!");
		}
		if(!$result = @mysql_query($query, $this->resource)) {
			mysql_close($this->resource);
			die("Database update failed!");
		}
		$rows = array();
		while($rows[] = @mysql_fetch_assoc($result)) {}
		mysql_close($this->resource);
		return $rows;
	}

	/**
	 * Fetch events between Start and End dates/times
	 *
	 * @start   Integer - start date to look for events as unix timestamp
	 * @end     Integer - end date to look for events as unix timestamp
	 * @return  Array - and array of whatever the query returns, if anything
	 */
	public function fetchEvents($start, $end) {
		$start = $this->mysql_date($start);
		$end = $this->mysql_date($end);
		$query = "SELECT * FROM ".$this->table." WHERE start>='$start' AND end<='$end'";
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
	 * @return  Array - and array of whatever the query returns, if anything
	 */
	public function updateEvent($id, $title, $start, $end, $allDay) {
		$start = $this->mysql_date($start);
		$end = $this->mysql_date($end);
		$allDay = $allDay == 'false' ? '0' : '1';
		// compact() doesn't work here so we have to do it manually :(
		$items = ['title' => $title, 'start' => $start, 'end' => $end, 'allDay' => $allDay];
		$update = [];
		$insert_keys = [];
		$insert_values = [];
		foreach($items as $key => $value) {
			$update[] = "$key='$value'";
			$insert_keys[] = "$key";
			$insert_values[] = "'$value'";
		}
		if(empty($id)) {
			$query = "INSERT INTO ".$this->table."(".implode(",", $insert_keys).") VALUES (".implode(",", $insert_values).")";
		} else {
			$query = "UPDATE ".$this->table." SET ".implode(",", $update)." WHERE id = $id";
		}
		return $this->doQuery($query);
	}
}
?>