$(document).ready(function() {
	$('div.inner').wrap('<div class="outer"></div>');

	$("div.box, div.plain").corner("round top");
	$("div.inner").corner("round top ").parent().css('padding', '2px').corner("round top");
	
	$("#searchInput").click(function(ev) {
		$(ev.target).attr("value", "");
	});
});