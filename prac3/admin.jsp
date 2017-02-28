<%@ page import="java.io.*" %>
<%
if (session.getAttribute("loggedin") == null) {
  response.sendRedirect(request.getContextPath() + "adminlogin.jsp");
}
%>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My Restaurant - Admin Page</title>
    <link rel="stylesheet" href="prac3style.css" />
    <script src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript">
      // Edit button pressed; load info for use
      function editButtonPressed(el) {
        // Get restaurant title
        var titleID = $(el).parent().attr('id');
        
        // Update input area using this title
        $.post("adminfunctions.jsp", {type: "getinfo", title: titleID.replace(/_/g, " ")}, updateInputArea);
      }
      
      // Update inputarea once POST reply is retrieved
      function updateInputArea(response) {
        // Break down response into understandable parts
        var responseParts = response.split("|");
        var titleID = responseParts[0].replace(/ /g, "_").trim();
        
        // Test response
        if (titleID == null || titleID == "") {
          window.alert("Failed to load from server.");
          return;
        }
        
        // Create editing form using this data
        var form = restaurantEditFormFrom(responseParts);
        $('#' + titleID + '.inputarea').html(form);
        
        // Update button area to have cancel and update buttons
        $('#' + titleID + '.buttonarea').html('<button class="adminEditTableEntryRight button" onClick="saveButtonPressed(this)">Save</button><br /><button class="adminEditTableEntryRight button" onClick="cancelButtonPressed(this)">Cancel</button>');
      }
      
      // Save button pressed; get data, check, and if valid, send off to server; finally, reset
      function saveButtonPressed(el) {
        // Get old restaurant title
        var titleID = $(el).parent().attr('id');
        
        // Get data
        var title = $('#' + titleID + '.inputarea #title').val();
        var address = $('#' + titleID + '.inputarea #address').val();
        var phone = $('#' + titleID + '.inputarea #phone').val();
        var icon = $('#' + titleID + '.inputarea #icon').val();
        var images = $('#' + titleID + '.inputarea #images').val();
        var desc = $('#' + titleID + '.inputarea #desc').val();
        if (!validTitle(title) || !validAddress(address) || !validPhone(phone) || !validIcon(icon) || !validImages(images) || !validDesc(desc)) {
          return;
        }
        
        // Send off to server
        var data = {type: "setinfo", oldtitle: titleID.replace(/_/g, " "), title: title, address: address, phone: phone, icon: icon, images: images, desc: desc};
        $.post("adminfunctions.jsp", data, savedSuccessfully);
        
        // Update ID of areas
        var newTitleID = title.replace(/ /g, "_");
        $('#' + titleID + '.inputarea').attr("id", newTitleID);
        $('#' + titleID + '.buttonarea').attr("id", newTitleID);
        
        // Update input area using this title
        $('#' + newTitleID + '.inputarea').html(title);
        
        // Update button area to have edit button.
        $('#' + newTitleID + '.buttonarea').html('<button class="adminEditTableEntryRight button" onClick="editButtonPressed(this)">Edit</button>');
      }
      
      // Server saved away our message successfully, responding with new title
      function savedSuccessfully(response) {
        // Tell user
        window.alert("Saved successfully");
      }
      
      // Cancel button pressed; reload default without saving any data
      function cancelButtonPressed(el) {
        // Get restaurant title
        var titleID = $(el).parent().attr('id');
        
        // Update input area using this title
        $('#' + titleID + '.inputarea').html(titleID.replace(/_/g, " "));
        
        // Update button area to have edit button
        $('#' + titleID + '.buttonarea').html('<button class="adminEditTableEntryRight button" onClick="editButtonPressed(this)">Edit</button>');
      }
      
      // Create an editing form using a response
      function restaurantEditFormFrom(responseParts) {
        var form = "";
        form += '<div class="adminEditTableEditEntry">Name: <input id="title" class="adminEditText" type="text" placeholder="Restaurant title" value="' + responseParts[0] + '"></div>';
        form += '<div class="adminEditTableEditEntry">Address: <input id="address" class="adminEditText" type="text" placeholder="Address" value="' + responseParts[1] + '"></div>';
        form += '<div class="adminEditTableEditEntry">Contact: <input id="phone" class="adminEditText" type="text" placeholder="Phone" value="' + responseParts[2] + '"></div>';
        form += '<div class="adminEditTableEditEntry">Icon: <input id="icon" class="adminEditText" type="text" placeholder="Icon" value = "' + responseParts[3] + '"></div>';
        form += '<div class="adminEditTableEditEntry">Images: <input id="images" class="adminEditText" type="text" placeholder="Images" value="' + responseParts[4] + '"></div>';
        form += '<div class="adminEditTableEditEntry">Desc: <textarea rows="4" id="desc" class="adminEditText" placeholder="Description">';
        form += responseParts[5];
        form += '</textarea></div>';
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
      
      function validDesc(desc) {
        if (desc == null || desc.length < 8) {
          window.alert("Description must be 8 or more characters long.");
          return false;
        }
        return true;
      }
    </script>
  </head>
  <body>
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
      <div class="areaMain">
        <h2>Admin page</h2>
        <div class="adminEditTable"><%
          BufferedReader br = new BufferedReader(new FileReader(application.getRealPath("/") + "prac3/resinfo.txt"));
          
          while (true) {
            String title = br.readLine();
            if (title == null || title.equals("")) {
              break;
            }
            String titleID = title.replace(" ", "_");
            String address = br.readLine();
            String phone = br.readLine();
            String icon = br.readLine();
            String imgList = br.readLine();
            String content = br.readLine();
            br.readLine();
        %><div class="adminEditTableEntry">
            <div id="<%= titleID %>" class="adminEditTableEntryLeft inputarea">
              <%= title %>
            </div>
            <span id="<%= titleID %>" class="buttonarea">
              <button class="adminEditTableEntryRight button" onclick="editButtonPressed(this)">Edit</button><br />
            </span>
          </div><%
          }
      %></div>
        <form action="adminlogout.jsp" method="post">
          <input class="button" type="submit" value="LOGOUT">
        </form>
      </div>
    </div>
  </body>
</html>