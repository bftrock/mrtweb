<?php

function check_field($field_name, $arr, $is_required = false) {

	if (isset($arr[$field_name]) && strlen($arr[$field_name]) > 0) {
		return true;
	} else {
		if ($is_required) {
			die("Error: the parameter '{$field_name}' is required to be passed but was not.");
		}
		return false;
	}

}

function debug($msg) {

	$h = fopen("log.txt", "a");
	$d = new DateTime();
	$df = $d->format('Y-m-d h:i:s');
	$line = "[{$df}] {$msg}\n";
	fwrite($h, $line);
	fclose($h);
	
}

?>