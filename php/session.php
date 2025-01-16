<?php

/*** MAINTENANCE MESSAGE ***/
const MAINT    = false;
const MAINT_IP = '194.95.144.21';

if(MAINT == true && $_SERVER['REMOTE_ADDR'] != MAINT_IP) {
	header('Location: login.php?reason=unavailable');
	exit();
}


/*** START SESSION ***/
require_once(__DIR__.'/session-options.php');


/*** AUTH CHECK ***/
if(!(isset($_SESSION['username']) && isset($_SESSION['password']))) {
	// redirect to login page
	if(empty($SUPRESS_NOTLOGGEDIN_MESSAGE)) {
		redirectToLogin('notloggedin');
	} else {
		redirectToLogin();
	}
	exit();
}


/*** TIMEOUT HANDLER ***/
$TIMEOUT_MIN = 15;

// check if session is timed out
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > ($TIMEOUT_MIN * 60))) {
	session_unset();     // unset $_SESSION variable for the run-time
	session_destroy();   // destroy session data in storage

	// redirect to login page
	redirectToLogin('timeout');
} else {
	// update last activity time stamp
	$_SESSION['last_activity'] = time();
}


function redirectToLogin($reason=null) {
	$params = [];
	if($reason) {
		$params['reason'] = $reason;
	}
	if(!empty($_SERVER['REQUEST_URI']) && substr($_SERVER['REQUEST_URI'], 0, 1) === '/') {
		$params['redirect'] = $_SERVER['REQUEST_URI'];
	}
	header('HTTP/1.1 401 Not Authorized');
	header('Location: login.php'.(empty($params) ? '' : '?').http_build_query($params));
	die();
}
