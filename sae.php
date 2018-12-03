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
		<link rel="stylesheet" href="css/style.css">
	</head>
<body>

<?php


// Beispiel 2
$data = "foo:*:1023:1000::/home/foo:/bin/sh";
list($wert, $user_id, $auf_id, $neue_aufgabe,$kommentar) = explode(":", $_GET['q']);
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

if ($neue_aufgabe=="1") {
	// Neue Aufgabe soll erstellt werden
	
	// Tätigkeits-ID ermitteln
	$sql = "SELECT `sae_tae_fk` FROM `sae_aufgabe` WHERE `auf_id` = " . $auf_id;
	
	$sql = "INSERT INTO `sae_aufgabe` (`auf_id`, `auf_kurz`, `auf_beschreibung`, `auf_daueraufgabe`, `auf_created_at`, `auf_updated_at`, `auf_beendet_am`, `sae_tae_fk`, `sae_team_id`) VALUES (NULL, \'e556\', \'E556 Image\', \'0\', CURRENT_TIMESTAMP, NULL, NULL, \'3\', \'1\')";
	
}


if ($wert!=0) {
	// DS soll nicht geschrieben werden, nur anzeige
	$stmt = $conn->prepare("INSERT INTO sae_buchung (buc_wert, users_id, sae_aufgabe_auf_id, buc_kommentar, sae_team_id)
		VALUES (?, ?, ?, ?, ?)");

	$stmt->bind_param("iiisi", $wert, $user_id, $auf_id, $kommentar, $user['sae_team_id']);

	$stmt->execute();
	$stmt->close();
}

// Tmp-Tabelle aktualisieren
$res=refresh_tmp();


$sql = "select tmp_user_id, tmp_user_nick, tmp_heute, tmp_woche, tmp_monat, tmp_jahr, tmp_jahr_top1_bez,tmp_jahr_top1_wert,tmp_jahr_top2_bez,tmp_jahr_top2_wert,tmp_jahr_top3_bez,tmp_jahr_top3_wert,tmp_monat_top1_bez,tmp_monat_top1_wert,tmp_monat_top2_bez,tmp_monat_top2_wert,tmp_monat_top3_bez,tmp_monat_top3_wert,tmp_woche_top1_bez,tmp_woche_top1_wert,tmp_woche_top2_bez,tmp_woche_top2_wert,tmp_woche_top3_bez,tmp_woche_top3_wert,tmp_tag_top1_bez,tmp_tag_top1_wert,tmp_tag_top2_bez,tmp_tag_top2_wert,tmp_tag_top3_bez,tmp_tag_top3_wert\n"

    . "FROM tmp_buchung\n"
	. "WHERE tmp_user_nick<>'deakt'"
	. " AND tmp_team_id=".$user['sae_team_id']
	. " ORDER BY tmp_heute DESC";

	
	$result = mysqli_query($conn,$sql);
	if (!$result) {
	    printf("Error: %s\n", mysqli_error($conn));
	    exit();
	}

			echo "<table class=\"table table-striped\"> 
				<tr>
				<th>Name</th>
				<th>Heute</th>
				<th>Woche</th>
				<th>Monat</th>
				<th>Jahr</th>
				</tr>";
			
			while($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>" . $row['tmp_user_nick'] . "</td>";    
				
				echo "<td>" . $row['tmp_heute']/4 . 
					"<ol class='top3'>
							<li>" . substr($row['tmp_tag_top1_bez'],0,8)." (".$row['tmp_tag_top1_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_tag_top2_bez'],0,8)." (".$row['tmp_tag_top2_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_tag_top3_bez'],0,8)." (".$row['tmp_tag_top3_wert']/4 . ")
							</li>
						</ol>
					</td>";
				
					echo "<td>" . $row['tmp_woche']/4 . 
					"<ol style=\"font-size : 0.5em;padding-left : 0px;margin-bottom: 0px;text-align : left;\">
							<li>" . substr($row['tmp_woche_top1_bez'],0,8)." (".$row['tmp_woche_top1_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_woche_top2_bez'],0,8)." (".$row['tmp_woche_top2_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_woche_top3_bez'],0,8)." (".$row['tmp_woche_top3_wert']/4 . ")
							</li>
						</ol>
					</td>";
				
					echo "<td>" . $row['tmp_monat']/4 . 
					"<ol style=\"font-size : 0.5em;padding-left : 0px;margin-bottom: 0px;text-align : left;\">
							<li>" . substr($row['tmp_monat_top1_bez'],0,8)." (".$row['tmp_monat_top1_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_monat_top2_bez'],0,8)." (".$row['tmp_monat_top2_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_monat_top3_bez'],0,8)." (".$row['tmp_monat_top3_wert']/4 . ")
							</li>
						</ol>
					</td>";
				
				echo "<td>" . $row['tmp_jahr']/4 . 
					"<ol style=\"font-size : 0.5em;padding-left : 0px;margin-bottom: 0px;text-align : left;\">
							<li>" . substr($row['tmp_jahr_top1_bez'],0,8)." (".$row['tmp_jahr_top1_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_jahr_top2_bez'],0,8)." (".$row['tmp_jahr_top2_wert']/4 . ")
							</li>
							<li>" . substr($row['tmp_jahr_top3_bez'],0,8)." (".$row['tmp_jahr_top3_wert']/4 . ")
							</li>
						</ol>
				</td>";
				
				echo "</tr>";
			}
			echo "</table>";

mysqli_close($conn);



?>
</body>
</html>