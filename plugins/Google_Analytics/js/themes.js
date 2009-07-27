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
  
  $(".activateButton").click(function(ev){
    var button = $(ev.target);
    var hidden = $(button[0]).nextAll(".action");
    $(hidden[0]).attr("value", "activate");
    ev.preventDefault();
    button.parents('form').get(0).submit();
  });
  $(".deleteButton").click(function(ev){
    ev.preventDefault();
    var button = $(ev.target);
    var hidden = $(button[0]).nextAll(".action");
    $(hidden[0]).attr("value", "delete");

    buttons[translate('Yes')] = function() {
      button.parents("form").get(0).submit();
    }
    $("#dialog").dialog("option", "buttons", buttons);  
    $("#dialog").dialog("open");
  });
});
