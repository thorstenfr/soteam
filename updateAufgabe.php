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
	require_once("inc/config.inc.php");
		
	list($aufid, $beschreibung) = explode(":", $_GET['q']);
	
	/*
	$beschreibung = "Lulu";
	$aufid = 9;
	*/
	
	$tag = date("d");
	
	$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name);
	if (!$conn) {
	    die('Could not connect: ' . mysqli_error($con));
	}
	
	$sql = "UPDATE `sae_aufgabe` SET `auf_beschreibung` = '" . $beschreibung . "', `auf_updated_at` = NULL, `auf_beendet_am` = NULL WHERE `sae_aufgabe`.`auf_id` = " . $aufid;
	

	if ($conn->query($sql) === TRUE) {
	    echo "Record updated successfully";
	} else {
	    echo "Error updating record: " . $conn->error;
	}
	
	$conn->close();



?>
</body>
</html>