<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

include("templates/header.inc.php");

?>

<!--
<h2>Get data as JSON from a PHP file on the server.</h2>

<p id="demo"></p>
-->



<script>
function mySelectSaeAufgabeFunction() {
    var x = document.getElementById("mySelectSaeAufgabe").value;
	 var res = x.split(";");
	 var auf_id = res[1];
    document.getElementById("akt_auf_id").innerHTML = res[0];
	document.getElementById("akt_auf_name").innerHTML = res[1];
	
}
</script>


<script>
function updateAufgabe() {
			var aufid = document.getElementById("akt_auf_id").innerHTML;
			var aufbeschreibung = document.getElementById("neu_auf_name").value;
			var str = aufid+":"+aufbeschreibung;
			
		if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
		
        xmlhttp.open("GET","updateAufgabe.php?q="+str,true);
        xmlhttp.send();
    }
		
		
		
   
}
</script>


<script>
function showBuchungen(str) {
	
	var myCheckBox = document.getElementById("cb_detail");  
	var myRadioButton = document.getElementById("alle");
	
	// var myQueryId = document.querySelector("#mySelect").value;

	
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getbuchungen.php?q="+myRadioButton.checked+"&c="+myCheckBox.checked+"",true);
        xmlhttp.send();
    }
}
</script>

<div class="container main-container">

	<h1>Interner Bereich</h1>
	
	
	
	<div class="panel panel-default">
	 
		<table class="table">
		
		<?php
		$statement = $pdo->prepare("SELECT sum(sae_buchung.buc_wert) as sum FROM `sae_buchung` where sae_buchung.users_id=".$user['id']."");
		$result = $statement->execute();
		while($row = $statement->fetch()) {
			$summe = $row['sum'];
		}
	
		$statement = $pdo->prepare("SELECT sae_buchung.buc_created_at as las FROM `sae_buchung`  where sae_buchung.users_id=".$user['id']."  ORDER by sae_buchung.buc_created_at DESC LIMIT 1");
		$result = $statement->execute();
		while($row = $statement->fetch()) {
			$last = $row['las'];
		}

		$statement = $pdo->prepare("SELECT sae_buchung.buc_created_at as fir FROM `sae_buchung`  where sae_buchung.users_id=".$user['id']."  ORDER by sae_buchung.buc_created_at ASC LIMIT 1");
		$result = $statement->execute();
		while($row = $statement->fetch()) {
			$first = $row['fir'];
		}

		$sql = "SELECT sae_team.bezeichnung as bez \n"
		
		    . "FROM `sae_team` , users\n"
		
		    . "where users.sae_team_id=sae_team.id\n"
		
		    . "and users.id=".$user['id']."";
			
		$statement = $pdo->prepare($sql);
		$result = $statement->execute();
		while($row = $statement->fetch()) {
			$bezeichnung = $row['bez'];
		}
		
		$sql = "SELECT sae_rollen.bezeichnung as rol\n"
		
		    . "FROM `sae_rollen`, users\n"
		
		    . "where users.rollen_id=sae_rollen.id\n"
		
		    . "and users.id=".$user['id']."";
			
		$statement = $pdo->prepare($sql);
		$result = $statement->execute();
		while($row = $statement->fetch()) {
			$rolle = $row['rol'];
		}
		
			
		
		?> 

		<?php
			echo "<tr>";
				echo "<td>Team</td>";
				echo "<td>".$bezeichnung."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Rolle</td>";
				echo "<td>".$rolle."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Vorname</td>";
				echo "<td>".$user['vorname']."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Nachname</td>";
				echo "<td>".$user['nachname']."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Nick</td>";
				echo "<td>".$user['nick']."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Buchungen</td>";
				echo "<td>".$summe."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Erste Buchung</td>";
				if (isset($first)) {
		 		   // Die Variable ist gesetzt, also wird etwas ausgegeben.
				   echo "<td>".$first."</td>";
				}
				else {
					echo "<td>Keine Buchung vorhanden</td>";
				}
				
			echo "</tr>";
			echo "<tr>";
				echo "<td>Letzte Buchung</td>";
				if (isset($last)) {
		 		   // Die Variable ist gesetzt, also wird etwas ausgegeben.
				   echo "<td>".$last."</td>";
				}
				else {
					echo "<td>Keine Buchung vorhanden</td>";
				}
			echo "</tr>";
		
		?>
		</table>
		
		<?php	
			if ($user['rollen_id']>1) {
		 ?>
		 
		 <div ng-app="">
		 Auswertung: <input type="checkbox" ng-model="myAuswertungen">
		 Administration: <input type="checkbox" ng-model="myAdministration">
		
			<div ng-show="myAuswertungen">
				<h2>Auswertung</h2>
				<h3>Buchungen</h3>
				Anzeigen der Buchungen: 
				  <label for="alle">alle</label><input  onchange="showBuchungen(1)"  id="alle" type="radio" name="wer" value="alle" checked>  oder nur 
				  
				  <label for="mir">meine</label>
				  <input onchange="showBuchungen(1)"  id="mir" type="radio" name="wer" value="ich">
				
				  <label for="cb_detail">. Details anzeigen: </label><input type="checkbox" name="details" value="detail" id="cb_detail" onchange="showBuchungen(1)"> 
				
					<br>
					<div id="txtHint"><b>Buchungen werden hier angezeigt...</b></div>
					</div>
			<div ng-show="myAdministration">
				<h2>Administration</h2>
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
							
							echo "<select id=\"mySelectSaeAufgabe\" onchange=\"mySelectSaeAufgabeFunction()\">";
							
							while($row = mysqli_fetch_array($result)) {
								
									echo "<option value='" . $row['auf_id'] . ";" . $row['auf_beschreibung'] . "" . "''>" . $row['auf_beschreibung'] . "</option>";
								
							}
							echo "</select>";
						?>
						<p id="p_akt_auf_id">Aktueller ID: <span id="akt_auf_id"></span></p>
						<p id="p_akt_auf_name">Aktueller Beschreibung: <span id="akt_auf_name"></span></p>
						<label for="neu_auf_name">Neue Beschreibung:</label>
						<input type="text" name="neu_auf_name" id="neu_auf_name" placeholder="Neue Beschreibung der Tätigkeit">
						<button onclick="updateAufgabe()">Speichern</button>
					</div> 
				</div>

			</div>
		</div>

		
	</div>
	<?php
		}
	?>
</div>
<?php 
include("templates/footer.inc.php")
?>
