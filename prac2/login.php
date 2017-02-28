<?php
session_start();
date_default_timezone_set("Australia/Brisbane");

//FUNCTIONS
function logMessage($message) {
  file_put_contents("/var/www/htdocs/logs/userlogs.txt", date("Y-m-d H:i:s e: ") . $message . "\n", FILE_APPEND);
}

function correctLoginPostData() {
  return (($_POST['username'] == 'INFS' || $_POST['username'] == 'infs') &&
          $_POST['password'] == '3202' &&
          ($_POST['time'] == '10s' || $_POST['time'] == '1d'));
}

//PAGE VARS
$errorMsg = "";
$loggedIn = false;


// First check if we are logged in
if (isset($_SESSION['loggedin'])) {
  // Log out; determine whether they timed out
  $timeBeen = time() - $_SESSION['logintime'];
  $timeRemaining = $_SESSION['loginduration'] - $timeBeen;
  if ($timeRemaining > 0) {
    logMessage($_SESSION['username'] . ' logged out. (disconnect by user)');
  } else {
    logMessage($_SESSION['username'] . ' logged out. (timed out)');
  }
  
  // Destroy their session
  session_unset();
  session_destroy();
  $errorMsg .= "You have been logged out.";
} else if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['time'])){
  if (correctLoginPostData()) {
    // Set login info
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];
    $_SESSION['logintime'] = time();
    if ($_POST['time'] == '10s') {
      $_SESSION['loginduration'] = 10;
    } else if ($_POST['time'] == '1d') {
      $_SESSION['loginduration'] = 60*60*24;
    }
    
    // Say they logged in
    logMessage($_SESSION['username'] . ' logged in.');
    
    // Change page
    header('Location: prac2.php');
  } else {
    $errorMsg .= "Incorrect username/password";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My Restaurant</title>
    <link rel="stylesheet" href="prac2style.css" />
    <link href="css/lightbox.css" rel="stylesheet" />
    <script src="js/jquery-1.11.0.min.js"></script>
  </head>
  <body>
    <div class="areaTop">
      <div class="areaTopLeft">
        <a href="prac2.php"><h1>myRestaurant List</h1></a>
      </div>
      <div class="areaTopRight">
        <form action="login.php" method="post" style="float:right">
          <input class="button" type="submit" value="Login Page">
        </form>
      </div>
    </div>
    <div class="areaBottom">
      <div class="areaMain">
        <p id="loginError" class="error"><?php echo($errorMsg); ?></p>
        <div class="areaMainLogin">
          <h2>Login Page</h2>
          <form action="login.php" method="post">
            <div class="areaMainLoginEntry"><input class="textinput" type="text" name="username" placeholder="Username"></div>
            <div class="areaMainLoginEntry"><input class="textinput" type="password" name="password" placeholder="Password"></div>
            <div class="areaMainLoginEntry">
              Login for:
              <select name="time" style="float:right">
                <option value="10s">10 seconds</option>
                <option value="1d">1 day</option>
              </select>
            </div>
            <div class="areaMainLoginEntry"><input class="button" type="submit" value="Login"></div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>