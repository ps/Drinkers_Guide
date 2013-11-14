/* This is a reference, not a runnable file. To run each snippet, use the Chrome Dev Console and copy paste the snippets */

/*
NJ: 

URL:
http://en.wikipedia.org/wiki/List_of_municipalities_in_New_Jersey

Script:
*/

document.body.innerHTML += "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>"
var rows = $("#mw-content-text .wikitable tbody tr");
var fulltext = "";
rows.each(function(){ 
	var tds = $(this).find("td"); 
	var pop = $(tds[3]).html();
	pop = pop.replace(/\,/g, '');
	fulltext += $(tds[1]).find("a").html() + ";" + pop + "<br/>";
});

$("body").replaceWith(fulltext);


/*

PA:

URL: http://en.wikipedia.org/wiki/List_of_municipalities_in_Pennsylvania

Since there were over 2500 for PA I removed some.

Script:
(remove last row)
*/
document.body.innerHTML += "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>"
var rows = $("#mw-content-text .wikitable tbody tr");
var fulltext = "";
rows.each(function(){ 
	var tds = $(this).find("td"); 
	var pop = $(tds[5]).html();
	pop = pop.replace(/\,/g, '');
	fulltext += $(tds[1]).text() + ";" + pop + "<br/>";
});

$("body").replaceWith(fulltext);


/*

NY:

URL 1: http://en.wikipedia.org/wiki/List_of_cities_in_New_York
URL 2: http://en.wikipedia.org/wiki/List_of_towns_in_New_York

Script (both pages are same):
*/
document.body.innerHTML += "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>"
var rows = $("#mw-content-text .wikitable.sortable tbody tr");
var fulltext = "";
rows.each(function(){ 
	var tds = $(this).find("td"); 
	var pop = $(tds[2]).html();
	pop = pop.replace(/\,/g, '');
	fulltext += $(tds[0]).text() + ";" + pop + "<br/>";
});

$("body").replaceWith(fulltext);