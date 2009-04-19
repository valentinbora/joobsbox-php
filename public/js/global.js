$(document).ready(function() {
	$('div.inner').wrap('<div class="outer"></div>');

	$("div.box, div.plain").corner("round top");
	$("div.inner").parent().css({'padding': '2px', 'padding-top': '4px'}).corner("round top");
});