<?php
require_once( "sparqllib.php" );

if(isset($_GET['reasoning']) && isset($_GET['query'])) {
	
	if($_GET['reasoning'] == false) {
		$db = sparql_connect("http://ec2-52-19-59-116.eu-west-1.compute.amazonaws.com/MovieNew/query?");
	} elseif($_GET['reasoning'] == true) {
		$db = sparql_connect("http://ec2-52-19-59-116.eu-west-1.compute.amazonaws.com/MovieNew/query?reasoning=true&");
	}
	
	if(!$db) {
		print $db->errno().": ".$db->error()."\n";
		exit;
	}
	
	$result = $db->query($_GET['query']); 
	
	if(!$result) {
		print $db->errno().": ".$db->error()."\n";
		exit;
	}
	
	print(json_encode($result));
	
} else {
	
	print "Er heeft zich een probleem voorgedaan..";
	
}
?>