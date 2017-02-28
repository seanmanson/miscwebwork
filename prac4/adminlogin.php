<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
	header('Location: admin.php');
}

$username = $_POST['username'];
$password = $_POST['password'];
$loginFailed = false;
if (isset($_POST['username']) && isset($_POST['password'])) {
  if ($username == 'admin' && $password == 'password') {
    $_SESSION['loggedin'] = true;
	$_SESSION['username'] = $username;
    header('Location: admin.php');
  } else {
    $loginFailed = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My Restaurant - Admin Login</title>
    <link rel="stylesheet" href="prac4style.css" />
    <link href="css/lightbox.css" rel="stylesheet" />
    <script src="js/jquery-1.11.0.min.js"></script>
  </head>
  <body>
    <div class="areaTop">
      <div class="areaTopLeft">
        <a href="prac4.php"><h1>myRestaurant List</h1></a>
      </div>
      <div class="areaTopRight">
      	<form action="admin.php" method="post">
          <input class="button" type="submit" value="Admin page" style="margin-top:1px">
        </form>
      </div>
      <div class="areaTopRight">
      	<form class="searchForm" action="search.php" method="get">
          <input name="name" type="text" style="border-radius: 3px 0 0 3px;" placeholder="Name">
          <input name="address" type="text" placeholder="Address">
          <input name="phone" type="text" placeholder="Phone">
          <button type="submit">Search</button>
        </form>
      </div>
    </div>
    <div class="areaBottom">
      <div class="areaMain">
        <p id="loginError" class="error"><?php if ($loginFailed) echo "Incorrect username/password." ?></p>
        <div class="areaMainLogin">
          <h2>Admin Login Page</h2>
          <form action="adminlogin.php" method="post">
            <div class="areaMainLoginEntry"><input class="textinput" type="text" name="username" placeholder="Username"></div>
            <div class="areaMainLoginEntry"><input class="textinput" type="password" name="password" placeholder="Password"></div>
            <div class="areaMainLoginEntry"><input class="button" type="submit" value="Login"></div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>