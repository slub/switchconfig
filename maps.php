<?php

require_once('php/session.php');

require_once('config.php');
require_once('php/functions.php');
require_once('lang.php');

?>

<!DOCTYPE html>
<html>
<head>
	<title>Maps - Switchconfig</title>
	<?php require('head.inc.php'); ?>
	<script language='javascript' type='text/javascript' src='js/move_on_drag.js'></script>
</head>

<body>

<?php if (isset($_GET['map'])) { ?>

	<div id='mapcontainer'>
<?php
	foreach(MAPS as $currentmap) {
		if($currentmap['name'] == $_GET['map']) {
			echo "<img src='map.php?img=".$currentmap['img']."' draggable='false' ondragstart='return false;'></img>";
			foreach($currentmap['items'] as $item) {
				// get additional details for popup
				$cSwitch = getSwitchByAddr($item['switch']);
				if($cSwitch != null) {
					// selected from index.php? If yes, highlight it.
					$blinkclass = "";
					if(isset($_GET['highlightswitch']) && $_GET['highlightswitch'] == $item['switch'])
						$blinkclass = "blink";
					// echo an anchor, a little bit higher than the main switch link
					echo "<a name='".$cSwitch['addr']."' class='anchor' style='top: " . ($item['y'] - 100) . "px; left: " . ($item['x'] + 180) . "px;'></a>";
					// echo the main switch link
					echo "<a href='index.php?switch=".$cSwitch['addr']."' class='slubbutton switchonmaplink ".$blinkclass."' style='top: " . $item['y'] . "px; left: " . $item['x'] . "px;' title='".$cSwitch['name']."\nAdresse: ".$cSwitch['addr']."'>".shortText($cSwitch['name'])."</a>";
				} else {
					error_log('Warning on building map '.$currentmap['name'].': switch '.$item['switch'].' not found in SWITCHES array.');
				}
			}
		}
	}
?>
	</div>
	<script>
		makeMoveableOnMouseClick('mapcontainer');
	</script>

<?php $ZOOM = true; /* for menu.inc.php */ ?>

<?php } else { ?>

	<div id='container'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='splash' class='login'>

			<div id='subtitle'>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<img id='imgSwitch' src='img/switch.png'></img>
				</div>
				<?php translate('Please click on a button to open the map with the switches.'); ?>
			</div>

			<div id='maplist'>
				<?php
				foreach(MAPS as $currentmap) {
					echo "<a href='maps.php?map=" . $currentmap['name'] . "' class='slubbutton secondary'>" . $currentmap['displayname'] . "</a>\n";
				}
				?>
			</div>

			<div class='spread-toolbar toolbar-margin-top'>
				<a href='index.php'>&gt;<?php translate('Back'); ?></a>
			</div>
		</div>

		<?php require('foot.inc.php'); ?>

	</div>

<?php } ?>

<?php require('menu.inc.php'); ?>

</body>
</html>
