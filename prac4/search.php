<?php
include("mysqllogin.php");
session_start();

// Connect to database
$db = new MySQLLogin();
$db->connect("infs", "3202", "restaurants");

// Get search terms
$name = $_GET['name'];
$address = $_GET['address'];
$phone = $_GET['phone'];

// Get relevant restaurant info
$query = "SELECT * FROM RestaurantInfo WHERE ";
$query .= "Name LIKE '%" . $name . "%' AND ";
$query .= "Address LIKE '%" . $address . "%' AND ";
$query .= "Phone LIKE '%" . $phone . "%'";
$resinfo = $db->query($query);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My Restaurant</title>
    <link rel="stylesheet" href="prac4style.css" />
    <link href="css/lightbox.css" rel="stylesheet" />
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYhCBdhgYx4kwXMzLOlaWxBd18klNFAi4"></script>
    <script src="js/lightbox.min.js"></script>
    <script type="text/javascript">
      var geocoder;
      var map;
      
      // Set up page on startup
      function loadPage() {
        // Setup more info buttons
        $(".moreInfoButton").click(function() {
          $(this).prev().slideToggle();
          if ($(this).text() == "More info") {
            $(this).text("Less info");
          } else {
            $(this).text("More info");
          }
        });
	  
        // Make map
        initMap(-27.46, 153.034);
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
		<?php
		// Print a marker for each row in the results
		$letter = 'A';
		while ($row = mysqli_fetch_array($resinfo)) {
			echo 'new google.maps.Marker({
            map: map,
            position: {lat:' . $row['Latitude'] . ', lng: ' . $row['Longitude'] . '},
            title: "' . $row['Name'] . '",
            icon: "http://maps.google.com/mapfiles/marker' . $letter . '.png" });';
			$letter++;
		}
		?>
      }
    </script>
  </head>
  <body onLoad="loadPage()">
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
          <input name="name" type="text" style="border-radius: 3px 0 0 3px;" placeholder="Name" <?php if (isset($name)) { echo 'value="' . $name . '"'; }?>>
          <input name="address" type="text" placeholder="Address" <?php if (isset($address)) { echo 'value="' . $address . '"'; }?>>
          <input name="phone" type="text" placeholder="Phone" <?php if (isset($phone)) { echo 'value="' . $phone . '"'; }?>>
          <button type="submit">Search</button>
        </form>
      </div>
    </div>
    <div class="areaBottom">
      <div class="areaMain" style="margin-right:400px">
        <div class="areaMainTop">
          <div class="areaMainTopLeft">
            <h2>Locations:</h2>
          </div>
        </div>
        <div id="map-canvas"></div>
      </div>
      <div class="areaSide">
        <div class="areaSideTitle">
          <h1>Restaurants:</h1>
        </div>
        <div class="areaSideScroll">
		  <?php
		  $i = 0;
		  $letter = 'A';
		  mysqli_data_seek($resinfo, 0);
		  while ($row = mysqli_fetch_array($resinfo)) {
			  $resEntry = "";
			  $resEntry .= '<div class="restaurantListEntry">';
			  
			  // Marker
			  $resEntry .= '<div class="restaurantListEntryLeft"><img src="http://maps.google.com/mapfiles/marker' . $letter . '.png" alt="' . $letter . '"></div>';
			  
			  // Main info Section
			  $resEntry .= '<div class="restaurantListEntryMid">';
			  $resEntry .= '<div class="restaurantListEntryTitle">' . $row['Name'] . '</div>';
			  $resEntry .= '<div class="restaurantListEntryAddress">' . $row['Address'] . '</div>';
			  $resEntry .= '<div class="restaurantListEntryPhone">' . $row['Phone'] . '</div>';
			  $resEntry .= '<div class="restaurantListEntryMoreContent">' . $row['Description'] . '</div>';
			  $resEntry .= '<button class="button moreInfoButton" type="button">More info</button>';
			  $resEntry .= '</div>';
			  
			  // Pictures section
			  $resEntry .= '<div class="restaurantListEntryRight">';
			  $imgLinks = str_split($row['Images']);
			  $j = 0;
			  foreach($imgLinks as $imgLink) {
				  $resEntry .= '<a href="' . $imgLink . '" data-lightbox="' . $i . '">';
				  if ($j == 0)
				  	  $resEntry .= '<img width=90 src="' . $row['Icon'] . '" alt="restaurant icon">';
				  $resEntry .= '</a>';
				  $j++;
			  }
			  $resEntry .= '</div>';
			  
			  $resEntry .= '</div>';
			  
			  echo $resEntry;
			  
			  $i++;
			  $letter++;
		  }
		  
		  if ($i == 0) {
			  echo "<div class='restaurantListEntry'>Sorry, no results found!</div>";
		  }
		  ?>
      	</div>
      </div>
    </div>
  </body>
</html>