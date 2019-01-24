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
// Fake - Buchung um Tabelle zu erzeugen
// addBuchung(\"1:".$user['id'].":".$row['auf_id']."\")
    window.onload = function () {
        addBuchung('0:0:0',0,0);
		var d = new Date();
		
		var n = d.toISOString().split('T')[0];
	
		
		$('#buc_datum').val(n);
		
    }
</script>



<script>
function addBuchung(str,aufid,daueraufgabe) {
	var kommentar = document.querySelector("#kommentar").value;
	var buc_datum = document.querySelector("#buc_datum").value;
  var pers_erledigt = 0;
  var pers_erstellen = 0;
  console.log("Buchungsdatum : " + buc_datum);
	
  /**
   * $wert, $user_id, $auf_id, $per_erledigt, $neue_aufgabe,$kommentar
   * Protokoll
   * a:b:c:d:e
   * a: 1 => persönliche Aufgabe soll erledigt werden
   * b: 1 => neue persönliche Aufgabe soll erstellt werden
   **/
  
  console.log(str + ":" + aufid + ":" + daueraufgabe);
  var checkBox = document.getElementById("myCheck");       
      
  if (aufid==0) {
    console.log("keine echte Buchung");    
    }
     else if (daueraufgabe==1000) {
        console.log("echte Buchung");        
        var id = "cb" + aufid;
        var cb = document.getElementById(id); 
        if (cb.checked == true){
           console.log("Persönliche Aufgabe soll erledigt werden");         
          pers_erledigt = 1;
        } 
        else {
          pers_erledigt = 0;
        }
    }
      
  
	if (checkBox.checked == true){
        console.log("Neue persönliche Aufgabe erstellen");        
        pers_erstellen = 1;
    } 
	else {
		pers_erstellen = 0;
	}
		 console.log("str: (" + str + ")");
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
				myShowTop3();		
        document.getElementById("myCheck").checked = false;
            }
        };	
        xmlhttp.open("GET","sae.php?q="+str+"&k="+kommentar+"&p="+pers_erledigt+"&e="+pers_erstellen+"&b="+buc_datum,true);
        xmlhttp.send();
    }
}
</script>



<div class="container main-container">

<p>Herzlich Willkommen <b><?php echo htmlentities($user['vorname']); ?></b>!</p>



<?php
/* ToDo: hier die tmp tabelle füllen
passiert altuell in sae. eventuell als funktion realisieren. dsnn wäre auch ein refresh möhlich
*/

error_log("Die Oracle-Datenbank ist nicht erreichbar!", 0);
$res = refresh_tmp();



?>

<div class="checkbox">
    <label><input onchange="myShowTop3()" id="showTop3" type="checkbox"> Top3 anzeigen</label>
</div>

	<div id="txtHint">
		Hier wird die Übersicht geladen ...
	</div> 
	




	<br>
	<label for="kommentar">Kommentar:</label>
	<input type="text" name="kommentar" id="kommentar" placeholder="Kommentar zur Buchung">
	<label><input type="checkbox" id="myCheck" value="" ckecked="false"> als Aufgabe</label>
	<br>
	<label for="buc_datum">Buchungsdatum:</label>	
	<input id="buc_datum" type="date" name="buc_datum">
	
</div>



<?php 
include("templates/footer.inc.php")
?>


<script>
function myShowTop3() {
	
    var x = document.getElementById("showTop3").checked;
		
	if (x) {
		// Show Top3
		$("ol").show();
		
	}
	else {
		// Dont show Top 3
		$("ol").hide();
	}
}
</script>

<script>
	var el = document.getElementById("tmp_heute");
			if (el) {
				el.addEventListener("onchange", myFunction);
			}
	// document.getElementById("tmp_heute").addEventListener("onchange", myFunction);

function myFunction() {
    var x = document.getElementById("tmp_heute");
	document.getElementById("tmp_heute").style.color = "#ff0000"; // forecolor
	document.getElementById("tmp_heute").style.backgroundColor = "#ff0000"; // backcolor
    x.value = x.value.toUpperCase();
}
</script>
