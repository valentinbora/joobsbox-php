$(document).ready(function(){
  $("#locale").change(function(){
    window.location.href += "?lang=" + $("#locale").attr("value");
  });
  
  $("#dbadapter").change(function(){
    window.location.href += "?dbadapter=" + $("#dbadapter").attr("value");
  });
});