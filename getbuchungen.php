<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//�berpr�fe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();



?>



<!DOCTYPE html>
<html>
<head>

</head>
<body>


<table class="table">
	
<?php
require_once("inc/config.inc.php");

$alle=$_GET['q'];
$details=$_GET['c'];

/* Gesamt-Buchung berechnen */
	$sql = "SELECT sum(sae_buchung.buc_wert) as summe FROM `sae_buchung` WHERE 1";
	if($alle=="false") {
			$sql = $sql." AND sae_buchung.users_id=".$user['id'];
	}
	$sql = $pdo->prepare($sql);		
	$result = $sql->execute();
	
	while($row = $sql->fetch()) {
			$mySumme = $row['summe'];
	}

if ($details=="false") {
	
	echo "<tr>";
			echo "<th>Was</th><th>Prozent</th><th>Wert</th>";
	echo "</tr>";

	$sql = "SELECT sae_aufgabe.auf_beschreibung as was   , sum(buc_wert) as wert\n"

		. "from sae_buchung,sae_aufgabe\n"

		. "where sae_aufgabe.auf_id=sae_buchung.sae_aufgabe_auf_id\n";
		
		if($alle=="false") {
			$sql = $sql." AND sae_buchung.users_id=".$user['id'];
		}
		
		$sql=$sql." GROUP BY sae_buchung.sae_aufgabe_auf_id ORDER BY wert DESC";	
}
else {
	echo "<tr>";
			echo "<th>Datum</th><th>Aufgabe</th>";
		echo "</tr>";		
	
	$sql = "SELECT  users.nick as wer, sae_buchung.buc_created_at as wann, sae_buchung.buc_wert as wert, sae_aufgabe.auf_beschreibung as was, sae_buchung.buc_kommentar as kommentar, sae_buchung.users_id\n"
    . "FROM `sae_buchung`, users, sae_aufgabe\n"
    . "where sae_buchung.users_id=users.id\n";
	
	if($alle=="false") {
			$sql = $sql." AND sae_buchung.users_id=".$user['id'];
		}
	
    $sql=$sql." AND sae_buchung.sae_aufgabe_auf_id=sae_aufgabe.auf_id\n"
	. "ORDER BY wann";
	
	
	
	
}

	
	
	$sql = $pdo->prepare($sql);
	
		
	$result = $sql->execute();
	
	
	
	while($row = $sql->fetch()) {
		$prozent=((intval($row['wert'])/intval($mySumme))*100);
		$prozent=number_format($prozent, 2, ',', '.');
		
	if ($details=="false") {
			echo "<tr>";		
			echo "<td>".$row['was']."</td>";			
			echo "<td>".$prozent."%</td>";			
			echo "<td>".$row['wert']."</td>";		
		echo "</tr>";
	}
	else {
		
		echo "<tr>";					
			echo "<td>".$row['wann']."</td>";
			echo "<td>".$row['was']."</td>";		
		echo "</tr>";
	}
	}
	
	echo "</table>";
	
	
	
?>
<?php


?>
</body>
</html>