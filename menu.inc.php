<?php require_once('lang.php'); ?>

	<div id='topmenu' style='text-align: center;'>
		<?php if(isset($MAINCTRLS) == false || (isset($MAINCTRLS) == true && $MAINCTRLS == true)) { ?>
		<div style='float: left;'>
			<a href='index.php' class='slubbutton secondary' id='mainmanubtn' title='<?php translate('Back to Start Page'); ?>'><?php translate('Start'); ?></a>
			<a href='maps.php' class='slubbutton secondary' id='mapsbtn'><?php translate('Maps'); ?></a>
		</div>
		<?php } ?>
		<?php if(!empty($ZOOM)) { ?>
		<script>
			function zoom(zoom) { document.body.style.zoom = zoom; }
		</script>
		<style>
			/* style definitions for maps */
			#topmenu {
				opacity: 0.95;
				background-color: rgba(255,255,255,0.75);
				backdrop-filter: blur(8px) brightness(90%) contrast(120%); /* bleeding edge feature of chrome and safari :> */
				border-bottom: 1px solid lightgray;
			}
			div#zoomlinks > a {
				min-width: 50px;
			}
		</style>
		<div style='text-align: center; display: inline-block;' id='zoomlinks'>
			<a href='#' class='slubbutton secondary notypo' onclick='zoom("50%");'>50%</a>&nbsp;
			<a href='#' class='slubbutton secondary notypo' onclick='zoom("80%");'>80%</a>&nbsp;
			<a href='#' class='slubbutton secondary notypo' onclick='zoom("100%");'>100%</a>&nbsp;
			<a href='#' class='slubbutton secondary notypo' onclick='zoom("120%");'>120%</a>&nbsp;
		</div>
		<?php } ?>
		<div style='float: right;'>
			<?php
			$htmlUsername = '';
			if(isset($_SESSION['username']))
				$htmlUsername = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
			?>
			<?php if(ENABLE_PASSWORD_CHANGE) { ?>
				<a href='password.php' class='slubbutton secondary' id='pwchangebtn' title='Passwort auf allen Switchen ändern'><?php translate('Change Password'); ?></a>
			<?php } ?>
			<?php if($htmlUsername != '') { ?>
				<a href='login.php?logout=1' class='slubbutton destructive' id='logoutbtn'><?php echo str_replace('%USER%', $htmlUsername, translate('Log Out %USER%',false)); ?></a>
			<?php } ?>
		</div>
	</div>
