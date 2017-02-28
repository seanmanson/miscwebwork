<?php
session_start();
date_default_timezone_set("Australia/Brisbane");

//FUNCTIONS
function logMessage($message) {
  file_put_contents("/var/www/htdocs/logs/userlogs.txt", date("Y-m-d H:i:s e: ") . $message . "\n", FILE_APPEND);
}


//PAGE VARS
$loggedIn = false;
$timeBeen = 0;
$timeRemaining = 0;


//Test whether logged in
if (isset($_SESSION['loggedin'])) {
  $loggedIn = true;
  $timeBeen = time() - $_SESSION['logintime'];
  $timeRemaining = $_SESSION['loginduration'] - $timeBeen;
}

//Test whether time has timed out
if ($loggedIn && ($timeRemaining <= 0)) {
  // Log out of session
  logMessage($_SESSION['username'] . ' logged out. (timed out)');
  session_unset();
  session_destroy();
  $loggedIn = false;
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYhCBdhgYx4kwXMzLOlaWxBd18klNFAi4"></script>
    <script src="js/lightbox.min.js"></script>
    <script type="text/javascript">
      var timer;
      var geocoder;
      var map;
      
      // Set up page on startup
      function loadPage() {
        // Set timer
        <?php if ($loggedIn) {echo("timer = $timeRemaining;");} ?>
        <?php if ($loggedIn) {echo("setInterval(updateTimer, 1000);");} ?>
        
        // Make map
        initMap(-27.46, 153.034);
        
        // Get user location, move map if applicable
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(loadedPosition, cantLoadPosition);
        } else {
          $('#currentLocation').html("ERROR: This browser does not support geolocation.");
        }
      }
      
      // Update the timer in the title bar
      function updateTimer() {
        // Get hours, minutes, seconds
        var hours = parseInt(timer / 3600, 10);
        var minutes = parseInt((timer % 3600) / 60, 10);
        var seconds = parseInt(timer % 60, 10);
        
        // Convert to string
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        document.title = "My Restaurant - Time remaining: " + hours + ":" + minutes + ":" + seconds;
        
        if (timer > 0) {
          timer--;
        } else {
          // Go to logout page
          $('#loginLogoutButton').click();
        }
      }
      
      // Creates a new map at the given lat, long coordinates
      function initMap(latitude, longitude) {
        // Set up a geocoder for use elsewhere
        geocoder = new google.maps.Geocoder();
        
        // Create a new map with given map options
        var mapOptions = {
          center: {lat: latitude, lng: longitude},
          zoom: 14
        };
        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        
        // Restaurant markers for this map
        var marker1 = new google.maps.Marker({
            map: map,
            position: {lat: -27.4578, lng: 153.0332},
            title:"King of Kings Seafood Restaurant",
            icon:"http://maps.google.com/mapfiles/markerA.png"
        });
        var marker2 = new google.maps.Marker({
            map: map,
            position: {lat: -27.4589, lng: 153.0358},
            title:"Fat Dumpling",
            icon:"http://maps.google.com/mapfiles/markerB.png"
        });
        var marker3 = new google.maps.Marker({
            map: map,
            position: {lat: -27.4679, lng: 153.022},
            title:"Steamed",
            icon:"http://maps.google.com/mapfiles/markerC.png"
        });
        var marker4 = new google.maps.Marker({
            map: map,
            position: {lat: -27.4711, lng: 153.0241},
            title:"Beijing House",
            icon:"http://maps.google.com/mapfiles/markerD.png"
        });
        var marker5 = new google.maps.Marker({
            map: map,
            position: {lat: -27.4531, lng: 153.0391},
            title:"Gyoza Bar Ann",
            icon:"http://maps.google.com/mapfiles/markerE.png"
        });
      }
      
      // Set up loading the user's current position
      function loadedPosition(position) {
        var latlng = {lat: position.coords.latitude, lng: position.coords.longitude};
        map.panTo(latlng);
        geocoder.geocode({'latLng': latlng}, loadedPositionInfo);
      }
      
      // Error occurs trying to load position
      function cantLoadPosition(err) {
        $('#currentLocation').html("ERROR: Was unable to load your position.");
      }
      
      // Set up loading the info about the user's position
      function loadedPositionInfo(data, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          $('#currentLocation').html("Current location: " + data[2].formatted_address + ".");
        } else {
          $('#currentLocation').html("ERROR: Unable to load position info.");
        }
      }
    </script>
  </head>
  <body onLoad="loadPage()">
    <div class="areaTop">
      <div class="areaTopLeft">
        <a href="prac2.php"><h1>myRestaurant List</h1></a>
      </div>
      <div class="areaTopRight">
        <form action="login.php" method="post" style="float:right">
          <input id="loginLogoutButton" class="button" type="submit" value="<?php if ($loggedIn) {echo('Logout');} else {echo('Login Page');} ?>">
        </form>
      </div>
    </div>
    <div class="areaBottom">
      <div class="areaMain" style="margin-right:400px">
        <div class="areaMainTop">
          <div class="areaMainTopLeft">
            <h2>Locations:</h2>
          </div>
          <div class="areaMainTopRight">
            <?php if ($loggedIn) {echo('Hello ' . $_SESSION['username'] . '.<br />');} else {echo('You are not logged in.<br />');} ?>
            <span id="currentLocation">Loading...</span>
          </div>
        </div>
        <div id="map-canvas"></div>
      </div>
      <div class="areaSide">
        <div class="areaSideTitle">
          <h1>Restaurants:</h1>
        </div>
        <div class="areaSideScroll">
          <div class="restaurantListEntry">
            <div class="restaurantListEntryLeft">
            <img src="http://maps.google.com/mapfiles/markerA.png" alt="A">
            </div>
            <div class="restaurantListEntryMid">
              <div class="restaurantListEntryTitle">King of Kings Seafood Restaurant</div>
              <div class="restaurantListEntryAddress">2F/175 Wickham Street, Fortitude Valley</div>
              <div class="restaurantListEntryPhone">(07) 3852 1889</div>
              <form action="https://plus.google.com/118201221434922262554/about?gl=au">
                <input class="button" type="submit" value="More info">
              </form>
            </div>
            <div class="restaurantListEntryRight">
              <a href="img/1/1.jpg" data-lightbox="1">
                <img width=90 src="img/1/icon.jpg" alt="restaurant icon">
              </a>
              <a href="img/1/2.png" data-lightbox="1"></a>
              <a href="img/1/3.jpg" data-lightbox="1"></a>
            </div>
          </div>
          <div class="restaurantListEntry">
            <div class="restaurantListEntryLeft">
            <img src="http://maps.google.com/mapfiles/markerB.png" alt="B">
            </div>
            <div class="restaurantListEntryMid">
              <div class="restaurantListEntryTitle">Fat Dumpling</div>
              <div class="restaurantListEntryAddress">368 Brunswick Street, Fortitude Valley</div>
              <div class="restaurantListEntryPhone">(07) 3195 1040</div>
              <form action="http://www.fatdumplingbar.com/">
                <input class="button" type="submit" value="More info">
              </form>
            </div>
            <div class="restaurantListEntryRight">
              <a href="img/2/1.jpg" data-lightbox="2">
                <img width=90 src="img/2/icon.jpg" alt="restaurant icon">
              </a>
              <a href="img/2/2.jpg" data-lightbox="2"></a>
              <a href="img/2/3.jpg" data-lightbox="2"></a>
            </div>
          </div>
          <div class="restaurantListEntry">
            <div class="restaurantListEntryLeft">
            <img src="http://maps.google.com/mapfiles/markerC.png" alt="C">
            </div>
            <div class="restaurantListEntryMid">
              <div class="restaurantListEntryTitle">Steamed</div>
              <div class="restaurantListEntryAddress">95 Turbot Street</div>
              <div class="restaurantListEntryPhone">0430 330 623</div>
              <form action="https://www.facebook.com/steamedcatering">
                <input class="button" type="submit" value="More info">
              </form>
            </div>
            <div class="restaurantListEntryRight">
              <a href="img/3/1.jpg" data-lightbox="3">
                <img width=90 src="img/3/icon.jpg" alt="restaurant icon">
              </a>
              <a href="img/3/2.jpg" data-lightbox="3"></a>
            </div>
          </div>
          <div class="restaurantListEntry">
            <div class="restaurantListEntryLeft">
            <img src="http://maps.google.com/mapfiles/markerD.png" alt="D">
            </div>
            <div class="restaurantListEntryMid">
              <div class="restaurantListEntryTitle">Beijing House</div>
              <div class="restaurantListEntryAddress">Queen Street Mall, 1/45 Queen Street</div>
              <div class="restaurantListEntryPhone">(07) 3210 0688</div>
              <form action="https://plus.google.com/102253973225169276239/about?gl=au">
                <input class="button" type="submit" value="More info">
              </form>
            </div>
            <div class="restaurantListEntryRight">
              <a href="img/4/1.jpg" data-lightbox="4">
                <img width=90 src="img/4/icon.jpg" alt="restaurant icon">
              </a>
              <a href="img/4/2.jpg" data-lightbox="4"></a>
            </div>
          </div>
          <div class="restaurantListEntry">
            <div class="restaurantListEntryLeft">
            <img src="http://maps.google.com/mapfiles/markerE.png" alt="E">
            </div>
            <div class="restaurantListEntryMid">
              <div class="restaurantListEntryTitle">Gyoza Bar Ann</div>
              <div class="restaurantListEntryAddress">27/1000 Ann Street, Fortitude Valley</div>
              <div class="restaurantListEntryPhone">(07) 3172 3020</div>
              <form action="http://www.gyozabarann.com.au/">
                <input class="button" type="submit" value="More info">
              </form>
            </div>
            <div class="restaurantListEntryRight">
              <a href="img/5/1.jpg" data-lightbox="5">
                <img width=90 src="img/5/icon.jpg" alt="restaurant icon">
              </a>
              <a href="img/5/2.jpg" data-lightbox="5"></a>
              <a href="img/5/3.jpg" data-lightbox="5"></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>