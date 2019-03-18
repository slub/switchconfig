<?php

require_once('php/session.php');

require_once('php/functions.php');
require_once('switchlist.php');
require_once('lang.php');
if(!ENABLE_PASSWORD_CHANGE) die("Not available.");

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php translate('Change Password'); ?> - <?php translate('Switchconfig'); ?></title>
	<?php require('head.inc.php'); ?>
</head>
<body onLoad='document.passwordform.newpw.focus()'>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
	}
	function endFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
	}
	</script>
	<style>
	/* password-change-only style definitions */
	.changeok {
		font-weight: bold;
		color: green;
	}
	.changefail {
		font-weight: bold;
		color: red;
	}
	</style>

	<div id='logincontainer'>
		<h1 id='title'><a><img id='logo' src='img/logo.png' /></a></h1>

		<div id='loginsplash'>
			<div id='subtitle'>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<img id='imgSwitch' src='img/switch.png'></img>
				</div>
				<?php translate('With this form you can change your password on all switches. After the procedure is done, you have to log in again.'); ?>
				<br><br>
				<?php translate('This procedure can take some minutes, because the webserver has to establish an SSH connection to all switches. Please do not close this page until finished.'); ?>
			</div>

			<table width='100%' id='logintable'>

<?php
$info = "";
$showform = true;
if(isset($_POST['newpw']) && isset($_POST['newpw2'])) {
	if($_POST['newpw'] == $_POST['newpw2']) {
		if($_POST['newpw'] != "") {
			if(strpos($_POST['newpw'], "$") === false && strpos($_POST['newpw'], " ") === false && strpos($_POST['newpw'], "\"") === false && strpos($_POST['newpw'], "'") === false && strpos($_POST['newpw'], ">") === false && strpos($_POST['newpw'], "|") === false && strpos($_POST['newpw'], "\n") === false) {

				echo "<script>beginFadeOutAnimation();</script>";

				foreach(SWITCHES as $currentswitch) {
					echo "<span>" . $currentswitch['name'] . "...</span>&nbsp;"; flush(); ob_flush();

					$connection = ssh2_connect($currentswitch['addr'], 22);

					if($connection !== false) {
						if(ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']) !== false) {
							$stdio_stream = ssh2_shell($connection);
							if($stdio_stream !== false) {

								fwrite($stdio_stream, "conf t" . "\n" .
								                      "username " . $_POST['username'] . " password 0 " . $_POST['newpw'] . "\n" .
								                      "end" . "\n" .
								                      "wr mem" . "\n" .
								                      "exit" . "\n");
								$cmd_output .= "\n" . stream_get_contents($stdio_stream);
								echo "<span class='changeok'>OK</span><br>"; flush(); ob_flush();

							} else {
								echo "<span class='changefail'>FAIL! (cmd execution error)</span><br>"; flush(); ob_flush();
							}
						} else {
							echo "<span class='changefail'>FAIL! (auth error)</span><br>"; flush(); ob_flush();
						}
					} else {
						echo "<span class='changefail'>FAIL! (connection error)</span><br>"; flush(); ob_flush();
					}

				}
				session_destroy();
				$info .= "<tr><td><div class='infobox ok'>Kennwort wurde auf den oben genannten Switchen geändert</div></input></td></tr>";
				$info .= "<tr><td><div class='infobox info'>Sie müssen sich neu anmelden</div></input></td></tr>";
				$info .= "<tr><td><a href='login.php' class='slubbutton'>&gt;Jetzt neu anmelden</a></td></tr>";
				$showform = false;
				echo "<script>endFadeOutAnimation();</script>";
			} else {
				$info = "<tr><td><div class='infobox warn'>Folgende Sonderzeichen oder Leerzeichen sind nicht erlaubt!<br><span style='font-family: monospace;'>\" | > $</span></div></input></td></tr>";
			}
		} else {
			$info = "<tr><td><div class='infobox warn'>Das Kennwort darf nicht leer sein!</div></input></td></tr>";
		}
	} else {
		$info = "<tr><td><div class='infobox warn'>Die Passwörter stimmen nicht überein!</div></input></td></tr>";
	}
}
?>

				<?php echo $info; ?>

				<?php if($showform == true) { ?>
				<form method='POST' name='passwordform' onsubmit='beginFadeOutAnimation();'>
					<tr><td>
						<input type='text' name='username' id='username' value='<?php echo $_SESSION['username']; ?>' readonly></input>
						<span class='tip'><label for='description'><?php translate('Username'); ?></label></span>
						<br>
					</td></tr>

					<tr><td>
						<input type='password' name='newpw' id='newpw' value=''></input>
						<span class='tip'><label for='description'><?php translate('New Password'); ?></label></span>
						<br>
					</td></tr>

					<tr><td>
						<input type='password' name='newpw2' id='newpw2' value=''></input>
						<span class='tip'><label for='description'><?php translate('Confirm New Password'); ?></label></span>
						<br>
					</td></tr>

					<tr><td>
						<input type='submit' value='&gt;<?php translate('Change Password'); ?>' class='slubbutton'>
					</td></tr>
				</form>

				<tr><td>
					<a href='index.php' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Back'); ?></a>
				</td></tr>
				<?php } ?>

			</table>
		</div>

		<?php require('foot.inc.php'); ?>

	</div>

	<span style='display: none'>
		SWITCH-CONFIG OUTPUT:
		<?php echo $cmd_output . "\n" ?>
	</span>

<?php require('menu.inc.php'); ?>

</body>
</html>
