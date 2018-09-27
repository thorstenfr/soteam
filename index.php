<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

include("templates/header.inc.php");
?>

<div class="container main-container">

<p>Herzlich Willkommen <b><?php echo htmlentities($user['vorname']); ?></b>!</p>

<?php
$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name);
if (!$conn) {
    die('Could not connect: ' . mysqli_error($con));
}
$sql = "select tmp_user_id, tmp_user_nick, tmp_heute, tmp_woche, tmp_monat, tmp_jahr\n"

    . "FROM tmp_buchung\n"
	. "WHERE tmp_user_nick<>'deakt'";
	
	
$result = mysqli_query($conn,$sql);
?>


<div>
	<div id="txtHint">
	

		<?php
			
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
				echo "<td>" . $row['tmp_monat']/4 . "</td>";
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

			$statement = $pdo->prepare("SELECT  sae_aufgabe.auf_beschreibung, auf_id FROM sae.sae_aufgabe, users, users_has_sae_aufgabe WHERE id=users_id AND auf_id=sae_aufgabe_auf_id AND id=".$user['id']);
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

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>


<script>
function changeColor(id)
{
  document.getElementById(id).style.color = "#ff0000"; // forecolor
  document.getElementById(id).style.backgroundColor = "#ff0000"; // backcolor
}
</script>


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
