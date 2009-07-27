$(document).ready(function(){
  $("#google_analytics_wrapper").parent().ajaxStart(function(){
     $(this).addClass("loading");
   });
   
   $("#google_analytics_wrapper").parent().ajaxSuccess(function(){
      $(this).removeClass("loading");
    });
  
  var href = window.location.href;
  if(href[href.length-1] != '/') href += '/';
  
  $("#google_analytics_wrapper").parent().load(href + 'google_analytics', {"ajax": 1});
});
