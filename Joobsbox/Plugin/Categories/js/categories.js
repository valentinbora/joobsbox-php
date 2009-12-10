var editDisabled = false;

function recreateInteractivity() {
  // Activate edit in place
  $("#categories li").each(function(){
     $(this).click(function(){
          $("#categories li input").each(function(){
            $(this).blur();
          });
          enableEditInPlace($(this));
     });  
     $(this).find("input").blur(function(){           
       disableEditInPlace(this);
     });
     $(this).find("input").keyup(function(e){
        if(e.keyCode == 13 || e.keyCode == 27) {   
          disableEditInPlace(this);
        }
     });
  });
  
  // Make them sortable
  $("#categories").sortable({
    start: function() {
      $(".editable").blur();
    },
    stop: function() {
      editDisabled = true;
    }
  });
}        

function enableEditInPlace(el) {
  if($(el).find("input").length) {
    return;
  }                  
  
  if(editDisabled) {
    setTimeout(function(){editDisabled = false;}, 100);
    return;
  }         
  
  $(".selected").each(function(){
    $(this).removeClass("selected");
  });
              
  el = el[0]; 
  $(el).addClass("selected");
  var text = $(el).text();
  $(el).text("");
  var a = $(el).append('<input type="text" id="currently-edited-input" value="' + text + '" />');
  $(el).find("input").focus();
  
  recreateInteractivity();
}                   

function disableEditInPlace(el) {
    var text = $(el).attr("value");  
    if(!text.length) {
      $(el).parent("li").remove();
      return;
    }
    $(el).parents("li").text(text);//.removeClass("selected");
    $(el).replaceWith(""); 
}

$(document).ready(function(){
  recreateInteractivity();
  $('#createNewNode').click(function(){
		var a = $("#categories").append('<li class="editable" id="new_' + $(".editable").length + '"></li>');
		$("#categories li").bind("blur", function(){
		  disableEditInPlace(this);
		});
		recreateInteractivity();
		$($("#categories :last").get(0)).click();
	});
	
	$('#deleteNode').click(function(){
	  var count = $("#categories li").length;
	  if(count <= 1) {
	    alert(translate("I won't let you delete all the categories in here, sorry."));
	    return;
	  }
		if(confirm(translate("Are you sure you want to delete this category and all its subcategories?"))) {
			$("#categories li.selected").remove();
		}
	});
	
	$('#saveConfiguration').click(function(){
	  $("#info").fadeIn();
	  $("#info").text("Wait...");
	  var categories = [];
	  $("#categories li").each(function(){
	    categories.push({"name": $(this).text(), "existing": $(this).attr('id')});
	  });   
	  
	  $.post(pluginUrl + 'saveConfiguration/', {"categoriesObject": $.toJSON(categories)},
		  function(data){
		      if(data.mustReload) {
		        $("#info").text("Saved - Going to reload page in a brief");
		        setTimeout(function() {window.location.reload();}, 1000);
		      } else {
		        $("#info").text("Saved");
		      }
		    	setTimeout(function(){
		    	  $("#info").fadeOut();
		    	  $("#saveConfiguration").removeClass("attention");
		    	}, 3000);
		  });
	});
});