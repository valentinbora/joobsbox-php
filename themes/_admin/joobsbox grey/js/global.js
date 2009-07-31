$(function(){
	$("li.item").mouseenter(function() {
		$(this).addClass("hover");
	});
	$("li.item").mouseleave(function() {
		$(this).removeClass("hover");
	});
	
	$("#admin-menu").sortable({
		distance: 15,
		cancel: '#menu-dashboard, .separator',
		stop: function() {
		  $.post(baseUrl + '/admin/sortmenu', $("#admin-menu").sortable("serialize"));
	  }
	});
});
