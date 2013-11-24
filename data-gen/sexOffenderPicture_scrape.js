/* 
this is not a runnable script, it only provides code for pasting into Chrome console. To get this you must first go to:
http://florida-offender.findthedata.org/
And then select males/females and run this code.
*/
document.body.innerHTML += "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>";
var rows = $(".srp-row img");
var fulltext = "";
rows.each(function(){ 
	fulltext += $(this).attr("src") + "<br/>";
});

$("body").replaceWith(fulltext);