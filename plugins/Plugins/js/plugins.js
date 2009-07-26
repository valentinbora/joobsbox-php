var buttons = {};

buttons[translate("No")] = function() {
  $("#dialog").dialog('close');
};

$(document).ready(function(){
  $("#dialog").dialog({
    autoOpen: false,
		bgiframe: true,
		resizable: false,
		height: 140,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		},
		buttons: buttons
	});
   		
  // Active form
  $("#check-all-active").click(function(ev){
    $(".active-plugin-checkbox").attr("checked", $(this).attr("checked"));
  });
  
  $(".active-plugin-checkbox").click(function(){
    $("#check-all-active").attr("checked", false);
  });
  
  $("#btnDeactivate").click(function(){
    $(this).parents("form").get(0).submit();
  });
  
  // Inactive form
  $("#check-all-inactive").click(function(ev){
    $(".inactive-plugin-checkbox").attr("checked", $(this).attr("checked"));
  });
  
  $(".inactive-plugin-checkbox").click(function(){
    $("#check-all-inactive").attr("checked", false);
  });
  
  $("#btnActivate").click(function(){
    $("#inactive_form_action").attr("value", "activate");
    $(this).parents("form").get(0).submit();
  });
  $("#btnDelete").click(function(){
    $("#inactive_form_action").attr("value", "delete");

    buttons[translate('Yes')] = function() {
      $("#btnDelete").parents("form").get(0).submit();
    }
    $("#dialog").dialog("option", "buttons", buttons);  
    $("#dialog").dialog("open");
  });
});