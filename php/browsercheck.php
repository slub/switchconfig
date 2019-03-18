<?php

require_once('lang.php');

$ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
if(strpos($ua, 'Edge/') !== false || strpos($ua, 'Trident/7.0') !== false) {
	echo "<div class='infobox warn'>".translate('Internet Explorer and Microsoft Edge are not supportet.',false)."</div>";
	die();
}
