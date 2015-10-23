<?php

function getCover($name) {
	$name = explode(" ", $name);
	foreach($name as $nam) {
		if(strlen($nam)<2) {
			unset($name[array_search($nam, $name)]);
		}
	}
	
	$url = "http://www.omdbapi.com/?type=movie&s=". implode("%20", $name);
	$result = json_decode(file_get_contents($url), true);
	
	if(!isset($result['Search'])) {
		return "";
	} else {
		return $result['Search'][0]['Poster'];
	}
	
	#print json_decode(file_get_contents($url), true)['Search'][0]['Title'];
}


$dbconn = new PDO("mysql:host=localhost;dbname=sw", "sw", "12345");
$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$q = "SELECT movies, id FROM movies";

$stmt = $dbconn->prepare($q);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($results as $result) {

	$jsonarray = json_decode($result['movies'], true);
	$newjsonarray = array();
	
	foreach($jsonarray as $jsonobject) {
		$temp = getCover($jsonobject['name']);
		if($temp != "") {
			$jsonobject['coverurl'] = $temp;
			$newjsonarray[] = $jsonobject;
		}
	}
	
	$jsonarray = json_encode($newjsonarray);
	
	$q = "UPDATE movies SET movies = :movies WHERE id = :id";

	$stmt = $dbconn->prepare($q);
	$stmt->bindParam(':id', $result['id']);
	$stmt->bindParam('movies', $jsonarray);
	$stmt->execute();
	echo "done ".$result['id']."<br/>";
}

?>