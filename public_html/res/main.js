$(document).ready(function(){
	$("input[data-placeholder]").each(function(ind){
		var p = $(this).attr("data-placeholder");
		$(this).val(p);
		$(this).on("focus", function(){
			$(this).parent().find("label").fadeIn(200);
			if($(this).val() == p){
				$(this).val("");
			}
		}).on("blur", function(){
			$(this).parent().find("label").fadeOut(200);
			if($(this).val() == ""){
				$(this).val(p);
			}
		});
	});
});