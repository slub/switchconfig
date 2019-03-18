<?php

$_LANG = [];

// load translations
$browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$transFile = 'lang/' . $browserLang . '.php';
if(file_exists($transFile)) {
	require_once($transFile);
}


function translate($string, $echo = true) {
	global $_LANG;
	if(isset($_LANG[$string])) {
		if($echo) echo $_LANG[$string];
		else return $_LANG[$string];
	} else {
		if($echo) echo $string;
		else return $string;
	}
}
