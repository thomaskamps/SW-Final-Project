<?php
$dbconn = new PDO("mysql:host=localhost;dbname=sw", "sw", "12345");
$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$q = "SELECT * FROM movies";

$stmt = $dbconn->prepare($q);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$filmarr = array();

foreach($results as $result) {
	
	$temp = json_decode($result['movies'], true);
	foreach($temp as $movie) {
		if(!in_array($movie['dbpedia'], $filmarr)) {
			print "&#60;".$movie['dbpedia']."&#62; &#60;http://thomaskamps.nl/onotolgy.owl#hasCover&#62; \"".$movie['coverurl']."\" .<br/>";
			$filmarr[] = $movie['dbpedia'];
		}
	}
}




?>