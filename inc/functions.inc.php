<?php
/**
 * A complete login script with registration and members area.
 *
 * @author: Nils Reimers / http://www.php-einfach.de/experte/php-codebeispiele/loginscript/
 * @license: GNU GPLv3
 */
include_once("password.inc.php");

/**
 * Refreshed die tmp Tabelle
 * T. Freimann
 */
function refresh_tmp() {
	global $pdo;
	$user = check_user();
	
	$sql = "TRUNCATE tmp_buchung";
	$trunc = $pdo->prepare($sql);		
	$result = $trunc->execute();

	
	$sql = "INSERT INTO tmp_buchung (tmp_user_id, tmp_user_nick,tmp_jahr,tmp_team_id)\n"
		. "select users.id, users.nick, SUM(sae_buchung.buc_wert) as summe,".intval($user['sae_team_id'])." \n"
		. "from users,sae_buchung\n"
		. "where users.id=sae_buchung.users_id AND YEAR(buc_created_at)=YEAR(Now())\n"
		. "AND sae_buchung.sae_team_id=".intval($user['sae_team_id'])."\n"
		." GROUP BY users.id";

	$ins = $pdo->prepare($sql);		
	$result = $ins->execute();
		


	$sql = "UPDATE tmp_buchung SET tmp_monat = ( select  SUM(sae_buchung.buc_wert) from sae_buchung where tmp_user_id=sae_buchung.users_id AND YEAR(sae_buchung.buc_created_at)=YEAR(Now()) AND MONTH(sae_buchung.buc_created_at)=MONTH(Now()) GROUP BY sae_buchung.users_id)";
	
	$upd = $pdo->prepare($sql);		
	$result = $upd->execute();
	

	$sql = "UPDATE tmp_buchung SET tmp_woche = ( select  SUM(sae_buchung.buc_wert) from sae_buchung where tmp_user_id=sae_buchung.users_id AND YEAR(sae_buchung.buc_created_at)=YEAR(Now()) AND MONTH(sae_buchung.buc_created_at)=MONTH(Now()) AND WEEK(sae_buchung.buc_created_at)=WEEK(Now()) GROUP BY sae_buchung.users_id)";
	
	$upd = $pdo->prepare($sql);		
	$result = $upd->execute();
	


	$sql = "UPDATE tmp_buchung SET tmp_heute = ( select  SUM(sae_buchung.buc_wert) from sae_buchung where tmp_user_id=sae_buchung.users_id AND YEAR(sae_buchung.buc_created_at)=YEAR(Now()) AND MONTH(sae_buchung.buc_created_at)=MONTH(Now()) AND WEEK(sae_buchung.buc_created_at)=WEEK(Now()) AND DAY(sae_buchung.buc_created_at)=DAY(Now()) GROUP BY sae_buchung.users_id)";
	
	$upd = $pdo->prepare($sql);		
	$result = $upd->execute();
	
	return true;


}

/**
 * Checks that the user is logged in. 
 * @return Returns the row of the logged in user
 */
function check_user() {
	global $pdo;
	
	if(!isset($_SESSION['userid']) && isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken'])) {
		$identifier = $_COOKIE['identifier'];
		$securitytoken = $_COOKIE['securitytoken'];
		
		$statement = $pdo->prepare("SELECT * FROM securitytokens WHERE identifier = ?");
		$result = $statement->execute(array($identifier));
		$securitytoken_row = $statement->fetch();
	
		if(sha1($securitytoken) !== $securitytoken_row['securitytoken']) {
			//Vermutlich wurde der Security Token gestohlen
			//Hier ggf. eine Warnung o.ä. anzeigen
			
		} else { //Token war korrekt
			//Setze neuen Token
			$neuer_securitytoken = random_string();
			$insert = $pdo->prepare("UPDATE securitytokens SET securitytoken = :securitytoken WHERE identifier = :identifier");
			$insert->execute(array('securitytoken' => sha1($neuer_securitytoken), 'identifier' => $identifier));
			setcookie("identifier",$identifier,time()+(3600*24*365)); //1 Jahr Gültigkeit
			setcookie("securitytoken",$neuer_securitytoken,time()+(3600*24*365)); //1 Jahr Gültigkeit
	
			//Logge den Benutzer ein
			$_SESSION['userid'] = $securitytoken_row['user_id'];
		}
	}
	
	
	if(!isset($_SESSION['userid'])) {
		// header("location: login.php");
		die('Bitte zuerst <a href="index.php">einloggen</a>');
	}
	

	$statement = $pdo->prepare("SELECT * FROM users WHERE id = :id");
	$result = $statement->execute(array('id' => $_SESSION['userid']));
	$user = $statement->fetch();
	return $user;
}

/**
 * Returns true when the user is checked in, else false
 */
function is_checked_in() {
	return isset($_SESSION['userid']);
}
 
/**
 * Returns a random string
 */
function random_string() {
	if(function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes(16);
		$str = bin2hex($bytes); 
	} else if(function_exists('mcrypt_create_iv')) {
		$bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
		$str = bin2hex($bytes); 
	} else {
		//Replace your_secret_string with a string of your choice (>12 characters)
		$str = md5(uniqid('your_secret_string', true));
	}	
	return $str;
}

/**
 * Returns the URL to the site without the script name
 */
function getSiteURL() {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
}

/**
 * Outputs an error message and stops the further exectution of the script.
 */
function error($error_msg) {
	include("templates/header.inc.php");
	include("templates/error.inc.php");
	include("templates/footer.inc.php");
	exit();
}
 
 
