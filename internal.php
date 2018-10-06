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

var xmlhttp = new XMLHttpRequest();

xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        myObj = JSON.parse(this.responseText);
        document.getElementById("demo").innerHTML = myObj.name;
    }
};
xmlhttp.open("GET", "demo_file.php", true);
xmlhttp.send();

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
		echo "<td>".$first."</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td>Letzte Buchung</td>";
		echo "<td>".$last."</td>";
	echo "</tr>";

?>
</table>

<?php	
	if ($user['rollen_id']>1) {
 ?>
<h2>Buchungen</h2>

<form>
<!--
<p>Wählen Sie den Teamer aus, für den die Buchungen angezeigt werden sollen.</p>
<select id="mySelect" name="users" onchange="showBuchungen(this.value)">
<option value="">Teamer auswählen</option>
<option value="*">ALLE</option>
<?php
$statement = $pdo->prepare("SELECT * FROM users ORDER BY id");
$result = $statement->execute();
$count = 1;
while($row = $statement->fetch()) {
	echo "<option value=\"".$row['id']."\">".$row['nick']."</option>";	
}
?> 
  </select>
   <br>
  -->
 

 
	Anzeigen der Buchungen: 
  <label for="alle">alle</label><input  onchange="showBuchungen(1)"  id="alle" type="radio" name="wer" value="alle" checked>  oder nur 
  
  <label for="mir">meine</label>
  <input onchange="showBuchungen(1)"  id="mir" type="radio" name="wer" value="ich">

  <label for="cb_detail">. Details anzeigen: </label><input type="checkbox" name="details" value="detail" id="cb_detail" onchange="showBuchungen(1)"> 
</form>
<br>
<div id="txtHint"><b>Buchungen werden hier angezeigt...</b></div>
</div>
<?php
	}
?>




</div>
<?php 
include("templates/footer.inc.php")
?>
