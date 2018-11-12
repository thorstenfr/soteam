<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

?>
<!DOCTYPE html>
<html>
<head>
<style>

</style>
</head>
<body>

<?php
require_once("inc/config.inc.php");

// Beispiel 2
$data = "foo:*:1023:1000::/home/foo:/bin/sh";
list($wert, $user_id, $auf_id, $kommentar) = explode(":", $_GET['q']);
/*
echo $user_id; // foo
echo $auf_id; // *
echo $wert;
echo $kommentar;
echo "<br>";
*/

$tag = date("d");

$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name);
if (!$conn) {
    die('Could not connect: ' . mysqli_error($con));
}





$stmt = $conn->prepare("INSERT INTO sae_buchung (buc_wert, users_id, sae_aufgabe_auf_id, buc_kommentar, sae_team_id)
VALUES (?, ?, ?, ?, ?)");

$stmt->bind_param("iiisi", $wert, $user_id, $auf_id, $kommentar, $user['sae_team_id']);

$stmt->execute();
$stmt->close();

// Tmp-Tabelle aktualisieren
$res=refresh_tmp();

$sql = "select tmp_user_id, tmp_user_nick, tmp_heute, tmp_woche, tmp_monat, tmp_jahr\n"

    . "FROM tmp_buchung\n"
	. "WHERE tmp_user_nick<>'deakt'";

	
$result = mysqli_query($conn,$sql);


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

mysqli_close($conn);



?>
</body>
</html>