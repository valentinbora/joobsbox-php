$(function() {
	$("#posting-tabs").tabs();
	$("#pending-postings tbody tr").click(function(ev) {
		var cb = $(this).find("input[type=checkbox]");
		cb = cb[0];
		$(this).toggleClass("selected");
		if($(ev.target).attr("tagName") != "INPUT") {
			$(cb).attr("checked", $(this).hasClass("selected"));
		}
	});
	$("#selectAllPending").click(function(){
		if($(this).attr("checked")) {
			$("#pending-postings tbody tr td input[type=checkbox]").attr("checked", true);
			$("#pending-postings tbody tr").addClass("selected");
		} else {
			$("#pending-postings tbody tr td input[type=checkbox]").attr("checked", false);
			$("#pending-postings tbody tr").removeClass("selected");
		}
	});
	$("#deletePostingsPending").click(function() {
		
	});
});