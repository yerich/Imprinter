$(document).ready(function() {
	$(".redlink").click(function() {
		if($(this).html() != "Sure?") {
			$(this).data("redlink_old", $(this).html());
			$(this).html("Sure?");
			
			return false;
		}
	});
	
	$(".redlink").mouseout(function() {
		if($(this).html() == "Sure?") {
			$(this).html($(this).data("redlink_old"));
		}
	});
})
