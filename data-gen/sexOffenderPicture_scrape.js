document.body.innerHTML += "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>";
var rows = $(".srp-row img");
var fulltext = "";
rows.each(function(){ 
	fulltext += $(this).attr("src") + "<br/>";
});

$("body").replaceWith(fulltext);