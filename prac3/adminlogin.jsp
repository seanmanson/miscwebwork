<%
if (session.getAttribute("loggedin") != null) {
  response.sendRedirect(request.getContextPath() + "admin.jsp");
}

String username = request.getParameter("username");
String password = request.getParameter("password");
boolean loginFailed = false;
if (username != null && password != null) {
  if (username.equals("admin") && password.equals("password")) {
    session.setAttribute("loggedin", true);
    session.setAttribute("username", username);
    response.sendRedirect(request.getContextPath() + "admin.jsp");
  } else {
    loginFailed = true;
  }
}
%>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My Restaurant - Admin Login</title>
    <link rel="stylesheet" href="prac3style.css" />
    <link href="css/lightbox.css" rel="stylesheet" />
    <script src="js/jquery-1.11.0.min.js"></script>
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
        <p id="loginError" class="error"><%= loginFailed ? "Incorrect username/password." : "" %></p>
        <div class="areaMainLogin">
          <h2>Admin Login Page</h2>
          <form action="adminlogin.jsp" method="post">
            <div class="areaMainLoginEntry"><input class="textinput" type="text" name="username" placeholder="Username"></div>
            <div class="areaMainLoginEntry"><input class="textinput" type="password" name="password" placeholder="Password"></div>
            <div class="areaMainLoginEntry"><input class="button" type="submit" value="Login"></div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>