<?php

require_once('switchlist.php');
require_once('lang.php');


$info = "";
$infoClass = "";
session_start();

if(isset($_GET['logout'])) {

	// sign out
	session_unset();
	session_destroy();
	$infoClass = "ok";
	$info = translate('Successfully logged out', false);

} elseif(isset($_POST['username']) && isset($_POST['password'])) {

	// test connection, if username and password is OK
	foreach(SWITCHES as $s) {
		$connection = ssh2_connect($s['addr'], 22);
		if($connection !== false) {
			if(ssh2_auth_password($connection, $_POST['username'], $_POST['password']) !== false) {
				// auth OK
				$_SESSION['username'] = $_POST['username'];
				$_SESSION['password'] = $_POST['password'];
				$_SESSION['last_activity'] = time(); // set last activity time stamp for timeout
				header('Location: index.php'); exit(); break;
			}
			break; // only test first switch (testing all switches would take too long)
		}
	}

	// error message - login not valid
	$infoClass = "error";
	$info = translate('Login failed', false);

} elseif(isset($_GET['reason']) && $_GET['reason'] == 'unavailable') {

	$infoClass = "info";
	$info = translate('Maintenance Mode - Please try again later.', false);

} elseif(isset($_GET['reason']) && $_GET['reason'] == 'timeout') {

	$infoClass = "warn";
	$info = translate('Session timed out. Please log in again.', false);

} elseif(isset($_GET['reason']) && $_GET['reason'] == 'notloggedin') {

	$infoClass = "warn";
	$info = translate('Please log in first.', false);

} elseif(isset($_SESSION['username']) && isset($_SESSION['password'])) {

	// already signed in
	header('Location: index.php'); exit();

}

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php translate('Switchconfig'); ?> - <?php translate('Log In'); ?></title>
	<?php require('head.inc.php'); ?>
	<script language='javascript' type='text/javascript' src='js/explode.js'></script>
	<style>
	@media only screen and (max-width: 620px) {
		#logincontainer {
			margin-top: 20px;
		}
	}
	</style>
</head>
<body onLoad='username.focus();'>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('loginsplash').className = 'out';
		document.getElementById('splashloading').className = 'load';
		document.getElementById('submitLogin').disabled = 'true';
		document.getElementById('username').readOnly = 'true';
		document.getElementById('password').readOnly = 'true';
	}
	</script>

	<div id='logincontainer'>
		<img id='splashloading' src='img/loading.login.svg'></img>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='loginsplash'>
			<div id='subtitle'>
				<div id='imgContainer'>
					<img id='imgSwitch' src='img/switch.login.png' class='easteregg-trigger' onclick='boom()' title='initiate self destruction'></img>
				</div>
				<div>
					<?php translate('This web application allows you to configure Cisco switches through a graphical interface.'); ?>
				</div>
				<div class='toolbar-margin-top'>
					<b><?php translate('Switch Authentication'); ?></b><br>
					<?php translate('Please log in with your switch credentials (SSH).'); ?>
				</div>
			</div>

			<?php require_once('php/browsercheck.php'); ?>

			<?php if ($info != '') { ?>
				<div class='infobox <?php echo $infoClass; ?>'><?php echo $info; ?></div>
			<?php } ?>

			<form method='POST' action='login.php' name='loginform' onsubmit='beginFadeOutAnimation();'>
				<input type='text' id='username' name='username' class='fullwidth bottommargin' placeholder='<?php translate("Username"); ?>' />
				<input type='password' id='password' name='password' class='fullwidth bottommargin' placeholder='<?php translate("Password"); ?>' />
				<input type='submit' id='submitLogin' class='slubbutton fullwidth' value='&gt;<?php translate("Log In"); ?>' />
			</form>

		</div>

		<?php require('foot.inc.php'); ?>

	</div>

</body>
</html>
