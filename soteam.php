<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//�berpr�fe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

// Admins umleiten
if($user['rollen_id']==3) {
			header("location: admin.php");
			
} 

include("templates/header.inc.php");
?>

<div class="container main-container">

<p>Herzlich Willkommen <b><?php echo htmlentities($user['vorname']); ?></b>!</p>



<?php
/* ToDo: hier die tmp tabelle f�llen
passiert altuell in sae. eventuell als funktion realisieren. dsnn w�re auch ein refresh m�hlich
*/

$res = refresh_tmp();

$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name);
if (!$conn) {
    die('Could not connect: ' . mysqli_error($con));
}

/*

SELECT SUM(sae_buchung.buc_wert) AS Wert, sae_aufgabe.auf_beschreibung FROM `sae_buchung`, sae_aufgabe 
WHERE sae_aufgabe.auf_id=sae_buchung.sae_aufgabe_auf_id
AND users_id=2 AND DAY(buc_created_at)=DAY(NOW()) AND MONTH(buc_created_at)=MONTH(NOW()) AND YEAR(buc_created_at)=YEAR(NOW())
GROUP BY sae_aufgabe.auf_beschreibung
ORDER BY Wert DESC
LIMIT 3


*/


$sql_top3_user_heute = "SELECT SUM(sae_buchung.buc_wert) AS Wert, sae_aufgabe.auf_beschreibung\n"

    . "FROM `sae_buchung`, sae_aufgabe\n"

    . "WHERE sae_aufgabe.auf_id=sae_buchung.sae_aufgabe_auf_id\n"

    . "AND users_id=2 AND DAY(buc_created_at)=DAY(NOW()) AND MONTH(buc_created_at)=MONTH(NOW()) AND YEAR(buc_created_at)=YEAR(NOW())\n"

    . "GROUP BY sae_aufgabe.auf_beschreibung\n"

    . "ORDER BY Wert DESC\n"

    . "LIMIT 3";



$sql = "select tmp_user_id, tmp_user_nick, tmp_heute, tmp_woche, tmp_monat, tmp_jahr\n"

    . "FROM tmp_buchung\n"
	. "WHERE tmp_user_nick<>'deakt'\n"
	. "AND tmp_team_id=".$user['sae_team_id']."";
	
	
$result = mysqli_query($conn,$sql);
?>


<div>
	<div id="txtHint">
	

		<?php
			$tag = date("d");
			
			echo "<table class=\"table table-bordered\"> 
				<tr>
				<th>Name</th>
				<th>Heute (Std)</th>
				<th>Woche (Std)</th>
				<th>Monat (Std)</th>
				<th>Jahr (Std)</th>
				</tr>";
			
			while($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['tmp_user_nick'] . "</td>";    
				echo "<td id=\"tmp_heute\">" . $row['tmp_heute']/4 . "</td>";
				echo "<td>" . $row['tmp_woche']/4 . "</td>";
				echo "<td>" . $row['tmp_monat']/4 . " (" . round(($row['tmp_monat']/4)/$tag,2) . " Std/Tag) </td>";
				echo "<td>" . $row['tmp_jahr']/4 . "</td>";
				echo "</tr>";
			}

			echo "</table>";

				
		?>
		

		
		
		
		</div> 
</div>


<div class="list-group">

	<div id="auf_liste">
		<?php 

			$statement = $pdo->prepare("SELECT  sae_aufgabe.auf_beschreibung, auf_id FROM sae.sae_aufgabe, users, users_has_sae_aufgabe WHERE id=users_id AND auf_id=sae_aufgabe_auf_id AND id=".$user['id']." AND sae_aufgabe.sae_team_id=".$user['sae_team_id']."");
			$result = $statement->execute();
			$count = 1;

			while($row = $statement->fetch()) {
				echo "<button data-toggle=\"tooltip\" title=\"Klicken zur Aufwandserfassung\" type='button' onclick='addBuchung(\"1:".$user['id'].":".$row['auf_id']."\")' class='list-group-item list-group-item-action'>".$row['auf_beschreibung']."</button>";

			}
		?>
	</div>
	<br>
	<label for="kommentar">Kommentar:</label>
	<input type="text" name="kommentar" id="kommentar" placeholder="Kommentar zur Buchung">
</div>



<?php 
include("templates/footer.inc.php")
?>



<script>
document.getElementById("tmp_heute").addEventListener("onchange", myFunction);

function myFunction() {
    var x = document.getElementById("tmp_heute");
	document.getElementById("tmp_heute").style.color = "#ff0000"; // forecolor
	document.getElementById("tmp_heute").style.backgroundColor = "#ff0000"; // backcolor
    x.value = x.value.toUpperCase();
}
</script>
