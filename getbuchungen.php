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

</head>
<body>


<table class="table">
	
<?php
require_once("inc/config.inc.php");
if ($_GET['q'] == "*") 
{
	$q=false;
}
else {
	$q = intval($_GET['q']);
}

$details=$_GET['c'];

if ($details=="true") {
	
	echo "<tr>";
			echo "<th>Was</th><th>Wert</th>";
	echo "</tr>";

	$sql = "SELECT sae_aufgabe.auf_beschreibung as was   , sum(buc_wert) as wert\n"

		. "from sae_buchung,sae_aufgabe\n"

		. "where sae_aufgabe.auf_id=sae_buchung.sae_aufgabe_auf_id\n";
		
		if($q) {
			$sql = $sql." AND sae_buchung.users_id=".$q;
		}
		
		$sql=$sql." GROUP BY sae_buchung.sae_aufgabe_auf_id";	
}
else {
	echo "<tr>";
			echo "<th>Nick</th><th>Datum</th><th>Aufgabe</th>";
		echo "</tr>";		
	
	$sql = "SELECT  users.nick as wer, sae_buchung.buc_created_at as wann, sae_buchung.buc_wert as wert, sae_aufgabe.auf_beschreibung as was, sae_buchung.buc_kommentar as kommentar, sae_buchung.users_id\n"
    . "FROM `sae_buchung`, users, sae_aufgabe\n"
    . "where sae_buchung.users_id=users.id\n";
	
	if($q) {
			$sql = $sql." AND sae_buchung.users_id=".$q;
		}
	
    $sql=$sql." AND sae_buchung.sae_aufgabe_auf_id=sae_aufgabe.auf_id\n"
	. "ORDER BY wann";
	
	
	
	
}

	
	
	$sql = $pdo->prepare($sql);
	
		
	$result = $sql->execute();
	
	
	
	while($row = $sql->fetch()) {
		
	if ($details=="true") {
			echo "<tr>";		
			echo "<td>".$row['was']."</td>";			
			echo "<td>".$row['wert']."</td>";		
		echo "</tr>";
	}
	else {
		
		echo "<tr>";		
			echo "<td>".$row['wer']."</td>";
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