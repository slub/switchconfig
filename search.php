<?php

require_once('php/session.php');

require_once('switchlist.php');
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
<body onLoad='self.focus()'>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
		if(document.getElementById('submit_search') != null)
			document.getElementById('submit_search').disabled = true;
	}
	</script>

	<div id='logincontainer'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='loginsplash'>
			<div id='subtitle'>
				<?php if($cSwitch != null) { ?>
					<h2><?php echo $cSwitch['name']; ?></h2>
				<?php } else { ?>
					<div class='infobox warn'><?php translate('No switch selected'); ?></div>
				<?php } ?>
				<hr/>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<img id='imgSwitch' src='img/switch.png'></img>
				</div>
				<?php translate('Please enter a MAC address in format <b>xxxx.xxxx.xxxx</b> to perform a search.'); ?>
			</div>

				<?php
				if($cSwitch != null && isset($_GET['port']) && $_GET['port'] != "") {
					$connection = ssh2_connect($cSwitch['addr'], 22);
					ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']);
					$stream = ssh2_exec($connection, 'sh mac address-table interface ' . removeInvalidChars($_GET['port']));
					stream_set_blocking($stream, true);
					$result = str_chop_lines(@stream_get_contents($stream));
					fclose($stream);
				?>
				<?php translate('MAC address table for Interface'); ?>&nbsp;<b>&gt;<?php echo removeInvalidChars($_GET['port']); ?>&lt;</b>
				<textarea style='height:100px;' class='fullwidth' readonly='true'><?php echo $result; ?></textarea>
				<hr/>
				<?php } ?>


				<?php
				if($cSwitch != null && isset($_GET['q']) && $_GET['q'] != "") {
					$connection = ssh2_connect($cSwitch['addr'], 22);
					ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']);
					$stream = ssh2_exec($connection, 'sh mac address-table address ' . removeInvalidChars($_GET['q']));
					stream_set_blocking($stream, true);
					$result = str_chop_lines(@stream_get_contents($stream));
					fclose($stream);
					$newsearchlink = true;
				?>
				<textarea style='height:200px;' class='fullwidth' readonly='true'><?php echo $result; ?></textarea>

				<?php } elseif($cSwitch != null) { ?>

					<form method='GET' onsubmit='beginFadeOutAnimation();' name='searchform'>
						<input type='hidden' name='switch' value='<?php echo $cSwitch['addr']; ?>' />
						<div class='bottommargin'><?php translate('Search for MAC address on this switch'); ?></div>
						<input type='text' placeholder='caff.eeca.ffee' name='q' class='fullwidth bottommargin' />
						<input id='submit_search' type='submit' value='&gt;<?php translate('Start search'); ?>' class='slubbutton fullwidth bottommargin' />
					</form>

				<?php } ?>

				<div class='spread-toolbar'>
					<a href='index.php?switch=<?php echo $cSwitch['addr']; ?><?php if(isset($_GET['port'])) echo "&port=" . $_GET['port']; ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Back'); ?></a>
					<?php if($newsearchlink) { ?>
						<a href='search.php?switch=<?php echo $cSwitch['addr']; ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('New Search'); ?></a>
					<?php } ?>
				</div>

		</div>

		<?php require('foot.inc.php'); ?>

	</div>

<?php require('menu.inc.php'); ?>

</body>
</html>
