$(function(){
	$("li.item").mouseenter(function() {
		$(this).addClass("hover");
	});
	$("li.item").mouseleave(function() {
		$(this).removeClass("hover");
	});
});