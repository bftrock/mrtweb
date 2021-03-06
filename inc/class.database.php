<?php

require_once ("util.mrt.php");

class database {

	const DB_HOST = "localhost";
	const DB_UNAME = "mrtuser";
	const DB_PWD = "Otis1990!";
	const DB_NAME = "mrt";
	
	public $conn = null;
	public $sql = null;
	public $result = null;

	// Opens a connection to the database and returns a link.
	public function open_connection() {

		$this->conn = new mysqli(self::DB_HOST, self::DB_UNAME, self::DB_PWD, self::DB_NAME)
			or die("Connect Error (" . $this->conn->connect_errno . ") " . $this->conn->connect_error);

	}

	// Executes a query with the passed SQL statement.
	public function execute_query($sql) {

		$this->sql = $sql;
		$this->result = $this->conn->query($sql)
			or die("Could not execute the query: {$this->conn->error}<br />sql = {$sql}");

	}

	// Fetches a sigle row from a result set, and returns an array with field names as keys
	public function fetch_line() {

		$line = $this->result->fetch_assoc();
		return $line;

	}

	// Creates safe sql by inserting escape characters.
	public function safe_sql($str) {

		if (get_magic_quotes_gpc()) {
			$new_str = stripslashes($str);
		} else {
			$new_str = $str;
		}
		return $this->conn->real_escape_string($new_str);

	}

	// Gets the last insert id for an auto-increment field
	public function get_insert_id() {

		return $this->conn->insert_id;

	}

	// Gets the number of rows in the last result set
	public function get_num_rows() {

		return $this->result->num_rows;

	}

}

?>