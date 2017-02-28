<%@ page import="java.io.*" %>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My Restaurant</title>
    <link rel="stylesheet" href="prac3style.css" />
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
        
        // Get user location, move map if applicable
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(loadedPosition, cantLoadPosition);
        } else {
          $('#currentLocation').html("ERROR: This browser does not support geolocation.");
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
        <a href="prac3.jsp"><h1>myRestaurant List</h1></a>
      </div>
      <div class="areaTopRight">
        <form action="admin.jsp" method="post" style="float:right">
          <input class="button" type="submit" value="Go to admin page">
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
            <span id="currentLocation">Loading...</span>
          </div>
        </div>
        <div id="map-canvas"></div>
      </div>
      <div class="areaSide">
        <div class="areaSideTitle">
          <h1>Restaurants:</h1>
        </div>
        <div class="areaSideScroll"><%
          BufferedReader br = new BufferedReader(new FileReader(application.getRealPath("/") + "prac3/resinfo.txt"));
          
          int i = 1;
          char letter = 'A';
          while (true) {
            String title = br.readLine();
            if (title == null || title.equals("")) {
              break;
            }
            String address = br.readLine();
            String phone = br.readLine();
            String icon = br.readLine();
            String imgList = br.readLine();
            String[] imgs = imgList.split("#");
            String content = br.readLine();
            br.readLine();
        %><div class="restaurantListEntry">
            <div class="restaurantListEntryLeft">
              <%= "<img src='http://maps.google.com/mapfiles/marker" + letter + ".png' alt='" + letter + "'>" %>
            </div>
            <div class="restaurantListEntryMid">
              <div class="restaurantListEntryTitle"><%= title %></div>
              <div class="restaurantListEntryAddress"><%= address %></div>
              <div class="restaurantListEntryPhone"><%= phone %></div>
              <div class="restaurantListEntryMoreContent"><%= content %></div>
              <button class="button moreInfoButton" type="button">More info</button>
            </div>
            <div class="restaurantListEntryRight"><%
              int j = 0;
              for (String img : imgs) { %>
                <a href="<%= img %>" data-lightbox="<%= i %>">
                  <%= (j == 0) ? "<img width=90 src='" + icon + "' alt='restaurant icon'>" : "" %>
                </a>
              <%j++;
              }
          %></div>
          </div><%
            i++;
            letter++;
          }
          
          br.close();
          %>
        </div>
      </div>
    </div>
  </body>
</html>