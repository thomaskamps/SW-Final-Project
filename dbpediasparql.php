<?php
	
function dbquery($query) {
	require_once( "sparqllibor.php" );

	$db = sparql_connect("http://dbpedia.org/sparql");
	
	if(!$db) {
		print $db->errno().": ".$db->error()."\n";
		exit;
	}
	
	$result = $db->query($query); 
	
	if(!$result) {
		print $db->errno().": ".$db->error()."\n";
		exit;
	}
	return $result;
}

if(isset($_GET['subject'])) {
	$subject = $_GET['subject'];
	$subject = strtoupper($subject);
	$subject = explode(" ", $subject);
	$subject = preg_replace("/[^A-Z]/", "", $subject);
	
	
	$add1 = "";
	$count = 1;
	foreach($subject as $sub) {
		if($count<count($subject)) {
			$add1.="'".$sub."', ";
			$count += 1;
		} else {
			$add1.="'".$sub."'";
		}
	}
	
	$add2 = "";
	$count = 1;
	foreach($subject as $sub) {
		if($count<count($subject)) {
			$add2.=$sub." AND ";
			$count += 1;
		} else {
			$add2.=$sub." ";
		}
	}	
	
	
	$query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX dbo: <http://dbpedia.org/ontology/>
PREFIX db: <http://dbpedia.org/>

 select distinct ?s1 as ?c1, ( bif:search_excerpt ( bif:vector ( ". $add1. " ) , ?o1 ) ) as ?c2, ?sc, ?rank, ?g where 
  {  
        select distinct ?s1, ( ?sc * 3e-1 ) as ?sc, ?o1, ( sql:rnk_scale ( <LONG::IRI_RANK> ( ?s1 ) ) ) as ?rank, ?g where 
        { 
          quad map virtrdf:DefaultQuadMap 
          { 
            graph ?g 
            { 
              ?s1 rdfs:label ?o1 .
              ?o1 bif:contains ' ( ". $add2. " ) ' option ( score ?sc ) .
			  ?s1 rdf:type dbo:Film .
              
            }
           }
         }
       order by desc ( ?sc * 3e-1 + sql:rnk_scale ( <LONG::IRI_RANK> ( ?s1 ) ) ) limit 1 offset 0 
      }";
	print_r(json_encode(dbquery($query)));
}


if(isset($_GET['movie'])) {
	$query = "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX dbo: <http://dbpedia.org/ontology/>
PREFIX dbr: <http://dbpedia.org/resource/>

SELECT DISTINCT ?genre ?label ?genrelabel WHERE { 
<". $_GET['movie']. "> rdfs:label ?label .
<". $_GET['movie']. "> <http://purl.org/dc/terms/subject> ?genre .
?genre rdfs:label ?genrelabel .
FILTER (lang(?label) = 'en') .
FILTER (lang(?genrelabel) = 'en') .
}";
	$result = dbquery($query);
	
	$query = "SELECT DISTINCT * WHERE { { ?film <http://purl.org/dc/terms/subject> <". $result->rows[0]['genre']['value']. "> . } ";
	$count = count($result->rows);
	
	for($i = 1; $i < $count; $i++) {
		$queryadd = "UNION { ?film <http://purl.org/dc/terms/subject> <". $result->rows[$i]['genre']['value']. "> . } ";
		$query .= $queryadd;
	}
	
	$query .= " ?film rdfs:label ?label . ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover . FILTER (lang(?label) = 'en') . }";
	
	$fields = array('query'=>$query);
	$fields_string = http_build_query($fields);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://ec2-52-19-59-116.eu-west-1.compute.amazonaws.com/MovieNew/query");
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array ("Accept: application/sparql-results+json"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	$response = json_decode($response, true);
	$response['label'] = $result->rows[0]['label']['value'];
	$genre = array();
	foreach($result->rows as $genrelabel) { $genre[] = array("label" => $genrelabel['genrelabel']['value'], "genre" => $genrelabel['genre']['value']); }
	$response['genre'] = $genre;
	$response = json_encode($response);
	print $response;
}

?>