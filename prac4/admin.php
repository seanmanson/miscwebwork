<?php
include("mysqllogin.php");
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
	header('Location: adminlogin.php');
}

// Connect to database
$db = new MySQLLogin();
$db->connect("infs", "3202", "restaurants");

// Get all restaurant info
$resinfo = $db->query("SELECT * FROM RestaurantInfo;");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>My Restaurant - Admin Page</title>
		<link rel="stylesheet" href="prac4style.css" />
		<script src="js/jquery-1.11.0.min.js"></script>
		<script type="text/javascript">
			// Add button pressed; change to edit field
			function addButtonPressed(el) {
				// Get id of last restaurant in table
				var restaurantNum = parseInt($(el).parent().prev().children().first().attr('id').substr(1)) + 1;
				
				// Setup a new empty form replacing add button
				var newRow ='<div id="r' + restaurantNum + '" class="adminEditTableEntryLeft inputarea">';
				newRow += emptyRestaurantEditForm();
				newRow += '</div>';
				newRow += '<span id="r' + restaurantNum + '" class="buttonarea">';
				newRow += '<button class="adminEditTableEntryRight button" onClick="saveAddButtonPressed(this)">Save</button><br /><button class="adminEditTableEntryRight button" onClick="cancelAddButtonPressed(this)">Cancel</button>';
				newRow += '</span>';
				
				$(el).parent().html(newRow);
			}
			
			// Saved adding a new row; put add button back
			function saveAddButtonPressed(el) {
				if (!saveButtonPressed(el)) {
					return;
				}
				
				// Put add button on end
				$(".adminEditTable").append('<div class="adminEditTableEntry"><button class="button" onclick="addButtonPressed(this)">Add</button></div>');
			}
		
			// Cancelled adding a new row; set this back to normal
			function cancelAddButtonPressed(el) {
				$(el).parent().parent().html('<button class="button" onclick="addButtonPressed(this)">Add</button>');
			}
	
			// Edit button pressed; load info for use
			function editButtonPressed(el) {
				// Get restaurant title
				var titleID = $(el).parent().attr('id').substr(1);
		
				
				// Update input area using this title
				$.post("adminfunctions.php", {type: "getinfo", id: titleID}, updateInputArea);
			}
			
			// Save button pressed; get data, check, and if valid, send off to server; finally, reset
			function saveButtonPressed(el) {
				// Get old restaurant title
				var titleID = $(el).parent().attr('id').substr(1);
				
				// Get data
				var title = $('#r' + titleID + '.inputarea #title').val();
				var address = $('#r' + titleID + '.inputarea #address').val();
				var phone = $('#r' + titleID + '.inputarea #phone').val();
				var icon = $('#r' + titleID + '.inputarea #icon').val();
				var images = $('#r' + titleID + '.inputarea #images').val();
				var latitude = $('#r' + titleID + '.inputarea #latitude').val();
				var longitude = $('#r' + titleID + '.inputarea #longitude').val();
				var desc = $('#r' + titleID + '.inputarea #desc').val();
				if (!validTitle(title) || !validAddress(address) || !validPhone(phone) || !validIcon(icon) || !validImages(images) || !validLatLong(latitude, longitude) || !validDesc(desc)) {
					return false;
				}
				
				// Send off to server
				var data = {type: "setinfo", id: titleID, title: title, address: address, phone: phone, icon: icon, images: images, latitude: latitude, longitude: longitude, desc: desc};
				$.post("adminfunctions.php", data, savedSuccessfully);
				
				// Update input area using this title
				$('#r' + titleID + '.inputarea').html(title);
				
				// Update button area to have edit button.
				$('#r' + titleID + '.buttonarea').html('<button class="adminEditTableEntryRight button" onClick="editButtonPressed(this)">Edit</button><br /><button class="adminEditTableEntryRight button" onclick="removeButtonPressed(this)">Remove</button>');
				return true;
			}
			
			// Server saved away our message successfully, responding with new title
			function savedSuccessfully(response) {
				// Tell user
				if (response == null || response == "") {
					window.alert("Saved successfully");
				} else {
					window.alert(response);
				}
			}
			
			// Cancel button pressed; reload default without saving any data
			function cancelButtonPressed(el) {
				// Get restaurant title
				var titleID = $(el).parent().attr('id').substr(1);
				var oldtitle = $('#r' + titleID + '.inputarea #oldtitle').val();
				
				// Update input area using this title
				$('#r' + titleID + '.inputarea').html(oldtitle);
				
				// Update button area to have edit button
				$('#r' + titleID + '.buttonarea').html('<button class="adminEditTableEntryRight button" onClick="editButtonPressed(this)">Edit</button><br /><button class="adminEditTableEntryRight button" onclick="removeButtonPressed(this)">Remove</button>');
			}
			
			// Delete button pressed; popup shit and ask to remove before refreshing page
			function removeButtonPressed(el) {
				// Get thing to delete
				var titleID = $(el).parent().attr('id').substr(1);
				
				// Popup asking for confirmation
				if (!confirm("Are you sure?")) {
					return;
				}
				
				// Send off remove request
				$.post("adminfunctions.php", {type: "deleteinfo", id: titleID}, removedSuccessfully);
			}
			
			// Server removed successfully
			function removedSuccessfully(response) {
				// Refresh page
				location.reload(true);
			}
		
			// Update inputarea once POST reply is retrieved
			function updateInputArea(response) {
				// Break down response into understandable parts
				var responseParts = response.split("|");
				var titleID = responseParts[0].trim();
				
				// Test response
				if (titleID == null || titleID == "") {
					window.alert("Failed to load from server.");
					return;
				}
				
				// Create editing form using this data
				var form = restaurantEditFormFrom(responseParts);
				$('#r' + titleID + '.inputarea').html(form);
				
				// Update button area to have cancel and update buttons
				$('#r' + titleID + '.buttonarea').html('<button class="adminEditTableEntryRight button" onClick="saveButtonPressed(this)">Save</button><br /><button class="adminEditTableEntryRight button" onClick="cancelButtonPressed(this)">Cancel</button>');
			}
		
			// Create an empty editing form
			function emptyRestaurantEditForm() {
				var form = "";
				form += '<div class="adminEditTableEditEntry">Name: <input id="title" class="adminEditText" type="text" placeholder="Restaurant title"></div>';
				form += '<div class="adminEditTableEditEntry">Address: <input id="address" class="adminEditText" type="text" placeholder="Address"></div>';
				form += '<div class="adminEditTableEditEntry">Contact: <input id="phone" class="adminEditText" type="text" placeholder="Phone"></div>';
				form += '<div class="adminEditTableEditEntry">Icon: <input id="icon" class="adminEditText" type="text" placeholder="Icon"></div>';
				form += '<div class="adminEditTableEditEntry">Images: <input id="images" class="adminEditText" type="text" placeholder="Images"></div>';
				form += '<div class="adminEditTableEditEntry">Latitude: <input id="latitude" class="adminEditText" type="text" placeholder="Latitude"></div>';
				form += '<div class="adminEditTableEditEntry">Longitude: <input id="longitude" class="adminEditText" type="text" placeholder="Longitude"></div>';
				form += '<div class="adminEditTableEditEntry">Desc: <textarea rows="4" id="desc" class="adminEditText" placeholder="Description"></textarea></div>';
				return form;
			}
		
			// Create an editing form using a response
			function restaurantEditFormFrom(responseParts) {
				var form = "";
				form += '<div class="adminEditTableEditEntry">Name: <input id="title" class="adminEditText" type="text" placeholder="Restaurant title" value="' + responseParts[1] + '"></div>';
				form += '<div class="adminEditTableEditEntry">Address: <input id="address" class="adminEditText" type="text" placeholder="Address" value="' + responseParts[2] + '"></div>';
				form += '<div class="adminEditTableEditEntry">Contact: <input id="phone" class="adminEditText" type="text" placeholder="Phone" value="' + responseParts[3] + '"></div>';
				form += '<div class="adminEditTableEditEntry">Icon: <input id="icon" class="adminEditText" type="text" placeholder="Icon" value = "' + responseParts[4] + '"></div>';
				form += '<div class="adminEditTableEditEntry">Images: <input id="images" class="adminEditText" type="text" placeholder="Images" value="' + responseParts[5] + '"></div>';
				form += '<div class="adminEditTableEditEntry">Latitude: <input id="latitude" class="adminEditText" type="text" placeholder="Latitude" value="' + responseParts[6] + '"></div>';
				form += '<div class="adminEditTableEditEntry">Longitude: <input id="longitude" class="adminEditText" type="text" placeholder="Longitude" value="' + responseParts[7] + '"></div>';
				form += '<div class="adminEditTableEditEntry">Desc: <textarea rows="4" id="desc" class="adminEditText" placeholder="Description">';
				form += responseParts[8];
				form += '</textarea></div>';
				form += '<input id="oldtitle" type="hidden" value="' + responseParts[1] + '">';
				return form;
			}
			
			// Validation functions
			function validTitle(title) {
				var regexp = /^[0-9a-zA-Z ]+$/;
				if (title == null || title.length < 4 || !regexp.test(title)) {
					window.alert("Title must be 4 or more characters long and can only contain alphanumberic characters and spaces.");
					return false;
				}
				return true;
			}
			
			function validAddress(address) {
				if (address == null || address.length < 10) {
					window.alert("Address must be 10 or more characters long.");
					return false;
				}
				return true;
			}
			
			function validPhone(phone) {
				var regexp = /^[0-9 ()]+$/;
				if (phone == null || phone.length < 8 || !regexp.test(phone)) {
					window.alert("Contact must be 8 or more characters long and contain only brackets, spaces and numbers.");
					return false;
				}
				return true;
			}
			
			function validIcon(icon) {
				var regexp = /^[0-9a-zA-Z][0-9a-zA-Z\/.]+[0-9a-zA-Z]$/;
				if (icon == null || icon.length < 4 || !regexp.test(icon)) {
					window.alert("Icon must be 4 or more characters long and contain only alphanumeric characters, '.' and '/'.");
					return false;
				}
				return true;
			}
			
			function validImages(images) {
				var regexp = /^[0-9a-zA-Z\/.#]+$/;
				if (images == null || images.length < 8 || !regexp.test(images)) {
					window.alert("Icon must be 8 or more characters long and contain only 0-9, a-z, A-Z, /, . or #.");
					return false;
				}
				return true;
			}
		
			function validLatLong(latitude, longitude) {
				var regexp = /^[-]{0,1}[0-9.]{1,}$/;
				if (latitude == null || longitude == null || !regexp.test(latitude) || !regexp.test(longitude)) {
					window.alert("Longitude and latitude must be float numbers.");
					return false;
				}
				return true;
			}
			
			function validDesc(desc) {
		 		var regexp = /^[0-9a-zA-Z:".@,()= ?<>\-\/]+$/;
				if (desc == null || desc.length < 8 || !regexp.test(desc)) {
					window.alert("Description must be 8 or more characters long, with no funny business.");
					return false;
				}
				return true;
			}
		</script>
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
				<h2>Admin page</h2>
				<div class="adminEditTable">
					<?php
					while ($row = mysqli_fetch_array($resinfo)) {
						echo '<div class="adminEditTableEntry">';
						echo '<div id="r' . $row['ID'] . '" class="adminEditTableEntryLeft inputarea">';
						echo $row['Name'];
						echo '</div>';
						echo '<span id="r' . $row['ID'] . '" class="buttonarea">';
						echo '<button class="adminEditTableEntryRight button" onclick="editButtonPressed(this)">Edit</button><br /><button class="adminEditTableEntryRight button" onclick="removeButtonPressed(this)">Remove</button>';
						echo '</span>';
						echo '</div>';
					}
					?>
					<div class="adminEditTableEntry">
						<button class="button" onclick="addButtonPressed(this)">Add</button>
					</div>
				</div>
				<form action="adminlogout.php" method="post">
					<input class="button" type="submit" value="LOGOUT">
				</form>
			</div>
		</div>
	</body>
</html>