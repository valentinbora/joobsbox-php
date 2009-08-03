$(document).ready(function(){
  $("#upgradecore").click(function(ev){
    ev.preventDefault();
    $("#upgradecore").remove();
    $("#core-upgrade").load(href + 'upgradecore', {"ajax": 1});
    setInterval(function() {
      $("#core-upgrade").load(href + 'upgradecoreprogress', {"ajax": 1});
    }, 3000);
  });
});