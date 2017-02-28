<?php
//AUTO REFRESH
$url = $_SERVER['REQUEST_URI'];
header("Refresh: 2; URL=$url");

//GET VISITS
$file = "counts.html";
//create
if ( is_file( $file )==false ) {
	touch($file);
	$open = fopen($file, "w");
	fwrite($open, "0");
	fclose($open);
}
//count
$open = fopen($file, "r");
$visits = fread($open, filesize($file));
fclose($open);
//save
$open = fopen($file, "w");
$visits++;
fwrite($open, $visits);
fclose($open);

//GET TIME
date_default_timezone_set("Australia/Brisbane"); 
$time = date('h:i:s A, l, j F Y');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Welcome</title>
	</head>
	<body>
		<p>Hello, and welcome to sean's page!!!. This page auto refreshes every 2 seconds.</p>
    <h1>Total visits: <?php echo $visits; ?></h1>
    <p>current time: <?php echo $time; ?></p>
	</body>
</html>