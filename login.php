<?php 
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

include("templates/header.inc.php");


$error_msg = "";
if(isset($_POST['email']) && isset($_POST['passwort'])) {
	$email = $_POST['email'];
	$passwort = $_POST['passwort'];

	$statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
	$result = $statement->execute(array('email' => $email));
	$user = $statement->fetch();

	//Überprüfung des Passworts
	if ($user !== false && password_verify($passwort, $user['passwort'])) {
		$_SESSION['userid'] = $user['id'];

		//Möchte der Nutzer angemeldet beleiben?
		if(isset($_POST['angemeldet_bleiben'])) {
			$identifier = random_string();
			$securitytoken = random_string();
				
			$insert = $pdo->prepare("INSERT INTO securitytokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken)");
			$insert->execute(array('user_id' => $user['id'], 'identifier' => $identifier, 'securitytoken' => sha1($securitytoken)));
			setcookie("identifier",$identifier,time()+(3600*24*365)); //Valid for 1 year
			setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //Valid for 1 year
		}

		header("location: soteam.php");
		exit;
	} else {
		$error_msg =  "E-Mail oder Passwort war ungültig<br><br>";
	}

}

$email_value = "";
if(isset($_POST['email']))
	$email_value = htmlentities($_POST['email']); 


?>
<div class="jumbotron">
      <div class="container">
        <h1>soteam <small style="font-size : 0.4em">by Thorsten Freimann</small></h1>
        <p>Herzlich Willkommen bei soteam, der super simplen Aufwandserfassung für <span style="font-weight : bold;">S</span>elbst<span style="font-weight : bold;">O</span>rganisierte <span style="font-weight : bold;">Teams</span>.
		</p>
		<p><span style="font-weight : bold;">soteam</span> bietet ein super simple Art, Aufwand zu erfassen und einen schnellen Überblick darüber zu bekommen, wer wann was gemacht hat.</p>
		<p>So könnt ihr super einfach eure Aufgaben super gerecht auf die einzelnen Teammitglieder verteilen!
		</p>
		
		
        
       
        
        </p>
       
      </div>
    </div>

 
 

<?php 
// include("templates/footer.inc.php")
?>