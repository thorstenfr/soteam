<?php
header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_GET["x"], false);


$sql = "select users.nick as nick, sae_aufgabe.auf_beschreibung, sae_buchung.buc_kommentar, sae_buchung.buc_wert\n"

    . "from sae_buchung, users, sae_aufgabe\n"

    . "where sae_buchung.users_id=users.id\n"

    . "and sae_buchung.sae_aufgabe_auf_id=sae_aufgabe.auf_id";


$conn = new mysqli("localhost", "root", "", "sae");
$stmt = $conn->prepare($sql);
// $stmt->bind_param("ss", $obj->table, $obj->limit);
$stmt->execute();
$result = $stmt->get_result();
$outp = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($outp);
?>