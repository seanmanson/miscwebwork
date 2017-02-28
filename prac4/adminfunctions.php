<?php
include("mysqllogin.php");
session_start();

// Function defs
function getInfo($db, $id) {
	$result = $db->query("SELECT * FROM RestaurantInfo WHERE ID = '" . $id . "'");
	while ($row = mysqli_fetch_array($result)) {
		echo $row['ID'] . '|';
		echo $row['Name'] . '|';
		echo $row['Address'] . '|';
		echo $row['Phone'] . '|';
		echo $row['Icon'] . '|';
		echo $row['Images'] . '|';
		echo $row['Latitude'] . '|';
		echo $row['Longitude'] . '|';
		echo $row['Description'];
	}
}

function setInfo($db, $id, $title, $address, $phone, $icon, $images, $lat, $long, $desc) {
	// First see how many results there are
	$countSQL = $db->query("SELECT COUNT(*) FROM RestaurantInfo");
	$countRow = mysqli_fetch_row($countSQL);
	$count = intval($countRow[0]);
	
	// If id is within this, then update otherwise insert
	if (intval($id) < $count) {
		$query = "UPDATE RestaurantInfo SET Name = '". $title ."', Address = '". $address ."', Phone = '". $phone ."', Icon = '". $icon ."', Images = '". $images ."', Latitude = '". $lat ."', Longitude = '". $long ."', Description = '". $desc ."' WHERE ID = '". $id ."'";
	} else {
		$query = "INSERT INTO RestaurantInfo VALUES ('". $id ."', '". $title ."', '". $address ."', '". $phone ."', '". $icon ."', '". $images ."', '". $lat ."', '". $long ."', '". $desc ."')";
	}
	
	$db->query($query);
}

function deleteInfo($db, $id) {
	$db->query("DELETE FROM RestaurantInfo WHERE ID = '" . $id . "'");
}

// Connect to database
$db = new MySQLLogin();
$db->connect("infs", "3202", "restaurants");

// See what request we have
$requestType = $_POST['type'];
if (isset($_POST['type'])) {
	if ($requestType == 'getinfo') {
		echo getInfo($db, $_POST['id']);
	} else if ($requestType == 'setinfo') {
		setInfo($db, $_POST['id'], $_POST['title'], $_POST['address'], $_POST['phone'], $_POST['icon'], $_POST['images'], $_POST['latitude'], $_POST['longitude'], $_POST['desc']);
	} else if ($requestType == 'deleteinfo') {
		deleteInfo($db, $_POST['id']);
	}
}
?>