<?php

require_once('config.php');
require_once('lang.php');
require_once('php/session-options.php');


$info = '';
$infoClass = '';

if(isset($_SESSION['username']) && isset($_GET['logout'])) {

	// sign out
	session_unset();
	session_destroy();
	$infoClass = 'ok';
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

				// redirect to desired page
				$redirect = 'index.php';
				if(!empty($_GET['redirect'])
				&& substr($_GET['redirect'], 0, 1) === '/') { // only allow relative URLs beginning with '/', do not redirect to other websites!
					$redirect = $_GET['redirect'];
				}
				header('Location: '.$redirect);
				die('Happy configuring!');
			}
			break; // only test first switch (testing all switches would take too long)
		}
	}

	// error message - login not valid
	$infoClass = 'error';
	$info = translate('Login failed', false);

} elseif(isset($_GET['reason']) && $_GET['reason'] == 'unavailable') {

	$infoClass = 'info';
	$info = translate('Maintenance Mode - Please try again later.', false);

} elseif(isset($_GET['reason']) && $_GET['reason'] == 'timeout') {

	$infoClass = 'warn';
	$info = translate('Session timed out. Please log in again.', false);

} elseif(isset($_GET['reason']) && $_GET['reason'] == 'notloggedin') {

	$infoClass = 'warn';
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
	<script type='text/javascript' src='webdesign-template/js/main.js'></script>
	<script type='text/javascript' src='js/explode.js'></script>
	<style>
	#forkme {
		position: absolute;
		top: 0; right: 0;
	}
	@media only screen and (max-width: 620px) {
		#logincontainer {
			margin-top: 20px;
		}
	}
	</style>
</head>
<body>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
		document.getElementById('submitLogin').disabled = true;
		document.getElementById('username').readOnly = true;
		document.getElementById('password').readOnly = true;
	}
	</script>

	<a id='forkme' href='https://github.com/slub/switchconfig'><img src='img/forkme.png'></a>

	<div id='container'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='splash' class='login'>
			<div id='subtitle'>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<img id='imgSwitch' src='img/switch.login.png' class='easteregg-trigger' onclick='boom()' title='initiate self destruction'></img>
				</div>
				<p class='first'>
					<?php translate('This web application allows you to configure Cisco switches through a graphical interface.'); ?>
				</p>
				<p class='toolbar-margin-top'>
					<b><?php translate('Switch Authentication'); ?></b><br>
					<?php translate('Please log in with your switch credentials (SSH).'); ?>
				</p>
			</div>

			<?php require_once('php/browsercheck.php'); ?>

			<?php if($info != '') { ?>
				<div class='infobox <?php echo $infoClass; ?>'><?php echo $info; ?></div>
			<?php } ?>

			<form method='POST' name='loginform' id='frmLogin' onsubmit='beginFadeOutAnimation();'>
				<div class='form-row icon'>
					<input type='text' id='username' name='username' placeholder='<?php translate('Username'); ?>' autofocus='true' />
					<img src='webdesign-template/img/person.svg'>
				</div>
				<div class='form-row password icon'>
					<input type='password' id='password' name='password' placeholder='<?php translate('Password'); ?>' />
					<img src='webdesign-template/img/key.svg'>
					<img src='webdesign-template/img/eye.svg' class='right showpassword' title='Kennwort anzeigen'>
				</div>
				<div class='form-row'>
					<button id='submitLogin' class='slubbutton'><?php translate('Log In'); ?></button>
				</div>
			</form>

		</div>

		<?php require('foot.inc.php'); ?>

	</div>

</body>
</html>
