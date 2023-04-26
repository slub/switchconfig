<?php

require_once('php/session.php');
session_write_close();

require_once('php/functions.php');
require_once('config.php');
require_once('php/switchoperations.php');
require_once('lang.php');


$cSwitch = null;
if(!empty($_GET['switch'])) {
	$cSwitch = getSwitchByAddr($_GET['switch']);
}

$port = "";
if(!empty($_GET['port']))
	$port = removeInvalidChars($_GET['port']);

$cSnippet = null;
$info = null;
$infoclass = null;
if(!empty($_POST['snippet'])) {
	$cSnippet = getSnippetById($_POST['snippet']);
}

$cmdResponse = null;
if($cSwitch != null && $cSnippet != null) {
	if(empty($cSnippet['users']) || in_array($_SESSION['username'], $cSnippet['users'])) {
		$cmd = '';
		if($cSnippet['scope'] == 'port') {
			if(empty($port)) {
				$info = translate('Port-Snippet cannot be executed because no port name was given', false).' ('.$cSnippet['name'].')';
				$infoclass = 'error';
			} else {
				$cmd = str_replace('%PORT%', $port, $cSnippet['cmd']) . "\n" . 'exit' . "\n";
			}
		} else {
			$cmd = $cSnippet['cmd'] . "\n" . 'exit' . "\n";
		}
		if(!empty($cmd)) {
			$cmdResponse = executeRawShell($cSwitch['addr'], $cmd);
			$info = translate('Snippet executed', false).' ('.$cSnippet['name'].')';
			$infoclass = 'ok';
			//$info = translate('Execution Preview:', false).' '.$cmd.'';
			//$infoclass = 'info';
		}
	} else {
		$info = translate('You are not allowed to run this snippet.', false).' ('.$cSnippet['name'].')';
		$infoclass = 'error';
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php translate('Snippets'); ?> - <?php translate('Switchconfig'); ?></title>
	<?php require('head.inc.php'); ?>
	<link rel='stylesheet' type='text/css' href='css/intstatuslist.css'>
</head>
<body>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;

		var buttons = document.getElementsByTagName("button");
		for(i = 0 ; i < buttons.length ; i++) {
			buttons[i].classList.add("disabled");
		}

		document.getElementsByTagName("body")[0].classList.add("loading");
	}
	</script>

	<div id='container'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='splash' <?php if($cmdResponse !== null) { ?>class='big'<?php } ?>>
			<?php if($cSwitch != null) { ?>
				<h2>
					<?php echo $cSwitch['name']; ?>
					<?php if(!empty($port)) echo "<br>".htmlspecialchars($port); ?>
				</h2>
			<?php } else { ?>
				<div class='infobox warn'><?php translate('No switch selected'); ?></div>
			<?php } ?>
			<hr/>
			<div id='subtitle'>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<img id='imgSwitch' src='img/switch.png'></img>
				</div>
				<?php translate('Send pre-defined commands to your switch.'); ?>
			</div>

			<table width='100%' id='logintable'>
				<tr><td>

<?php if($info != null) {?>
	<div class='infobox <?php echo htmlspecialchars($infoclass); ?>'><?php echo htmlspecialchars($info); ?></div>
<?php } ?>

<?php
	if($cSwitch != null) {

		if($cmdResponse === null) {

			// show snippet list
			foreach(SNIPPETS as $snippet) {
				if(!empty($snippet['users'])) {
					if(!in_array($_SESSION['username'], $snippet['users'])) continue;
				}
				if($snippet['scope'] == 'switch' || ($snippet['scope'] == 'port' && !empty($port))) {
					$img = '';
					if($snippet['scope'] == 'switch') $img = 'img/switch.png';
					if($snippet['scope'] == 'port') $img = 'img/notconnected.svg';
					echo "<form method='POST'>";
					echo "<input type='hidden' name='snippet' value='".htmlspecialchars($snippet['id'])."'>";
					echo "<button class='fullwidth slubbutton secondary notypo marginbottom' onclick='beginFadeOutAnimation();'><img class='smallimg' src='".$img."'>&nbsp;&nbsp;".htmlspecialchars($snippet['name'])."</button>";
					echo "</form>";
				}
			}

		} else {

			// show cmd output
			echo "<textarea id='intstatustext' wrap='off' readonly='true'>" . htmlspecialchars($cmdResponse) . "</textarea>";

		}

	}
?>

				</td></tr>

				<tr>
					<td>
						<div class='spread-toolbar toolbar-margin-top'>
							<a href='index.php?switch=<?php echo urlencode($cSwitch['addr']); ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Back'); ?></a>
							<a href='snippets.php?switch=<?php echo urlencode($cSwitch['addr']); ?>&port=<?php echo urlencode($port); ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Execute Another Snippet'); ?></a>
						</div>
					</td>
				</tr>

			</table>
		</div>

		<?php require('foot.inc.php'); ?>

	</div>

<?php require('menu.inc.php'); ?>

</body>
</html>
