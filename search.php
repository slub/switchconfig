<?php

require_once('php/session.php');

require_once('config.php');
require_once('php/functions.php');
require_once('lang.php');


$newsearchlink = false;
$cSwitch = null;
if(isset($_GET['switch']) && $_GET['switch'] != "") {
	$cSwitch = getSwitchByAddr($_GET['switch']);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php translate('MAC Search'); ?> - <?php translate('Switchconfig'); ?></title>
	<?php require('head.inc.php'); ?>
</head>
<body>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
		if(document.getElementById('submit_search') != null)
			document.getElementById('submit_search').disabled = true;
	}
	</script>

	<div id='container'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='splash' class='login'>
			<?php if($cSwitch != null) { ?>
				<h2><?php echo $cSwitch['name']; ?></h2>
			<?php } else { ?>
				<div class='infobox warn'><?php translate('No switch selected'); ?></div>
			<?php } ?>
			<hr/>
			<div id='subtitle'>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<img id='imgSwitch' src='img/switch.png'></img>
				</div>
				<?php translate('Please enter a MAC address to perform a search on this switch.'); ?>
			</div>

				<?php
				if($cSwitch != null && !empty($_GET['port'])) {
					$connection = ssh2_connect($cSwitch['addr'], 22);
					ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']);
					$stream = ssh2_exec($connection, 'sh mac address-table interface ' . removeInvalidChars($_GET['port']));
					stream_set_blocking($stream, true);
					$result = str_chop_lines(@stream_get_contents($stream));
					fclose($stream);
					?>
					<p><?php translate('MAC address table for Interface'); ?>&nbsp;<b>&gt;<?php echo htmlspecialchars($_GET['port']); ?>&lt;</b></p>
					<textarea style='height:100px;' class='fullwidth' readonly='true'><?php echo $result; ?></textarea>
					<hr/>
				<?php } ?>


				<?php
				if($cSwitch != null && !empty($_GET['q'])) {
					$newsearchlink = true;
					$macFormatted = formatMac($_GET['q']);
					if($macFormatted) {
						$connection = ssh2_connect($cSwitch['addr'], 22);
						ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']);
						$stream = ssh2_exec($connection, 'sh mac address-table address ' . removeInvalidChars($macFormatted));
						stream_set_blocking($stream, true);
						$result = str_chop_lines(@stream_get_contents($stream));
						fclose($stream);
						?>
						<textarea style='height:200px;' class='fullwidth' readonly='true'><?php echo $result; ?></textarea>
					<?php } else { ?>
						<div class='infobox error'><?php translate('Invalid MAC address'); ?></div>
					<?php } ?>

				<?php } elseif($cSwitch != null) { ?>

					<form method='GET' onsubmit='beginFadeOutAnimation();' name='searchform'>
						<input type='hidden' name='switch' value='<?php echo $cSwitch['addr']; ?>' />
						<div class='form-row'>
							<input type='text' placeholder='ca:ff:ee:ca:ff:ee / caff.eeca.ffee / caffeecaffee' name='q' class='fullwidth' autofocus='true' />
						</div>
						<div class='form-row'>
							<input id='submit_search' type='submit' value='&gt;<?php translate('Start search'); ?>' class='slubbutton fullwidth' />
						</div>
					</form>

				<?php } ?>

				<div class='spread-toolbar'>
					<a href='index.php?switch=<?php echo urlencode($cSwitch['addr']); ?><?php if(isset($_GET['port'])) echo "&port=".urlencode($_GET['port']); ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Back'); ?></a>
					<?php if($newsearchlink) { ?>
						<a href='search.php?switch=<?php echo urlencode($cSwitch['addr']); ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('New Search'); ?></a>
					<?php } ?>
				</div>

		</div>

		<?php require('foot.inc.php'); ?>

	</div>

<?php require('menu.inc.php'); ?>

</body>
</html>
