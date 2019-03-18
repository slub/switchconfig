<?php

require_once('php/session.php');
session_write_close();

require_once('php/functions.php');
require_once('switchlist.php');
require_once('php/switchoperations.php');
require_once('lang.php');


$sAddr = "";
$cSwitch = null;
$interfaces = [];
if(isset($_GET['switch']) && $_GET['switch'] != "") {
	$cSwitch = getSwitchByAddr($_GET['switch']);
	if($cSwitch != null) {
		$tempInterfaces = getAllPortsOnSwitch($cSwitch['addr'], true, true);
		if($tempInterfaces == "ERR:AUTH" || $tempInterfaces == "ERR:CONN") {
			$cSwitch = null;
		} else {
			$sAddr = $cSwitch['addr'];
			$interfaces = $tempInterfaces;
		}
	}
}

$refreshtableonly = false;
if(isset($_GET['refreshtableonly']) && $_GET['refreshtableonly'] == "1")
	$refreshtableonly = true;

	##########

	// counters
	$nConnected = 0;
	$nNotconnected = 0;
	$nVoipEnabled = 0;
	$nIoError = 0;
	$nDisabled = 0;

	function echoPortTD($interfaces, $port, $sAddr) {
		// make counters accessable
		global $nConnected;
		global $nNotconnected;
		global $nVoipEnabled;
		global $nIoError;
		global $nDisabled;

		// get port details
		$portInfo = getPort($interfaces, $port);
		$status = getPortStatus($interfaces, $port);

		// build text, title and onclick event
		$statustext = "";
		$onclickevent = "";
		if($status != "none") {
			$statustext = $portInfo['stat'] . " [VLAN " . $portInfo['vlan'] . "]";
			if($portInfo['voip'] != "none" && $portInfo['voip'] != "")
				$statustext .= " [VoIP " . explode(" ", $portInfo['voip'])[0] . "]";

			$onclickevent = "onclick=\"beginFadeOutAnimation(); window.location.href='index.php?switch=".urlencode($sAddr)."&port=" . urlencode($port) . "'\"";

			// set css class "error" if errors were reported
			$title = translate("Packet Error Information (since last reboot):",false) . str_replace("<br>", "\n", htmlspecialchars($portInfo['errt']));
			$htmlclass_additional = "";
			$errorinfo = "";
			if($portInfo['errs'] == true) {
				$nIoError ++;
				$htmlclass_additional .= " error";
				$errorinfo = "<br>".translate("Packet Errors - Check Cable!",false)."<br><small>" . htmlspecialchars($portInfo['errt']) . "</small>";
			}

			// set css class "trunk" if port is an trunk port
			if($portInfo['vlan'] == "trunk") {
				$htmlclass_additional .= " trunk";
			}
			// set css class "voice" if port has set a voice vlan
			if($portInfo['voip'] != "none" && $portInfo['voip'] != "") {
				$htmlclass_additional .= " voip";
				$nVoipEnabled ++;
			}
		}

		// echo final td
		echo "<td class='$status tooltip $htmlclass_additional' title='$title' $onclickevent>\n";
		if($statustext != "") {
			echo "<span class='tooltiptext'>" .
			     "<div><b>" . $port . "</b> <span class='clickhint'>".translate('(click to configure)',false)."</span></div>" .
			     "<div>" . $statustext . "</div>" .
			     "<div>" . htmlspecialchars($portInfo['desc']) . "</div>" .
			     "<div>" . $errorinfo . "</div>" .
			     "</span>\n";
		}
		echo "</td>\n\n";

		// count
		if($status == "connected") $nConnected ++;
		if($status == "notconnect") $nNotconnected ++;
		if($status == "other") $nDisabled ++;
	}
?>

<?php if(!$refreshtableonly) { ?>
<!DOCTYPE html>
<html>
<head>
	<title><?php translate('Portmatrix'); ?> - <?php translate('Switchconfig'); ?></title>
	<?php require('head.inc.php'); ?>
	<link rel='stylesheet' type='text/css' href='style/intmatrix.css'>
</head>
<body onLoad=''>
	<script>
	function beginFadeOutAnimation() {
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
	}
	</script>
	<script>
		function reloadMatrix() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
				 document.getElementById("matrix").innerHTML = this.responseText;
				}
			};
			xhttp.open("GET", "intmatrix.php?switch=<?php echo $sAddr; ?>&refreshtableonly=1", true);
			xhttp.send();
			console.log("Interface table (matrix) refreshed.");
		}
		<?php if(isset($_GET['autorefresh']) && $_GET['autorefresh'] == "1")
				echo "window.setInterval(reloadMatrix, 5000);"; ?>
	</script>

	<div id='logincontainer'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='loginsplash' class='big'>
			<div id='subtitle'>
				<?php if($cSwitch != null) { ?>
					<h2><?php echo $cSwitch['name']; ?></h2>
				<?php } else { ?>
					<div class='infobox warn'><?php translate('No switch selected'); ?></div>
					<style>.matrix { display: none }</style>
				<?php } ?>
				<hr/>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<img id='imgSwitch' src='img/switch.png'></img>
				</div>
				<?php translate('This page shows all ports and packet errors, arranged as on the device.'); ?>
			</div>

			<table width='100%' id='logintable'>
				<tr><td>
					<table id='matrix' class='matrix'>
<?php } ?>

						<?php ##### for small 10-port switches #####
						if(getPortStatus($interfaces, "Gi0/1") != "none") {
							require('php/intmatrix-layouts/10port.php');
						}
						?>

						<?php ##### for regular nexus switches (up to 3 stacked) #####
						if(getPortStatus($interfaces, "Gi1/0/1") != "none") {
							require('php/intmatrix-layouts/48port.php');
						}
						?>

						<tr class='legende'><td>&nbsp;</td></tr>
						<tr class='legende'><td colspan='13'><b><?php translate('Legend'); ?></b></td></tr>
						<tr class='legende'>
							<td class='connected'></td>
							<td colspan='5'><?php translate('Connected'); ?>&nbsp;[<?php echo $nConnected; ?>]</td>
							<td>&nbsp;</td>
							<td class='notconnect voip'></td>
							<td colspan='5'><?php translate('VoIP enabled'); ?>&nbsp;[<?php echo $nVoipEnabled; ?>]</td>
							<td>&nbsp;</td>
							<td class='notconnect'></td>
							<td colspan='5'><?php translate('Not Connected'); ?>&nbsp;[<?php echo $nNotconnected; ?>]</td>
							<td>&nbsp;</td>
							<td class='notconnect error-static'></td>
							<td colspan='5'><?php translate('I/O Error'); ?>&nbsp;[<?php echo $nIoError; ?>]</td>
						</tr>
						<tr class='legende'>
							<td class='other'></td>
							<td colspan='5'><?php translate('Disabled'); ?>&nbsp;[<?php echo $nDisabled; ?>]</td>
							<td>&nbsp;</td>
							<td class='connected trunk'></td>
							<td colspan='5'><?php translate('Trunk connected'); ?></td>
							<td>&nbsp;</td>
							<td class='notconnect trunk'></td>
							<td colspan='5'><?php translate('Trunk not connected'); ?></td>
						</tr>
<?php if(!$refreshtableonly) { ?>
					</table>
				</td></tr>

				<tr>
					<td>
						<div class='spread-toolbar toolbar-margin-top'>
							<a href='index.php?switch=<?php echo $sAddr; ?>' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Back'); ?></a>
							<?php if (isset($_GET['autorefresh']) && $_GET['autorefresh'] == "1") { ?>
								<a href='intmatrix.php?switch=<?php echo $sAddr; ?>&autorefresh=0' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Disable auto refresh'); ?></a>
							<?php } else { ?>
								<a href='intmatrix.php?switch=<?php echo $sAddr; ?>&autorefresh=1' onclick='beginFadeOutAnimation();'>&gt;<?php translate('Enable auto refresh'); ?></a>
							<?php } ?>
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
<?php } ?>
