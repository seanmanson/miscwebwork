<?php
class MySQLLogin {
	var $ctn;
	
	function connect($user, $password, $database) {
		$this->ctn = mysqli_connect('localhost', $user, $password);
		if (!$this->ctn) {
			die("Connection Failed: " . mysqli_error($this->ctn));
		}
		
		$db = mysqli_select_db($this->ctn, $database);
		if (!$db) {
			die("Connection Failed: " . mysqli_error($this->ctn));
		}
		
		return $this->ctn;
	}
	
	function query($query) {
		$result = mysqli_query($this->ctn, $query);
		if (!$result) {
			die(mysqli_error($this->ctn));
		}
		
		return $result;
	}
	
	function disconnect() {
		mysqli_close($this->ctn);
	}
}
?>