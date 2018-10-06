<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
<style>
</style>

include("templates/header.inc.php");
?>

<h1>Demo123</h1>

<div id="myElement">lala</div>



<?php 
include("templates/footer.inc.php")
?>
