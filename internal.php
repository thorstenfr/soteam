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

Hallo <?php echo htmlentities($user['vorname']); ?>,<br>
Herzlich Willkommen im internen Bereich!<br><br>


<div class="panel panel-default">
 
<table class="table">
<tr>
	<th>#</th>
	<th>Vorname</th>
	<th>Nachname</th>
	<th>E-Mail</th>
</tr>
<?php 
$statement = $pdo->prepare("SELECT * FROM users ORDER BY id");
$result = $statement->execute();
$count = 1;
while($row = $statement->fetch()) {
	echo "<tr>";
	echo "<td>".$count++."</td>";
	echo "<td>".$row['vorname']."</td>";
	echo "<td>".$row['nachname']."</td>";
	echo '<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>';
	echo "</tr>";
}
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
