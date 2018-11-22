<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

?>
<!DOCTYPE html>
<html>
<head>
<style>

</style>
</head>
<body>

<?php


	list($aufid, $beschreibung, $taetid, $auf_daueraufgabe) = explode(":", $_GET['q']);
	
		
	$tag = date("d");
	
	$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name);
	if (!$conn) {
	    die('Could not connect: ' . mysqli_error($con));
	}
	
	$sql = "UPDATE `sae_aufgabe` SET `auf_beschreibung` = '" . $beschreibung . "' WHERE `sae_aufgabe`.`auf_id` = " . $aufid . " AND sae_team_id=" . $user['sae_team_id'];
	
	
	
	if ($conn->query($sql) === TRUE) {
	    echo "Record updated successfully";
	} else {
	    echo "Error updating record: " . $conn->error;
	}
	
	if ($auf_daueraufgabe == 1) 
	{
		// Bei Dueraufgaben auch die entsprechende Tätigkeit neu schreiben
		$sql = "UPDATE `sae_taetigkeit` SET `tae_bezeichnung` = '" . $beschreibung . "' WHERE `sae_taetigkeit`.`tae_id` = " . $taetid . " AND sae_team_id=" . $user['sae_team_id'];
		
		if ($conn->query($sql) === TRUE) {
			echo "Record updated successfully";
		} else {
			echo "Error updating record: " . $conn->error;
		}
	}
	
	
	
	$conn->close();



?>
</body>
</html>