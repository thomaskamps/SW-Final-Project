<?php 
if(isset($_GET['genre'])) {
	$query = "SELECT DISTINCT * WHERE { ?film <http://purl.org/dc/terms/subject> <". $_GET['genre']. "> . ?film rdfs:label ?label . ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover . FILTER (lang(?label) = 'en') . }";
	
	$fields = array('query'=>$query);
	$fields_string = http_build_query($fields);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://ec2-52-19-59-116.eu-west-1.compute.amazonaws.com/MovieNew/query");
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array ("Accept: application/sparql-results+json"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	print $response;
}

?>