<?php 
	require_once("settings.php");
	$PG_NAME = "technical";
	require_once("res/top.php"); //header, navigation, opening tags etc.
?>
<h3>Target Audience and Business Aspects</h3>
Target Audience: Drinkers and Police

Drinkers:
- find safe bar around them
- be aware which predators go to the same bars that they do
<h3>Data Generation</h3>


<h4>Beer, Country, Manufacturer Tables (beer_country_manf.py)</h4>
<ul>
	<li>beer name, manufacturer, and country information was scrapted from (www.beerme.com/beerlist.php)</li>
	<li>beer name, manufacturer, and style were scrapted from the page, alcohol content was generated randomly between 1%-10% inclusive</li>
	<li>Manufacturer and Country tables followed from the scrapted data</li>
	<li>a few countries were manually labeled as having prohibition</li>
</ul>


<h4>Consumed Table (consumed.py)</h4>
<ul>
	<li>
		the script pulled the data from the LeftWith table
		<ul>
		<li>the date was based on the date from the LeftWith table and the time was anywhere between 1-120 minutes before the time at which the individuals left the bar</li>
		<li>drinker1 from LeftWith table was selected to consume 1-4 drinks</li>
		<li>drinker2 from LeftWith table was selected to consume 4-10 drinks</li>
		<li>only one entry was generated for each unique drinker from the LeftWith table</li>
		</ul>	
	</li>
	<li>
		the rest of data was pulled from the Drinkers table and selected those that did not appear in LeftWith
		<ul>
			<li>for each drinker, an assumption was made that a drinker would go to a bar 1-4 times over a course of a month</li>
			<li>each drinker would be generated to consume anywhere between 1-10 drinks during one visit, date of consumption was random</li>
		</ul>
	</li>
</ul>

<h4>SexOffender Table (sexOffenders.py)</h4>
<ul>
	<li>Out of the offenses generated, 80% of them occured on the weekend (Thursday, Friday, Saturday</li>
	<li>Each sex offender attacked anywhere between 1-2 times</li>
	<li>The script selected 220 random male drinkers (210 assult females, 10 assult males)</li>
	<li>The script selected 20 random female drinkers (5 assult females, 15 assult males)</li>
	<li>10 male victims were made into sexual offenders and assulted females</li>
	<li>2 female victims were made into sexual offenders and assulted males</li>
</ul>


<h3>Patterns</h3>
<?php require_once("res/bottom.php"); ?>