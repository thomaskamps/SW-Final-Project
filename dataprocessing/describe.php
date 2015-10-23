<?php
$dbconn = new PDO("mysql:host=localhost;dbname=sw", "sw", "12345");
$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$q = "SELECT movies FROM movies";

$stmt = $dbconn->prepare($q);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$checkarr = array();

foreach($results as $result) {

	$arr = json_decode($result['movies'], true);
	foreach($arr as $ar) {
		if(!in_array($ar['dbpedia'], $checkarr)) {
			$checkarr[] = $ar['dbpedia'];
			print "URL = \"http://dbpedia.org/sparql?query=DESCRIBE&#60;". str_replace("\\", "", $ar['dbpedia']). "&#62;\"<br/>";
		}
	}
}

?>