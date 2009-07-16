$(function() {
	if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
		if($('#postJob a'))
			$('#postJob a').html('+');
		if($('#admin-menu li a'))
			$('#admin-menu li a').html('');
	}
});