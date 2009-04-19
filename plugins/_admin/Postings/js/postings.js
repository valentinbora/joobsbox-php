$(function() {
	$("#posting-tabs").tabs();
	$("#pending-postings tbody tr").click(function(el) {
		var cb = $(this).find("input[type=checkbox]");
		cb = cb[0];
		if($(cb).attr("checked")) {
			$(this).removeClass("selected");
			$(cb).attr("checked", false);
		} else {
			$(this).addClass("selected");
			$(cb).attr("checked", true);
		}
	});
	$("#selectAllPending").click(function(){
		if($(this).attr("checked")) {
			$("#pending-postings tbody tr td input[type=checkbox]").attr("checked", false);
		} else {
			$("#pending-postings tbody tr td input[type=checkbox]").attr("checked", true);
		}
	});
	$('#saveConfiguration').click(function(){
		var data = {};

		$.post(pluginUrl + 'saveConfiguration', {'data': $.toJSON(data)},
		  function(data){
		    	$('#saveDialog').dialog('option', 'mustReload', data.mustReload).dialog('open');			
		  }, "json");
	});
});