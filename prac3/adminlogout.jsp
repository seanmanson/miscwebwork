<%
session.invalidate();
response.sendRedirect(request.getContextPath() + "prac3.jsp");
%>