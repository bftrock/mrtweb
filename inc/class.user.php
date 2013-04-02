<?php

require_once ("class.device.php");
require_once ("util.mrt.php");

class user {
	
	const LOAD_DB = 1;
	const LOAD_POST = 2;
	const LOAD_NEW = 3;

	private $db = null;

	public $user_id = null;
	public $name = null;
	public $email = null;
	public $password = null;
	public $in_session = false;
	public $devices = array();

	public function __construct($db) {
		$this->db = $db;
	}

	public function check_session() {
		if (isset($_SESSION['user_id'])) {
			$this->user_id = $_SESSION['user_id'];
			$this->in_session = true;
		}
		debug("In session = {$this->in_session}");
	}
	
	// This function loads citizen properties if a user is logged in.
	public function load($source) {
		
		switch ($source) {
			case self::LOAD_DB:
				debug("Loading user from database.");
				$sql = "SELECT * FROM users WHERE user_id = '{$this->user_id}'";
				$this->db->execute_query($sql);
				$line = $this->db->fetch_line();
				$this->name = $line['name'];
				$this->email = $line['email'];
				break;
			case self::LOAD_POST:
				debug("Loading user from POST.");
				$this->id = $_POST['user_id'];
				$this->name = $_POST['name'];
				$this->email = $_POST['email'];
				$this->password = $_POST['password'];
				break;
			case self::LOAD_NEW:
			default:
		}
	}
		
	public function insert() {
		
		$sql = "INSERT users SET " . $this->get_sql();
		$this->db->execute_query($sql);
		
		// Get the id of the last insert and store it in the id property.
		$this->user_id = $this->db->get_insert_id();
		
	}
	
	public function update() {
		
		$sql = "UPDATE users SET " . $this->get_sql() . " WHERE user_id = '{$this->user_id}'";
		$this->db->execute_query($sql);

	}
	
	private function get_sql() {

		$sql = "password = SHA1('{$this->password}'),email = '" . $this->db->safe_sql($this->email) . "',name = '" . $this->db->safe_sql($this->name) . "'";
		return $sql;
	
	}
	
	// Checks to see if submitted email address is available.
	public function email_available() {
		
		$result = false;
		$email = $_POST['email'];
		$sql = "SELECT COUNT(*) cnt FROM users WHERE email = '{$email}'";
		$result = $this->db->execute_query($sql);
		$line = $this->db->fetch_line($result);
		if ($line['cnt'] == 0) {
			$result = true;
		}
		return $result;
		
	}

	public function verify_password($password) {

		$ret = false;
		$sql = "SELECT password db_pwd, SHA1('{$password}') pwd FROM users WHERE user_id = '{$this->user_id}'";
		$this->db->execute_query($sql);
		$line = $this->db->fetch_line();
		debug("Old password = {$line['db_pwd']}, new passord = {$line['pwd']}");
		if ($line['db_pwd'] == $line['pwd']) {
			$ret = true;
		}
		return $ret;

	}

	public function get_devices() {

		$sql = "SELECT d.* 
			FROM user_device ud 
			LEFT JOIN devices d ON ud.device_id = d.device_id 
			WHERE ud.user_id = '{$this->user_id}'";
		$this->db->execute_query($sql);
		while ($line = $this->db->fetch_line()) {
			$device = new device($line);
			$this->devices[] = $device;
		}

	}
	
}

?>