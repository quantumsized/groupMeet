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
	 * @args    Array - associative array with the start and end time between which to fetch events
	 * @return  Array - an array of whatever the query returns, if anything
	 */
	public function fetchEvents($args) {
		$start = $this->sqlDate($args['start']);
		$end = $this->sqlDate($args['end']);
		$query = "SELECT * FROM ".$this->table." WHERE \"start\">='$start' AND \"end\"<='$end'";
		return $this->doQuery($query);
	}

	/**
	 * Insert / Update Calendar Events in the Database
	 *
	 * @args    Array - associative array of columns (as keys) and column values to put into the database
	 * @return  Array - an array of whatever the query returns, if anything
	 */
	public function updateEvent($args) {
		// pre-set some vars
		$update = [];
		$insert_keys = [];
		$insert_values = [];
		
		// parse some vars so they make the database happy
		if(!empty($args['start']))
			$args['start'] = $this->sqlDate($args['start']);
		if(!empty($args['end']))
			$args['end'] = $this->sqlDate($args['end']);
		$args['allDay'] = ($args['allDay'] == 'false') ? '0' : '1';
		
		// put the vars onto a database-make-happy string
		foreach($args as $key => $value) {
			$update[] = "\"$key\"='$value'";
			$insert_keys[] = "\"$key\"";
			$insert_values[] = "'$value'";
		}
		
		if(empty($args['id'])) {
			$query = "INSERT INTO ".$this->table."(".implode(",", $insert_keys).") VALUES (".implode(",", $insert_values).") RETURNING id";
		} else {
			$query = "UPDATE ".$this->table." SET ".implode(",", $update)." WHERE id = ".$args['id'];
		}
		return $this->doQuery($query);
	}

	/**
	 * Remove calendar event
	 *
	 * @args    Array - associative array of which we only need the id
	 * @return  Array - an array of whatever the query returns, if anything
	 */
	public function removeEvent($args) {
		$query = "DELETE FROM ".$this->table." WHERE id=".$args['id'];
		return $this->doQuery($query);
	}
}
?>