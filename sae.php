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
$conn = mysqli_connect($db_host,$db_user,$db_password,$db_name);
if (!$conn) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql = "TRUNCATE tmp_buchung";
if (!mysqli_query($conn, $sql)) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}


$stmt = $conn->prepare("INSERT INTO sae_buchung (buc_wert, users_id, sae_aufgabe_auf_id, buc_kommentar, sae_team_id)
VALUES (?, ?, ?, ?, ?)");

$stmt->bind_param("iiisi", $wert, $user_id, $auf_id, $kommentar, $user['sae_team_id']);

$stmt->execute();
$stmt->close();

$sql = "INSERT INTO tmp_buchung (tmp_user_id, tmp_user_nick,tmp_jahr)\n"
    . "select users.id, users.nick, SUM(sae_buchung.buc_wert) as summe \n"
    . "from users,sae_buchung\n"
    . "where users.id=sae_buchung.users_id AND YEAR(buc_created_at)=YEAR(Now())\n"
	. "AND sae_buchung.sae_team_id=".intval($user['sae_team_id'])."\n"
	." GROUP BY users.id";
	

if (!mysqli_query($conn, $sql)) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

$sql = "UPDATE tmp_buchung SET tmp_monat = ( select  SUM(sae_buchung.buc_wert) from sae_buchung where tmp_user_id=sae_buchung.users_id AND YEAR(sae_buchung.buc_created_at)=YEAR(Now()) AND MONTH(sae_buchung.buc_created_at)=MONTH(Now()) GROUP BY sae_buchung.users_id)";
	

if (!mysqli_query($conn, $sql)) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}


$sql = "UPDATE tmp_buchung SET tmp_woche = ( select  SUM(sae_buchung.buc_wert) from sae_buchung where tmp_user_id=sae_buchung.users_id AND YEAR(sae_buchung.buc_created_at)=YEAR(Now()) AND MONTH(sae_buchung.buc_created_at)=MONTH(Now()) AND WEEK(sae_buchung.buc_created_at)=WEEK(Now()) GROUP BY sae_buchung.users_id)";
	

if (!mysqli_query($conn, $sql)) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}


$sql = "UPDATE tmp_buchung SET tmp_heute = ( select  SUM(sae_buchung.buc_wert) from sae_buchung where tmp_user_id=sae_buchung.users_id AND YEAR(sae_buchung.buc_created_at)=YEAR(Now()) AND MONTH(sae_buchung.buc_created_at)=MONTH(Now()) AND WEEK(sae_buchung.buc_created_at)=WEEK(Now()) AND DAY(sae_buchung.buc_created_at)=DAY(Now()) GROUP BY sae_buchung.users_id)";
	

if (!mysqli_query($conn, $sql)) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}



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
				echo "<td>" . $row['tmp_monat']/4 . "</td>";
				echo "<td>" . $row['tmp_jahr']/4 . "</td>";
				echo "</tr>";
			}
			echo "</table>";

mysqli_close($conn);



?>
</body>
</html>