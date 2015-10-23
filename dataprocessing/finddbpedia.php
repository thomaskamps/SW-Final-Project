<?php

function getDBPedia($name) {
	$subject = strtoupper($name);
	$subject = explode(" ", $subject);
	
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

 select ?s1 as ?c1, ( bif:search_excerpt ( bif:vector ( ". $add1. " ) , ?o1 ) ) as ?c2, ?sc, ?rank, ?g where 
  {  
        select ?s1, ( ?sc * 3e-1 ) as ?sc, ?o1, ( sql:rnk_scale ( <LONG::IRI_RANK> ( ?s1 ) ) ) as ?rank, ?g where 
        { 
          quad map virtrdf:DefaultQuadMap 
          { 
            graph ?g 
            { 
              ?s1 ?s1textp ?o1 .
              ?o1 bif:contains ' ( ". $add2. " ) ' option ( score ?sc ) .
			  ?s1 rdf:type dbo:Film .
              
            }
           }
         }
       order by desc ( ?sc * 3e-1 + sql:rnk_scale ( <LONG::IRI_RANK> ( ?s1 ) ) ) limit 1 offset 0 
      }";
	
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
	if(empty($result->rows)) {
		return "";
	} else {
		return $result->rows[0]['c1']['value'];
	}
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
		$jsonobject['dbpedia'] = getDBPedia($jsonobject['name']);
		$newjsonarray[] = $jsonobject;
	}
	
	$jsonarray = json_encode($newjsonarray);
	
	$q = "UPDATE movies SET movies = :movies WHERE id = :id";

	$stmt = $dbconn->prepare($q);
	$stmt->bindParam(':id', $result['id']);
	$stmt->bindParam('movies', $jsonarray);
	$stmt->execute();
	echo "done ".$result['id']."<br/>";
	sleep(1);
}

?>