<!DOCTYPE html>
<html>
<head>

</head>
<body>


<table class="table">
	<tr>
		<th>Nick</th><th>Datum</th><th>Aufgabe</th>
	</tr>
<?php
require_once("inc/config.inc.php");
if ($_GET['q'] == "*") 
{
	$str = "OR 1";
}
else {
	$str = "";
}


$q = intval($_GET['q']);

	$sql_txt = "SELECT  users.nick as wer, sae_buchung.buc_created_at as wann, sae_buchung.buc_wert as wert, sae_aufgabe.auf_beschreibung as was, sae_buchung.buc_kommentar as kommentar, sae_buchung.users_id\n"

    . "FROM `sae_buchung`, users, sae_aufgabe\n"

    . "where sae_buchung.users_id=users.id\n"
	
	. "AND sae_buchung.users_id=".$q." ".$str."\n"
	
    . "AND sae_buchung.sae_aufgabe_auf_id=sae_aufgabe.auf_id";
	
	
	$sql = $pdo->prepare($sql_txt);
	
		
	$result = $sql->execute();
	
	
	while($row = $sql->fetch()) {
		
	echo "<tr>";		
		echo "<td>".$row['wer']."</td>";
		echo "<td>".$row['wann']."</td>";
		echo "<td>".$row['was']."</td>";		
	echo "</tr>";
	}
	
	
	echo "</table>";
	
	
	
?>
<?php


?>
</body>
</html>