<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

include("templates/header.inc.php");
?>

<script>
function showBuchungen(str) {
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
        xmlhttp.open("GET","getbuchungen.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>

<div class="container main-container">

<h1>Herzlich Willkommen!</h1>

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
<h2>Buchungen</h2>
<p>Wählen Sie den Teamer aus, für den die Buchungen angezeigt werden sollen.</p>
<form>
<select name="users" onchange="showBuchungen(this.value)">
<option value="*"> - ALLE - </option>
<?php
$statement = $pdo->prepare("SELECT * FROM users ORDER BY id");
$result = $statement->execute();
$count = 1;
while($row = $statement->fetch()) {
	echo "<option value=\"".$row['id']."\">".$row['nick']."</option>";	
}
?> 
  </select>
</form>
<br>
<div id="txtHint"><b>Buchungen werden hier angezeigt...</b></div>
</div>


</div>
<?php 
include("templates/footer.inc.php")
?>
