<?php

/*** MAINTENANCE MESSAGE ***/
const MAINT = false;
const MAINT_IP = "194.95.144.21";

if(MAINT == true && $_SERVER['REMOTE_ADDR'] != MAINT_IP) {
	header('Location: login.php?reason=unavailable');
	exit();
}


/*** START SESSION ***/
require_once(__DIR__.'/session-options.php');

/*** AUTH CHECK ***/
/* CHECK IF CLIENT IS AUTHENTICATED */
if(!(isset($_SESSION['username']) && isset($_SESSION['password']))) {
	/* IF NOT: REDIRECT TO LOGIN PAGE AND DO NOT EXECUTE CURRENT SCRIPT */
	if($SUPRESS_NOTLOGGEDIN_MESSAGE == true) {
		header('Location: login.php');
	} else {
		header('Location: login.php?reason=notloggedin');
	}
	exit();
}

/*** TIMEOUT HANDLER ***/
/* TIMEOUT VALUES */
$TIMEOUT_MIN = 15; // mit Absicht ziemlich restriktiv, falls man den Switch mal 'von unterwegs' konfiguriert

/* CHECK IF SESSION IS TIMED OUT */
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > ($TIMEOUT_MIN * 60))) {
	/* SESSION TIMED OUT! */
	session_unset();     // unset $_SESSION variable for the run-time
	session_destroy();   // destroy session data in storage

	/* REDIRECT TO LOGIN PAGE */
	header('Location: login.php?reason=timeout'); exit();
} else {
	// update last activity time stamp
	$_SESSION['last_activity'] = time();
}
