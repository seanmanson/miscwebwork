<?php
function connect() {
	$user = "kenichim"
	$pass = "password0!";
	
	try {
		$conn = new PDO ( "sqlsrv:server = tcp:ohqr86ssp1.database.windows.net,1433; Database = seandatabase", $user, $pass);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	} catch ( PDOException $e ) {
		print("Error connecting to SQL Server.");
		die(print_r($e));
	}
	
	return $conn;
}
function markItemComplete($item_id)
{
	$conn = connect();
	$sql = "UPDATE items SET is_complete = 1 WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(1, $item_id);
	$stmt->execute();
}
function getAllItems()
{
	$conn = connect();
	$sql = "SELECT * FROM items";
	$stmt = $conn->query($sql);
	return $stmt->fetchAll(PDO::FETCH_NUM);
}
function addItem($name, $category, $date, $is_complete)
{
	$conn = connect();
	$sql = "INSERT INTO items (name, category, date, is_complete) VALUES (?, ?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(1, $name);
	$stmt->bindValue(2, $category);
	$stmt->bindValue(3, $date);
	$stmt->bindValue(4, $is_complete);
	$stmt->execute();
}
function deleteItem($item_id)
{
	$conn = connect();
	$sql = "DELETE FROM items WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(1, $item_id);
	$stmt->execute();
}
?>