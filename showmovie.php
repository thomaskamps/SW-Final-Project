<?php
if(isset($_GET['movie'])) {
	
	$movie = $_GET['movie'];
	
	$query = "PREFIX dbo: <http://dbpedia.org/ontology/> 
		  SELECT * WHERE {
		  ?film rdfs:label ?label .
		  FILTER(lang(?label) = 'en') .
		  ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover .
		  ?film dbo:abstract ?abstract . 
		  FILTER(lang(?abstract) = 'en') .
		  OPTIONAL { ?film <http://dbpedia.org/property/released> ?released }
		  OPTIONAL { ?film <http://www.w3.org/ns/prov#wasDerivedFrom> ?wiki }
		  }";
	$query = str_replace("?film", "<".$movie.">", $query);
	
	require_once( "sparqllib.php" );

	$db = sparql_connect("http://ec2-52-19-59-116.eu-west-1.compute.amazonaws.com/MovieNew/query?reasoning=true&");
	
	if(!$db) {
		print $db->errno().": ".$db->error()."\n";
		exit;
	}
	
	$result = $db->query($query); 
	
	if(!$result) {
		print $db->errno().": ".$db->error()."\n";
		exit;
	}
	
	$result = $result->rows[0];
	
	$cover = "imgproxy.php?url=".$result['cover']['value'];
	$abstract = $result['abstract']['value'];
	$title = $result['label']['value'];
	if($result['released']['value'] != "") { $released  = $result['released']['value']; }
	if($result['wiki']['value'] != "") { $wiki = $result['wiki']['value']; }
	
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Semantic Movie Recommendation Application</title>
		<script src="static/js/jquery-1.11.0.min.js" type='text/javascript'></script>
		<link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="static/css/bootstrap-theme.min.css">
		<script type="text/javascript" src="static/js/bootstrap.min.js"></script>
		
	</head>

	<body>
		<div class="container" style="margin-top: 30px;">
			
			<div class='row'>
				<div class='col-lg-4'>
					<div class='panel panel-default'>
						<img src="<?php echo $cover; ?>" alt="cover" style="width: 80%; margin-left: 10%; margin-top: 33px; margin-bottom: 33px;" />
					</div>
				</div>
				<div class='col-lg-8'>
					<div class='panel panel-default' style="padding: 20px;">
						<a onclick="history.go(-1)"><button class="btn btn-primary"><-- Back to previous page</button></a>
						<h3><?php echo $title; ?></h3>
						<p><?php echo $abstract; ?></p>
						<?php if(isset($released)) { echo "<p><b>Released: ".$released."</b></p>"; } ?>
						<?php if(isset($wiki)) { echo "<a href=\"".$wiki."\" target=\"_blank\"><button class=\"btn btn-primary\">More information</button></a>"; } ?>
					</div>
				</div>
			</div>
			
		</div>
	<script src="static/js/scripts.js" type='text/javascript'></script>
	</body>
</html>

<?php
}
?>