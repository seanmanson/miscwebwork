<%@ page import="java.io.*" %>
<%!
public String getNames(String loc) throws IOException {
  BufferedReader br = new BufferedReader(new FileReader(loc));
  String titles = "";
  
  while (true) {
    String nextTitle = br.readLine();
    if (nextTitle == null || nextTitle.equals("")) {
      break;
    }
    titles += nextTitle + "#";
    br.readLine();
    br.readLine();
    br.readLine();
    br.readLine();
    br.readLine();
    br.readLine();
  }
  
  br.close();
  return titles;
}

public String getInfo(String loc, String title) throws IOException {
  BufferedReader br = new BufferedReader(new FileReader(loc));
  
  while (true) {
    String thisTitle = br.readLine();
    if (thisTitle == null || thisTitle.equals("")) {
      return "";
    } else if (thisTitle.equals(title)) {
      break;
    }
    br.readLine();
    br.readLine();
    br.readLine();
    br.readLine();
    br.readLine();
    br.readLine();
  }
  
  String address = br.readLine();
  String phone = br.readLine();
  String icon = br.readLine();
  String imgList = br.readLine();
  String content = br.readLine();
  
  return title + "|" + address + "|" + phone + "|" + icon + "|" + imgList + "|" + content;
}

public void setInfo(String loc, String oldTitle, String title, String address, String phone, String icon, String images, String desc) throws IOException {
  BufferedReader br = new BufferedReader(new FileReader(loc));
  String fileData = "";
  
  while (true) {
    String thisTitle = br.readLine();
    if (thisTitle == null || thisTitle.equals("")) {
      break;
    } else if (thisTitle.equals(oldTitle)) {
      fileData += title + '\n' + address + '\n' + phone + '\n' + icon +'\n' + images + '\n' + desc + '\n' + '\n';
      br.readLine();
      br.readLine();
      br.readLine();
      br.readLine();
      br.readLine();
      br.readLine();
    } else {
      fileData += thisTitle + '\n';
      fileData += br.readLine() + '\n';
      fileData += br.readLine() + '\n';
      fileData += br.readLine() + '\n';
      fileData += br.readLine() + '\n';
      fileData += br.readLine() + '\n';
      fileData += br.readLine() + '\n';
    }
  }
  
  br.close();
  FileOutputStream fileOut = new FileOutputStream(loc);
  fileOut.write(fileData.getBytes());
  fileOut.close();
}
%><%
String requestType = request.getParameter("type");
if (requestType != null) {
  if (requestType.equals("getnames")) {
    out.print(getNames(application.getRealPath("/") + "prac3/resinfo.txt"));
  } else if (requestType.equals("getinfo")) {
    String titleToGet = request.getParameter("title");
    out.print(getInfo(application.getRealPath("/") + "prac3/resinfo.txt", titleToGet));
  } else if (requestType.equals("setinfo")) {
    String oldTitle = request.getParameter("oldtitle");
    String title = request.getParameter("title");
    String address = request.getParameter("address");
    String phone = request.getParameter("phone");
    String icon = request.getParameter("icon");
    String images = request.getParameter("images");
    String desc = request.getParameter("desc");
    setInfo(application.getRealPath("/") + "prac3/resinfo.txt", oldTitle, title, address, phone, icon, images, desc);
  }
}
%>