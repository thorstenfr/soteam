<!DOCTYPE html>
<html>
<head>
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
    padding: 5px;
}

th {text-align: left;}
</style>
</head>
<body>

<?php
require_once("inc/config.inc.php");
$q = intval($_GET['q']);
echo "<h1>" .$q. "</h1>";

	$sql = $pdo->prepare("SELECT  users.nick as wer, sae_buchung.buc_created_at as wann, sae_buchung.buc_wert as wert, sae_aufgabe.auf_beschreibung as was, sae_buchung.buc_kommentar as kommentar, sae_buchung.users_id\n"

    . "FROM `sae_buchung`, users, sae_aufgabe\n"

    . "where sae_buchung.users_id=users.id\n"
	. "AND sae_buchung.users_id=".$q."\n"
    . "AND sae_buchung.sae_aufgabe_auf_id=sae_aufgabe.auf_id");
	
	
	$result = $sql->execute();
	echo "<table>";
	
	while($row = $sql->fetch()) {
		
	echo "<tr>";
		echo "<td>".$row['users_id']."</td>";	
		echo "<td>".$row['wer']."</td>";
		echo "<td>".$row['wann']."</td>";
		echo "<td>".$row['was']."</td>";
		echo "<td>".$row['kommentar']."</td>";	
	echo "</tr>";
	}
	
?>
</table>
</body>
</html>