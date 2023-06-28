<?php

require_once('php/session.php');
session_write_close();

require_once('php/functions.php');
require_once('config.php');
require_once('php/switchoperations.php');
require_once('lang.php');


$cSwitch = null;
if(isset($_GET['switch']) && $_GET['switch'] != "") {
	$cSwitch = getSwitchByAddr($_GET['switch']);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php translate('Interface Status List'); ?> - <?php translate('Switchconfig'); ?></title>
	<?php require('head.inc.php'); ?>
	<link rel='stylesheet' type='text/css' href='css/intstatuslist.css'>
</head>
<body>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
	}
	</script>

	<div id='container'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='splash' class='big'>
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
				<?php translate('This page provides a table overview for all ports with their settings.'); ?>
			</div>

			<table width='100%' id='logintable'>
				<tr><td>

<?php
	if($cSwitch != null) {

		if(isset($_GET['view']) && $_GET['view'] == "raw") {

			$result = executeRawCommand($cSwitch['addr'], 'show int status');
			echo "<textarea id='intstatustext' wrap='off' readonly='true'>" .htmlspecialchars($result) . "</textarea>";

		} else {
			echo "<div id='intstatuslistcontainer'><table id='intstatuslist'>\n";

			echo "<thead>\n";
			echo "\t<tr>\n";
			echo "\t\t<th>".translate('Port',false)."</th>\n";
			echo "\t\t<th>".translate('Description',false)."</th>\n";
			echo "\t\t<th>".translate('Status',false)."</th>\n";
			echo "\t\t<th>".translate('VLAN',false)."</th>\n";
			echo "\t\t<th>".translate('Duplex',false)."</th>\n";
			echo "\t\t<th>".translate('Speed',false)."</th>\n";
			echo "\t\t<th>".translate('Type',false)."</th>\n";
			echo "\t</tr>\n";
			echo "</thead>\n";

			$interfaces = getAllPortsOnSwitch($cSwitch['addr']);
			if($interfaces === "ERR:AUTH" || $interfaces === "ERR:CONN") {
				echo $interfaces;
			} else {
				echo "<tbody>\n";
				foreach($interfaces as $interface) {
					$link_href = "index.php?switch=".urlencode($cSwitch['addr'])."&port=".urlencode($interface['port']);
					$link = "<a href='$link_href' onclick='beginFadeOutAnimation();'>";
					$link_end = "</a>";
					echo "\t<tr onclick='beginFadeOutAnimation(); window.location.href = \"$link_href\"'>\n";
					echo "\t\t<td>$link" . htmlspecialchars($interface['port']) . "$link_end</td>\n";
					echo "\t\t<td>$link" . htmlspecialchars($interface['desc']) . "$link_end</td>\n";
					echo "\t\t<td>$link<img src='" . getStatusImgPath($interface['stat']) . "'>$link_end</td>\n";
					echo "\t\t<td>$link" . htmlspecialchars($interface['vlan']) . "$link_end</td>\n";
					echo "\t\t<td class='small'>$link" . htmlspecialchars($interface['dupl']) . "$link_end</td>\n";
					echo "\t\t<td class='small'>$link" . htmlspecialchars($interface['spee']) . "$link_end</td>\n";
					echo "\t\t<td class='small'>$link" . htmlspecialchars($interface['type']) . "$link_end</td>\n";
					echo "\t</tr>\n";
				}
				echo "</tbody>\n";
			}

			echo "</table></div>\n";
		}

	}
?>

				</td></tr>

				<tr>
					<td>
						<div class='spread-toolbar toolbar-margin-top'>
							<a href='index.php?switch=<?php echo urlencode($cSwitch['addr']); ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Back'); ?></a>
							<span><a href='intstatuslist.php?switch=<?php echo urlencode($cSwitch['addr']); ?>&view=raw' onclick='beginFadeOutAnimation();'><?php translate('Text'); ?></a>&nbsp;<span style='color:gray;'>|</span>&nbsp;<a href='intstatuslist.php?switch=<?php echo urlencode($cSwitch['addr']); ?>' onclick='beginFadeOutAnimation();'><?php translate('Table'); ?></a></span>
							<a href='#' onclick='beginFadeOutAnimation(); window.location.reload(true);'>&gt;<?php translate('Refresh'); ?></a>
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
