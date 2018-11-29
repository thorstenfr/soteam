<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

// Admins umleiten
if($user['rollen_id']==3) {
			header("location: admin.php");
			
} 

include("templates/header.inc.php");
?>


<script>
// Fake - Buchung um Tabelle zu erzeugen
// addBuchung(\"1:".$user['id'].":".$row['auf_id']."\")
    window.onload = function () {
        console.log('Dokument geladen');
		addBuchung('0:0:0');        
    }
</script>



<div class="container main-container">

<p>Herzlich Willkommen <b><?php echo htmlentities($user['vorname']); ?></b>!</p>



<?php
/* ToDo: hier die tmp tabelle füllen
passiert altuell in sae. eventuell als funktion realisieren. dsnn wäre auch ein refresh möhlich
*/

error_log("Die Oracle-Datenbank ist nicht erreichbar!", 0);
$res = refresh_tmp();



?>


<div>
	<div id="txtHint">
	Hier wird die Übersicht geladen ...
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
	var el = document.getElementById("tmp_heute");
			if (el) {
				el.addEventListener("onchange", myFunction);
			}
	// document.getElementById("tmp_heute").addEventListener("onchange", myFunction);

function myFunction() {
    var x = document.getElementById("tmp_heute");
	document.getElementById("tmp_heute").style.color = "#ff0000"; // forecolor
	document.getElementById("tmp_heute").style.backgroundColor = "#ff0000"; // backcolor
    x.value = x.value.toUpperCase();
}
</script>
