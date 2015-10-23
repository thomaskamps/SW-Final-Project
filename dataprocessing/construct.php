<?php
	
$prefix = "http://thomaskamps.nl/onotolgy.owl#";
$dbconn = new PDO("mysql:host=localhost;dbname=sw", "sw", "12345");
$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$q = "SELECT * FROM movies";

$stmt = $dbconn->prepare($q);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $result) {
	
	print "&#60;".$prefix.str_replace(" ", "", $result['name'])."&#62; rdf:type &#60;".$prefix."Person&#62; .<br/>";
	print "&#60;".$prefix.str_replace(" ", "", $result['name'])."&#62; &#60;".$prefix."hasGender&#62; &#60;".$prefix.ucwords($result['gender'])."&#62; .<br/>";
	print "&#60;".$prefix.str_replace(" ", "", $result['name'])."&#62; rdfs:label \"".$result['name']."\" .<br/>";
	
	$movies = json_decode($result['movies'], true);
	
	foreach($movies as $movie) {
		
		print "&#60;".$prefix.str_replace(" ", "", $result['name'])."&#62; &#60;".$prefix."hasLiked&#62; &#60;".$movie['dbpedia']."&#62; .<br/>";
		
	}
		
}
?>