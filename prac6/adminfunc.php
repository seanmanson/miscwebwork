<?php
// Function defs
function connect() {
	$user = "kenichim";
	$pass = "password0!";
	
	try {
		$conn = new PDO ( "sqlsrv:server = tcp:ohqr86ssp1.database.windows.net,1433; Database = seandatabase", $user, $pass);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	} catch ( PDOException $e ) {
		echo "Error connecting to SQL Server.";
		echo $e;
	}
	
	return $conn;
}

function createComments() {
	$conn = connect();

	$sql = "CREATE TABLE comments(
			id INT NOT NULL, 
			PRIMARY KEY(id),
			placeid VARCHAR(50),
			commentname VARCHAR(30),
			commenttext VARCHAR(200))";
	try{
		$conn->query($sql);
	}
	catch(Exception $e){
		print_r($e);
	}
}

function getComments($placeid) {
	$conn = connect();
	
	$sql = "SELECT * FROM comments WHERE placeid = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(1, $placeid);
	$stmt->execute();

	while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
		echo $row[2] . '|';
		echo $row[3] . '|';
	}
}

function saveComment($placeid, $commentname, $commenttext) {
	$conn = connect();
	
	// First get max id to use
	$stmt = $conn->query("SELECT MAX(id) FROM comments");
	$maxRow = $stmt->fetch(PDO::FETCH_NUM);
	$max = intval($maxRow[0]) + 1;
	
	$sql = "INSERT INTO comments (id, placeid, commentname, commenttext) VALUES (?, ?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(1, $max);
	$stmt->bindValue(2, $placeid);
	$stmt->bindValue(3, $commentname);
	$stmt->bindValue(4, $commenttext);
	$stmt->execute();
}

// See what request we have
$requestType = $_POST['type'];
if (isset($_POST['type'])) {
	if ($requestType == 'getcomments') {
		echo getComments($_POST['placeid']);
	} else if ($requestType == 'savecomment') {
		saveComment($_POST['placeid'], $_POST['commentname'], $_POST['commenttext']);
	}
}
?>