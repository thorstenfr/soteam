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
<h1>Admin-Bereich</h1>
<p>Herzlich Willkommen <b><?php echo htmlentities($user['vorname']); ?></b>!</p>



<?php
/* ToDo: hier die tmp tabelle füllen
passiert altuell in sae. eventuell als funktion realisieren. dsnn wäre auch ein refresh möhlich
*/

$res = refresh_tmp();

$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name);
if (!$conn) {
    die('Could not connect: ' . mysqli_error($con));
}
$sql = "SELECT `auf_id`,`auf_kurz`,`auf_beschreibung`,`auf_daueraufgabe`,`sae_tae_fk` FROM `sae_aufgabe` WHERE `sae_team_id` = " . $user['sae_team_id'];


$result = mysqli_query($conn,$sql);
?>


<div>
	<div id="txtHint">
		
		<?php
			$tag = date("d");
			
			echo "<select>";
			
			while($row = mysqli_fetch_array($result)) {
				
					echo "<option value=" . $row['auf_id'] . ">" . $row['auf_beschreibung'] . "</option>";
				
			}

			echo "</select>";

				
		?>
		
		

		
		
		
		</div> 
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
