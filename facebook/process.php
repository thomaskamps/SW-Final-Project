<?php 

if(isset($_POST['id'])) {
	
	$dbconn = new PDO("mysql:host=localhost;dbname=sw", "sw", "12345");
	$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$q = "SELECT id FROM movies";
	
	$stmt = $dbconn->prepare($q);
	$stmt->execute();
	
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$idarray = array();
	
	foreach($results as $result){
		$idarray[] = $result['id'];
	}
		
	if(in_array($_POST['id'], $idarray)) {
		echo "Helaas, u heeft al meegedaan...";
	} else {
		echo "Bedankt voor uw deelname!";
		$q = "INSERT INTO movies (id, movies, name, gender) VALUES (:id, :movies, :name, :gender)";
		$stmt = $dbconn->prepare($q);
		$stmt->bindParam(':id', $_POST['id']);
		$stmt->bindParam(':movies', $_POST['movies']);
		$stmt->bindParam(':name', $_POST['name']);
		$stmt->bindParam(':gender', $_POST['gender']);
		$stmt->execute();
	}
} else {
	echo "Deze pagina is niet toegankelijk...";
}
	
	?>