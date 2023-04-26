<?php

require_once('php/session.php');

require_once('php/functions.php');
require_once('config.php');
require_once('lang.php');
if(!ENABLE_PASSWORD_CHANGE) die('This feature is disabled.');

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php translate('Change Password'); ?> - <?php translate('Switchconfig'); ?></title>
	<?php require('head.inc.php'); ?>
</head>
<body>
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

	<div id='container'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='splash' class='login'>
			<div id='imgContainer'>
				<img id='imgLoading' src='img/loading.svg'></img>
				<img id='imgSwitch' src='img/switch.png'></img>
			</div>
			<div id='subtitle'>
				<p>
					<?php translate('With this form you can change your password on all switches. After the procedure is done, you have to log in again.'); ?>
				</p>
				<p>
					<?php translate('This procedure can take some minutes, because the webserver has to establish an SSH connection to all switches. Please do not close this page until finished.'); ?>
				</p>
			</div>

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
				$info .= "<div class='infobox ok'>".translate("Password changed on the displayed switches",false)."</div>";
				$info .= "<div class='infobox info'>".translate("You need to login again",false)."</div>";
				$info .= "<a href='login.php' class='slubbutton'>".translate("Re-Login Now",false)."</a>";
				$showform = false;
				echo "<script>endFadeOutAnimation();</script>";
			} else {
				$info = "<div class='infobox warn'>".translate("The following special chars are not allowed!",false)."<br><span style='font-family: monospace;'>\" | > $</span></div>";
			}
		} else {
			$info = "<div class='infobox warn'>".translate("The password cannot be empty",false)."</div>";
		}
	} else {
		$info = "<div class='infobox warn'>".translate("The passwords do not match!",false)."</div>";
	}
}
?>

				<?php echo $info; ?>

				<?php if($showform == true) { ?>
					<form method='POST' name='passwordform' onsubmit='beginFadeOutAnimation();'>
						<div class='form-row'>
							<input type='text' name='username' id='username' value='<?php echo $_SESSION['username']; ?>' readonly></input>
							<span class='tip'><label for='description'><?php translate('Username'); ?></label></span>
							<br>
						</div>
						<div class='form-row'>
							<input type='password' name='newpw' id='newpw' autofocus='true'></input>
							<span class='tip'><label for='description'><?php translate('New Password'); ?></label></span>
							<br>
						</div>
						<div class='form-row'>
							<input type='password' name='newpw2' id='newpw2' autofocus='true'></input>
							<span class='tip'><label for='description'><?php translate('Confirm New Password'); ?></label></span>
							<br>
						</div>
						<div class='form-row'>
							<button class='slubbutton'><?php translate('Change Password'); ?></button>
						</div>
					</form>

					<a href='index.php' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Back'); ?></a>
				<?php } ?>

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
