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
date_default_timezone_set("Europe/Berlin");

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

// Beispiel 2
$data = "foo:*:1023:1000::/home/foo:/bin/sh";
list($wert, $user_id, $auf_id) = explode(":", $_GET['q']);

$kommentar =  $_GET['k'];
$per_erledigt = $_GET['p'];
$neue_aufgabe = $_GET['e']; 
$buc_datum = $_GET['b'];
if (validateDate($buc_datum)) {
	$buc_datum = $buc_datum . " " . date("H:i:s");
}
else {
	$buc_datum = date("Y-m-d") . " " . date("H:i:s");
}

 


$wert = intval($wert);
$auf_id = intval($auf_id);
$per_erledigt = intval($per_erledigt);
$neue_aufgabe = intval($neue_aufgabe); 


$txt = sprintf("GET: (%s)(%s)(%s)(%s) (%s)\n", $_GET['q'],$_GET['k'],$_GET['p'],$_GET['e'],$buc_datum);
error_log($txt, 0);

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($per_erledigt==1) {
  $txt = sprintf("Setze Aufgabe mit id (%u) auf erledigt \n", $auf_id);
  error_log($txt, 0);  
  /* Aufgabe soll erledigt werden */
  
  /* Setze wret auf 0, damot kein DS geschrieben wird */
  $wert=0;
  
  /* Setze auf_bbendet_am - Datum */  
  if ($stmt = $conn->prepare("UPDATE sae_aufgabe SET auf_beendet_am = NOW() WHERE auf_id =?")) {

		/* bind parameters for markers */
		$stmt->bind_param("i", $auf_id);

		/* execute query */
		$stmt->execute();
    
    /* close statement */
    $stmt->close();	
	}
  else {
    error_log("Kann Datensatz nicht aktualisiseren.");
  }
	
  
  
}

error_log("neue_aufgabe: " . $neue_aufgabe);
if ($neue_aufgabe==1) {
	// Neue Aufgabe soll erstellt werden
	error_log("Neue Aufgabe",0);
	// Tätigkeits-ID ermitteln
  /* create a prepared statement */
	if ($stmt = $conn->prepare("SELECT sae_tae_fk FROM sae_aufgabe WHERE auf_id=?")) {

		/* bind parameters for markers */
		$stmt->bind_param("i", $auf_id);

		/* execute query */
		$stmt->execute();

		/* bind result variables */
		$stmt->bind_result($sae_tae_fk);

		/* fetch value */
		$stmt->fetch();
    
    /* close statement */
    $stmt->close();

		$txt = sprintf("auf_id: (%s) gehört zu Tätigkeit: (%s)\n", $auf_id, $sae_tae_fk);
		error_log($txt, 0);
	}
		
	/* bind darf keine Referenze übergeben werden, deswegen Variablen */
  $auf_daueraufgabe = 1000;
  $user_teamid =  intval($user['sae_team_id']);
  $sae_tae_fk = intval($sae_tae_fk);
  $komm_kurz = substr($kommentar,0,5);
  $userid = intval($user['id']);
  
	
  // prepare and bind
  $fk =  intval($sae_tae_fk);
  $stmt = $conn->prepare("INSERT INTO sae_aufgabe(auf_kurz, auf_beschreibung, auf_daueraufgabe, sae_tae_fk, sae_team_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssiii", $komm_kurz, $kommentar, $auf_daueraufgabe, $sae_tae_fk, $user_teamid );
  $stmt->execute();
  $stmt->close();
  
  
  $last_id = $conn->insert_id;
  $txt = sprintf("Neue Aufgabe auf_id: (%s) zur Tätigkeit hinzugefügt: (%s)\n", $last_id, $sae_tae_fk);
	error_log($txt, 0);
  
  
  /* User die neue AUfgabe zuordnen */ 
  $stmt = $conn->prepare("INSERT INTO `users_has_sae_aufgabe` (`users_id`, `sae_aufgabe_auf_id`) VALUES (?, ?)");
  $stmt->bind_param("ii", $userid, $last_id );
  $stmt->execute();
  $stmt->close();
  
  
  
  

} /* Ende neue Aufgabe */


if ($wert!=0) {
	// DS soll geschrieben werden, nur anzeige
  $stmt = $conn->prepare("INSERT INTO sae_buchung (buc_wert, users_id, sae_aufgabe_auf_id, buc_kommentar, sae_team_id, buc_created_at)
		VALUES (?, ?, ?, ?, ?, ?)");

	$stmt->bind_param("iiisis", $wert, $user_id, $auf_id, $kommentar, $user['sae_team_id'],$buc_datum);

  
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
      ?>
      <div class="list-group">

	<div id="auf_liste">
	 <?php 
			$statement = $pdo->prepare("SELECT  sae_aufgabe.auf_daueraufgabe, sae_aufgabe.auf_beschreibung, auf_id FROM sae.sae_aufgabe, users, users_has_sae_aufgabe WHERE auf_beendet_am IS NULL AND id=users_id AND auf_id=sae_aufgabe_auf_id AND id=".$user['id']." AND sae_aufgabe.sae_team_id=".$user['sae_team_id']." ORDER BY `auf_daueraufgabe` DESC");
			$result = $statement->execute();
			$count = 1;
			while($row = $statement->fetch()) {
				echo "<a href=\"#\" data-toggle=\"tooltip\" title=\"Klicken zur Aufwandserfassung\" type='button' onclick='addBuchung(\"1:" . $user['id'] . ":" . $row['auf_id'] . "\"," . $row['auf_id'] . "," . $row['auf_daueraufgabe'] . ")' class='list-group-item list-group-item-action ";
        
        if ($row['auf_daueraufgabe']==1000) 
        {
			echo " list-group-item-info'>".$row['auf_beschreibung'];
             echo "<input id=\"cb" . $row['auf_id'] . "\" class=\"pull-right\" type=\"checkbox\">";          
        } 
		else {
			echo "'>".$row['auf_beschreibung'];
		}
		
        
        echo "</a>";
			}

?>
	</div>
<?php 
mysqli_close($conn);

?>
</body>
</html>
