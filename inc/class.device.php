<?php

class device {
	
	private $status_name = array(0=>'Off line', 1=>'Online');

	public $device_id = null;
	public $name = null;
	public $serial_number = null;
	public $status = null;

	public function __construct($line) {
		$this->device_id = $line['device_id'];
		$this->name = $line['name'];
		$this->serial_number = $line['serial_number'];
		$this->status = $line['status'];
	}

	public function get_status_name() {
		return $this->status_name[$this->status];
	}

}

?>