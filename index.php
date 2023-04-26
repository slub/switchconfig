<?php

$SUPRESS_NOTLOGGEDIN_MESSAGE = true;
require_once('php/session.php');

require_once('php/functions.php');
require_once('config.php');
require_once('php/switchoperations.php');
require_once('lang.php');


/* READ INPUT */
$cSwitch = null; // current switch
$switch = "";
$port = "";
$vlan = "";
$voip = "";
$desc = "";

if (isset($_POST['port']))
	$port = removeInvalidChars($_POST['port']);
elseif (isset($_GET['port']))
	$port = removeInvalidChars($_GET['port']);

if (isset($_POST['vlan']))
	$vlan = intval($_POST['vlan']);
elseif (isset($_GET['vlan']))
	$vlan = intval($_GET['vlan']);

if (isset($_POST['voip']))
	$voip = $_POST['voip'];
elseif (isset($_GET['voip']))
	$voip = $_GET['voip'];

if (isset($_POST['description']))
	$desc = removeInvalidChars($_POST['description']);
elseif (isset($_GET['description']))
	$desc = removeInvalidChars($_GET['description']);

if (isset($_POST['switch']))
	$switch = removeInvalidChars($_POST['switch']);
elseif (isset($_GET['switch']))
	$switch = removeInvalidChars($_GET['switch']);

/*** GET SWITCH OBJECT ***/
$cSwitch = getSwitchByAddr($switch);

/*** CHANGE PORT SETTINGS IF REQUESTED ***/
$cmdOutput = "";
$info = "";
$infoClass = "";
if($cSwitch != null && isset($_POST['action']) && $_POST['action'] == "SETVALUES") {
	$cmdOutput = setValues($cSwitch['addr'], $port, $vlan, $desc, $voip);
	$info = translate('New settings sent to switch.', false);
	$infoClass = "ok";
}

/*** READ PORT INFO ***/
$interfaces = array();
if($cSwitch != null) {

	$new_interfaces = getAllPortsOnSwitch($cSwitch['addr'], true, true);
	if($new_interfaces === "ERR:AUTH") {
		$cSwitch = null;
		$infoClass = "error";
		$info = translate('Switch authentication failed.', false);
	}
	elseif($new_interfaces === "ERR:CONN") {
		$cSwitch = null;
		$infoClass = "error";
		$info = translate('Error while establishing connection to switch.', false);
	}
	else {
		$interfaces = $new_interfaces;
		foreach($interfaces as $interface) {
			if($interface['port'] == $port) {
				$vlan = $interface['vlan'];
				$desc = $interface['desc'];
				if($interface['voip'] != "none" && $interface['voip'] != "")
					$voip = "enabled";
				else
					$voip = "disabled";
			}
		}
	}

}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php translate('Switchconfig'); ?></title>
	<?php require('head.inc.php'); ?>
	<script language='javascript' type='text/javascript' src='js/explode.js'></script>
</head>
<body>
	<script>
	function beginFadeOutAnimation() {
		document.body.classList.add('loading');
		document.getElementById('imgSwitch').classList.remove('delayIn');
		document.getElementById('imgSwitch').style.opacity = 0;
		document.getElementById('imgLoading').style.opacity = 1;
		document.getElementById('submit_changeform').innerHTML = "<?php translate('Please wait...'); ?>";
		// disable fields while loading/applying changes
		document.getElementById('switch').setAttribute('readOnly', true);
		document.getElementById('port').setAttribute('readOnly', true);
		document.getElementById('vlan').setAttribute('readOnly', true);
		document.getElementById('voip').setAttribute('readOnly', true);
		document.getElementById('description').setAttribute('readOnly', true);
		document.getElementById('submit_changeform').setAttribute('disabled', true);
		document.getElementById('function_intstatuslist').setAttribute('disabled', true);
		document.getElementById('function_intmatrix').setAttribute('disabled', true);
		document.getElementById('function_more').setAttribute('disabled', true);
		document.getElementById('function_snippets').setAttribute('disabled', true);
		document.getElementById('function_search').setAttribute('disabled', true);
	}
	</script>

	<?php if(!empty($cmdOutput)) { ?>
	<span id='sshDebugOutput'>
		[DEBUG] SSH SWITCH OUTPUT:
		<?php echo $cmdOutput . "\n" ?>
	</span>
	<?php } ?>

	<div id='container'>
		<h1 id='title'><div id='logo'></div></h1>

		<div id='splash' class='login'>

			<div id='subtitle'>
				<div id='imgContainer'>
					<img id='imgLoading' src='img/loading.svg'></img>
					<?php if($infoClass == 'ok') { ?>
						<img id='imgSwitch' src='img/switch.png' class='easteregg-trigger delayIn' onclick='boom()' title='initiate self destruction'></img>
						<img id='imgTick' class='delayOut' src='img/tick_anim.svg'></img>
					<?php } else { ?>
						<img id='imgSwitch' src='img/switch.png' class='easteregg-trigger' onclick='boom()' title='initiate self destruction'></img>
					<?php } ?>
				</div>
				<?php translate('This web application allows you to configure Cisco switches through a graphical interface.'); ?>
			</div>

			<?php if($info != '') { ?>
				<tr><td><div class='infobox <?php echo $infoClass; ?>'><?php echo $info; ?></div></td></tr>
			<?php } ?>

			<form method='GET' name='switchform' onsubmit='beginFadeOutAnimation();'>
				<div class='form-row'>
					<select name='switch' value='' id='switch' height='1' class='fullwidth hand' autofocus='true' onchange='this.form.submit(); beginFadeOutAnimation();'>
						<?php if($cSwitch == null) { ?>
							<option value='' selected='true' disabled='true'>&gt;&gt;&gt; <?php translate("Please select"); ?> &lt;&lt;&lt;</option>
						<?php } ?>
						<?php
						$last_group = '';
						foreach(SWITCHES as $s) {
							if($s['group'] != $last_group) {
								echo "</optgroup>\n";
								echo "<optgroup label='" . $s['group'] . "'>\n";
								$last_group = $s['group'];
							}
							$select = "";
							if($cSwitch != null && $s['addr'] == $cSwitch['addr']) $select = "selected";
							echo "<option value='" . $s['addr'] . "' $select>" . $s['name'] . "</option>\n";
						}
						?>
					</select>
					<span class='tip'><label for='switch'><?php translate('Switch'); ?></label></span>
					<?php
					$mapresult = $cSwitch==null ? false : switchOnMapAvail(MAPS, $cSwitch['addr']);
					if($mapresult !== false) {
						echo "<a href='maps.php?map=" . $mapresult . "&highlightswitch=".$cSwitch['addr']."#".$cSwitch['addr']."'><img src='img/location.svg' title='Zeige Switch auf Karte' class='tipicon pointer pulse'></img></a>\n";
					}
					?>
				</div>
			</form>

			<form method='GET' name='portform' onsubmit='beginFadeOutAnimation();'>
				<div class='form-row tooltip'>
					<input type='hidden' name='switch' value='<?php echo $cSwitch==null ? '' : $cSwitch['addr']; ?>'></input>
					<select name='port' value='' id='port' height='1' class='fullwidth hand' onchange='this.form.submit(); beginFadeOutAnimation();' <?php if($cSwitch == null) echo "disabled"; ?>>
						<?php
						if($port == "" && $cSwitch != null)
							echo "<option value='' selected='true' disabled='true'>&gt;&gt;&gt; ".translate("Please select",false)." &lt;&lt;&lt;</option>";
						$icon = "";
						$queried_port_found = false;
						foreach($interfaces as $interface) {
							if(startsWith($interface['port'], "Gi") // only show "normal" ports
								&& startsWith($interface['port'], "Gi1/1") == false
								&& startsWith($interface['port'], "Gi2/1") == false
								&& $interface['vlan'] != "trunk" // and don't show trunk ports
							) {
								$selected = "";
								if($interface['port'] == $port) {
									$selected = "selected";
									$icon = getStatusImg($interface['stat'], "tipicon");
									$queried_port_found = true;
								}
								echo "<option value='" . $interface['port'] . "' $selected>" . $interface['port'] . "</option>\n";
							}
						}
						if($port != "" && $cSwitch != null && $queried_port_found == false) {
							echo "<option value='' selected>&gt;&gt;&gt; ".translate("Invalid Port!",false)." &lt;&lt;&lt;</option>";
							$port = ""; $vlan = ""; $desc = "";
						}
						?>
					</select>
					<span class='tip'><label for='port'><?php translate('Port'); ?></label></span>
					<?php echo $icon ?>
					<span class='tooltiptext'>
						<div><b><?php translate('Icons'); ?></b></div>
						<div class='demoStatusIcon'>
							<img src='img/connected.svg'></img>
							<span>&nbsp;<?php translate('Connected'); ?></span>
						</div>
						<div class='demoStatusIcon'>
							<img src='img/notconnected.svg'></img>
							<span>&nbsp;<?php translate('Not Connected'); ?></span>
						</div>
						<div class='demoStatusIcon'>
							<img src='img/disabled.svg'></img>
							<span>&nbsp;<?php translate('Other State (Disabled)'); ?></span>
						</div>
					</span>
				</div>
			</form>

			<form method='POST' name='setvaluesform' onsubmit='beginFadeOutAnimation();'>
				<input type='hidden' name='action' value='SETVALUES'></input>
				<input type='hidden' name='switch' value='<?php echo $cSwitch==null ? '' : $cSwitch['addr']; ?>'></input>
				<input type='hidden' name='port' value='<?php echo $port; ?>'></input>
				<div class='multirow'>
					<div class='form-row' style='flex-basis:66%'>
						<select name='vlan' value='' id='vlan' class='hand' <?php if($cSwitch == null || $port == "") echo "disabled"; ?>>
							<?php $selected = false; ?>
							<?php foreach(VISIBLE_VLAN as $v) { ?>
								<option value='<?php echo $v['id']; ?>' <?php if($vlan==$v['id']){echo "selected";$selected=true;} ?>><?php echo $v['name']; ?></option>
							<?php } ?>
							<?php if ($vlan == "") {
								echo "<option value='' selected></option>";
							} elseif ($selected == false) {
								echo "<option value='$vlan' selected>[$vlan]  (".translate('custom',false).")</option>";
							} ?>
						</select>
						<span class='tip'><label for='vlan'><?php translate('VLAN'); ?></label></span>
					</div>
					<div class='form-row tooltip' style='flex-basis:33%'>
						<select name='voip' value='' id='voip' class='hand' <?php if ($cSwitch == null || $port == "") echo "disabled"; ?>>
							<option value='disabled' <?php if($voip == "disabled") echo "selected"; ?>><?php translate('Off'); ?></option>
							<option value='enabled' <?php if($voip == "enabled") echo "selected"; ?>><?php translate('On'); ?></option>
						</select>
						<span class='tip'><label for='voip'><?php translate('VoIP'); ?></label></span>
						<span class='tooltiptext'>
							<b><?php translate('VoIP-Options'); ?></b>
							<div style="text-align: left;">
								<b>[<?php translate('On'); ?>]</b>&nbsp;<?php translate('Set voice VLAN and PoE on'); ?>
								<br>
								<b>[<?php translate('Off'); ?>]</b>&nbsp;<?php translate('No voice VLAN and PoE off'); ?>
							</div>
						</span>
					</div>
				</div>

				<div class='form-row tooltip'>
					<input type='text' name='description' id='description' maxlength='35' class='fullwidth' value='<?php echo $desc ?>' <?php if($cSwitch == null || $port == "") echo "disabled"; ?>></input>
					<span class='tip'><label for='description'><?php translate('Description'); ?></label></span>
					<?php if(PORT_DESCRIPTION_HINT != '') { ?>
					<span class='tooltiptext'>
						<?php echo PORT_DESCRIPTION_HINT; ?>
					</span>
					<?php } ?>
				</div>

				<div class='form-row'>
					<button type='submit' class='slubbutton fullwidth' id='submit_changeform' <?php if($cSwitch == null || $port == "") echo "disabled"; ?>><?php translate('Save'); ?></button>
				</div>
			</form>

			<div class='stretched-toolbar'>
				<?php
				$options_disabled = $cSwitch==null ? "disabled='true'" : "";
				$switchportparams = "switch=".urlencode($cSwitch==null ? '' : $cSwitch['addr']) . "&port=".urlencode($port!='' ? '' : $port);
				?>
				<a href='intstatuslist.php?<?php echo($switchportparams); ?>' id='function_intstatuslist' class='slubbutton secondary' onclick='beginFadeOutAnimation()' <?php echo $options_disabled; ?>><?php translate('Port List'); ?></a>
				<a href='intmatrix.php?<?php echo($switchportparams); ?>' id='function_intmatrix' class='slubbutton secondary' onclick='beginFadeOutAnimation()' <?php echo $options_disabled; ?>><?php translate('Port Matrix'); ?></a>
				<button id='function_more' class='slubbutton secondary' <?php echo $options_disabled; ?>><?php translate('More'); ?>
					<div id='function_more_container'>
						<a href='search.php?<?php echo($switchportparams); ?>' id='function_search' class='slubbutton secondary' onclick='beginFadeOutAnimation()' <?php echo $options_disabled; ?>><?php translate('MAC Search'); ?></a>
						<a href='snippets.php?<?php echo($switchportparams); ?>' id='function_snippets' class='slubbutton secondary' onclick='beginFadeOutAnimation()' <?php echo $options_disabled; ?>><?php translate('Snippets'); ?></a>
					</div>
				</button>
			</div>
		</div>

		<?php require('foot.inc.php'); ?>

	</div>

<?php require('menu.inc.php'); ?>

</body>
</html>
