<?php 
if(isset($_GET['url'])) {
	
	$url = $_GET['url'];
	
	header('Content-Type: image/jpeg');
	
	$img = @imagecreatefromjpeg($url);
	
	imagejpeg($img);
	imagedestroy($img);
	
}
?>