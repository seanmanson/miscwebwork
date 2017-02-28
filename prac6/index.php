<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>myRestaurant</title>
		<link rel="stylesheet" href="prac5style.css" />
		<link href="css/lightbox.css" rel="stylesheet" />
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDYhCBdhgYx4kwXMzLOlaWxBd18klNFAi4"></script>
		<script src="js/lightbox.min.js"></script>
		<script type="text/javascript">
			var searchTerm;
			var geocoder;
			var placeService;
			var map;
			var markers;
			var showingComments;
			var placeCurrentlyShown;
			
			// Set up page on startup
			function loadPage() {
				// Get search results
				searchTerm = $('#searchVal').val();
				if (searchTerm == "") {
					searchTerm = 'restaurant';
				}
				showingComments = false;
				
				// Make map with arbitrary position
				initMap(-27.46, 153.034);
			}
			
			// Creates a new map at the given lat, long coordinates
			function initMap(latitude, longitude) {
				// Set up a geocoder for use elsewhere
				geocoder = new google.maps.Geocoder();
				markers = [];
				
				// Create a new map with given map options
				var mapOptions = {
					center: {lat: latitude, lng: longitude},
					zoom: 15
				};
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				placeService = new google.maps.places.PlacesService(map);
		
				// Tell map to update on zoom or move where marker disappears from bounds
				google.maps.event.addListener(map, 'zoom_changed', reloadMap);
				google.maps.event.addListener(map, 'dragend', reloadMap);
				setTimeout(reloadMap, 3000);
			}
			
			// Reload map on search
			function updateSearch() {
				// Get search results
				searchTerm = $('#searchVal').val();
				
				// Reload
				reloadMap();
			}
			
			// Update places visible on map when moved
			function reloadMap() {
				// Clear all markers
				for (var i = 0; i < markers.length; i++) {
					markers[i].setMap(null);
				}
				
				// Move side bar if showing comments
				if (showingComments) {
					$(".areaSide").animate({width:'toggle'}, 500, function() {
							reloadMap2();
						});
				} else {
					reloadMap2();
				}
			}
			
			function reloadMap2() {
				if (showingComments) {
					showingComments = false;
					
					// Set up area for searching
					$(".areaSideScrollTitle").html("<h1>Restaurants:</h1>");
					$(".areaSideScroll").css("bottom", "0px");
					$(".areaSideBottom").css("height", "0px");
					
					$(".areaSide").animate({width:'toggle'}, 500);
				}
				
				// Loading icon
				$('.areaSideScrollContent').html('<img style="margin-left:30px" src="img/load.gif" alt="loading...">');
				
				// Get places
				var request = {
						bounds: map.getBounds(),
						query: searchTerm,
						types: ['restaurant'],
					};
				placeService.textSearch(request, onPlacesLoaded);
			}
			
			// Got places; update screen to include them
			function onPlacesLoaded(results, status) {
				if (status != google.maps.places.PlacesServiceStatus.OK) {
					if (status == google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
						results = [];
					} else if (status == google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT) {
						$('.areaSideScrollContent').html('<div class="restaurantListEntry">Server too busy; try again later.</div>');
						return;
					} else {
						window.alert("Failed to load place information: status " + status);
						return;
					}
				}
				
				// Remove places not in bounded area
				results = orderedResultsInAreaWithCentre(results, map.getBounds(), map.getCenter());
				
				// Update side bar
				updateSideBar(results);
				
				// Update map icons
				updateMarkers(results);
			}
			
			// Update the side bar to contain place info
			function updateSideBar(results) {
				var resultsHtml = "";
				
				// No results message
				if (results.length == 0) {
					resultsHtml += '<div class="restaurantListEntry">Sorry, no results found!</div>';
				}
				
				// Update with search results
				var letter = 'A';
				var i = 0;
				var place;
				while (place = results[i]) {
					resultsHtml += '<div id="' + place.place_id + '" class="restaurantListEntry">';
					
					// Marker
					resultsHtml += '<div class="restaurantListEntryLeft"><img src="http://maps.google.com/mapfiles/marker' + letter + '.png" alt="' + letter + '"></div>';
					
					// Main info section
					resultsHtml += '<div class="restaurantListEntryMid">';
					resultsHtml += '<div class="restaurantListEntryTitle">' + place.name + '</div>';
					resultsHtml += '<div class="restaurantListEntryAddress">' + place.formatted_address + '</div>';
					resultsHtml += '<div class="restaurantListEntryDetails">Loading...</div>';
					resultsHtml += '<button class="button commentButton" type="button" onClick="showComments(this.parentElement.parentElement)">Comments</button>';
					resultsHtml += '</div>';
					
					// Images
					resultsHtml += '<div class="restaurantListEntryRight"><img src="img/load.gif" alt="loading..."></div>';
					
					resultsHtml += '</div>';
					letter = nextChar(letter);
					i++;
					
					// Load extra info for this place
					setTimeout(getDetail, 500, place);
					placeService.getDetails(place, function(details, status) {updatePlaceDetails(place, details, status)} );
				}
				
				// Set html
				$('.areaSideScrollContent').html(resultsHtml);
			}
			
			function getDetail(place) {
				placeService.getDetails(place, function(details, status) {updatePlaceDetails(place, details, status)} );
			}
			
			// Update the markers on the map
			function updateMarkers(results) {
				var letter = 'A';
				var i = 0;
				var place;
				while (place = results[i]) {
					var img = 'http://maps.google.com/mapfiles/marker' + letter + '.png'
					var marker = new google.maps.Marker({
						map: map,
						icon: img,
						title: place.name,
						position: place.geometry.location
					});
					markers.push(marker);
					
					letter = nextChar(letter);
					i++;
				}
			}
			
			// Update some sidebar place's specific details
			function updatePlaceDetails(place, details, status) {
				if (status != google.maps.places.PlacesServiceStatus.OK) {
					setTimeout(getDetail, 1000, place);
					return;
				}
				
				// Update phone + URL
				var detailsHtml = "Contact: " + details.formatted_phone_number + "<br />";
				detailsHtml += "<a href='" + details.website + "'>Website</a>";
				$("#" + details.place_id + " .restaurantListEntryMid .restaurantListEntryDetails").html(detailsHtml);
				
				// Stop loading icon
				$("#" + details.place_id + " .restaurantListEntryRight").html("");
			}
			
			// Button pressed to display comments; arrange UI and get ready to load
			function showComments(el) {
				showingComments = true;
				
				// Get place clicked
				placeCurrentlyShown = $(el).attr('id');
				
				// Remove side area
				$(".areaSide").animate({width:'toggle'}, 500, showComments2);
			}
			
			function showComments2() {
				var placeName = $("#" + placeCurrentlyShown + " .restaurantListEntryMid .restaurantListEntryTitle").html();
				
				// Update 'restaurants' text
				$(".areaSideScrollTitle").html("<h2>Comments for " + placeName + ":</h2>");
				$(".areaSideScrollContent").html("");
				
				// Show commenting area
				$(".areaSideScroll").css("bottom", "200px");
				$(".areaSideBottom").css("height", "200px");
				
				// Show side area
				$(".areaSide").animate({width:'toggle'}, 500, loadComments);
			}
			
			// Load the comments for the current place shown from the database
			function loadComments() {
				// Loading icon
				$('.areaSideScrollContent').html('<img style="margin-left:30px" src="img/load.gif" alt="loading...">');
				
				// Load comments using POST
				$.post("adminfunc.php", {type: 'getcomments', placeid: placeCurrentlyShown}, loadedComments);
			}
			
			// Use comments loaded to fill the comments area
			function loadedComments(data, status, jqXHR) {
				var commentHtml = "";
				
				// Break down data into comments
				var commentData = data.split("|");
				
				if (commentData.length < 2) {
					commentHtml += '<div class="commentEntry">No comments yet.</div>';
				}
				
				for (var i = 0; i < commentData.length - 1; i+=2) {
					var name = commentData[i].trim();
					var text = commentData[i+1].trim();
					
					commentHtml += '<div class="commentEntry">';
					commentHtml += '<div class="commentEntryName">' + name + ' says:</div>';
					commentHtml += '<div class="commentEntryText">' + text + '</div>';
					commentHtml += '</div>';
				}
				
				$('.areaSideScrollContent').html(commentHtml);
			}
			
			// Save a comment submitted by the comment form into the database
			function submitComment() {
				// Get comment info
				var placeID = placeCurrentlyShown;
				var commentName = $('#submitCommentName').val();
				var commentText = $('#submitCommentText').val();
				
				// Validate comment
				if (!validator(commentName) || !validator(commentText)) {
					window.alert("Invalid name or text for comment.");
					return;
				}
				
				// Send info using post
				$.post("adminfunc.php", {type: 'savecomment', placeid: placeCurrentlyShown, commentname: commentName, commenttext: commentText}, savedComment);
			}
			
			// Run when comment is submitted correctly
			function savedComment(data, status, jqXHR) {
				// Tell user
				window.alert("Saved comment successfully!");
				
				// Clear comment area
				$('#submitCommentText').val("");
				
				// Reload comments
				loadComments();
			}
			
			//HELPER FUNCTIONS
			function orderedResultsInAreaWithCentre(results, area, centre) {
				newResults = [];
				distances = [];
				
				var i = 0, j = 0;
				var place;
				while (place = results[i]) { // For every place
					// We only care if it is within the area
					if (area.contains(place.geometry.location)) {
						// Find distance of this place from our centre
						var placeDist = getDistance(centre, place.geometry.location);
						
						// FInd index this needs to be inserted at
						for (j = 0; j < distances.length; j++) {
							if (placeDist < distances[j]) {
								break;
							}
						}
						
						newResults.splice(j, 0, place);
						distances.splice(j, 0, placeDist);
					}
					i++;
				}
				
				return newResults;
			}
			
			function nextChar(c) {
				return String.fromCharCode(c.charCodeAt(0) + 1);
			}
			
			function getDistance(p1, p2) {
				var rad = function(x) { return x * Math.PI / 180; };
				
				var R = 6378137; // Earth’s mean radius in meter
				var dLat = rad(p2.lat() - p1.lat());
				var dLong = rad(p2.lng() - p1.lng());
				var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(rad(p1.lat())) * Math.cos(rad(p2.lat())) * Math.sin(dLong / 2) * Math.sin(dLong / 2);
				var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
				var d = R * c;
				return d; // returns the distance in meter
			}
			
			function validator(s) {
				var regexp = /^[0-9a-zA-Z .,]+$/;
				if (s == null || s.length < 2 || !regexp.test(s)) {
					return false;
				}
				return true;
			}
		</script>
	</head>
	<body onLoad="loadPage()">
		<div class="areaTop">
			<div class="areaTopLeft">
				<a href="prac5.php"><h1>myRestaurant List</h1></a>
			</div>
			<div class="areaTopRight">
				<div class="searchForm">
					<input id="searchVal" type="text" style="border-radius: 3px 0 0 3px;" placeholder="Search" onKeyPress="if (event.keyCode == 13) { updateSearch(); }" />
					<button onClick="updateSearch()">Search</button>
				</div>
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
				<div class="areaSideScroll">
                	<div class="areaSideScrollTitle">
						<h1>Restaurants:</h1>
					</div>
                    <div class="areaSideScrollContent">
                    </div>
				</div>
                <div class="areaSideBottom">
                	<div class="inputCommentName"><input id="submitCommentName" class="textinput" type="text" name="username" placeholder="Name"></div>
           			<div class="inputCommentText"><textarea rows="4" id="submitCommentText" class="textinput" type="password" name="password" placeholder="Comment"></textarea></div>
            		<div class="inputCommentButtons"><input style="float:left" class="button" type="submit" value="Submit" onClick="submitComment()"> <input style="float:right" class="button" type="submit" value="Cancel" onClick="reloadMap()"></div>
				</div>
			</div>
		</div>
	</body>
</html>