$(document).ready(function(){
  $("#locale_selector").change(function(){
    window.location.href += "?lang=" + $("#locale_selector").attr("value");
  });
});