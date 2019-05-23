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

	error_log("aufid: " . $aufid, 0);
	if ($aufid=='-99') {
		$kurz = substr($beschreibung, 0, 5);  
		$sql = "INSERT INTO `sae_aufgabe` (`auf_id`, `auf_kurz`, `auf_beschreibung`, `auf_daueraufgabe`,  `sae_tae_fk`, `sae_team_id`) VALUES (NULL, '" . $kurz . "', '" . $beschreibung . "', 1,  1, 99)";
	
	}
	else 
	{
		$sql = "UPDATE `sae_aufgabe` SET `auf_beschreibung` = '" . $beschreibung . "' WHERE `sae_aufgabe`.`auf_id` = " . $aufid . " AND sae_team_id=" . $user['sae_team_id'];
	
	}
	
	error_log("sql: " . $sql, 0);
	
	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully";
		error_log("ok!",0);
		
		
	} else {
		echo "Error updating record: " . $conn->error;
		error_log("Fehler: " . $conn->error,0);
	}
	if ($aufid=='-99') {
		// Neuer Daueraufgabe wird jedem Teamer zugeordnet
		$id=$conn->insert_id;
		$sql = "INSERT INTO users_has_sae_aufgabe (sae_aufgabe_auf_id, users_id)
			SELECT ".$id.", id
			FROM   users
			WHERE sae_team_id=" . $user['sae_team_id'];
			if ($conn->query($sql) === TRUE) {
				error_log("Record updated successfully");
			} else {
				error_log("Error updating record: " . $conn->error);
			}
	}
	if ($auf_daueraufgabe == 1000) 
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

