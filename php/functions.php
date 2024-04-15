<?php

/* General */
const TEXT_LENGTH = 25;
function shortText($in) {
	return strlen($in) > TEXT_LENGTH ? substr($in,0,TEXT_LENGTH)."..." : $in;
}

function getSwitchByAddr($addr) {
	foreach(SWITCHES as $s) {
		if($s['addr'] == $addr) {
			return $s;
		}
	}
	return null;
}

function getSnippetById($id) {
	foreach(SNIPPETS as $s) {
		if($s['id'] == $id) {
			return $s;
		}
	}
	return null;
}

function removeInvalidChars($string) {
	// this function removes chars which cannot be set as port description on the switch
	// replace german umlauts
	$string = str_replace("Ä", "Ae", $string); $string = str_replace("Ö", "Oe", $string); $string = str_replace("Ü", "Ue", $string);
	$string = str_replace("ä", "ae", $string); $string = str_replace("ö", "oe", $string); $string = str_replace("ü", "ue", $string);
	$string = str_replace("ß", "ss", $string);
	// remove all other chars
	// except A-Z, a-z, 0-9, Spaces, -, _, ., / (for interface name, e.g. Gi1/0/1)
	return preg_replace('/[^A-Za-z0-9\-\/\_\.\ ]/', '', $string);
}

function startsWith($haystack, $needle) {
	if(!is_array($needle)) $needle = [$needle];
	foreach($needle as $n) {
		$length = strlen($n);
		if(substr($haystack, 0, $length) === $n) return true;
	}
	return false;
}

function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if($length == 0) return true;
	return (substr($haystack, -$length) === $needle);
}

function str_chop_lines($str, $lines = 3) {
	return implode("\n", array_slice(explode("\n", $str), $lines));
}

function getStatusImgPath($status) {
	if($status == 'connected') return 'img/connected.svg';
	elseif($status == 'notconnect') return 'img/notconnected.svg';
	else return 'img/disabled.svg';
}

function formatMac($input) {
	// format mac address in Cisco-like format
	// ca:ff:ee:ca:ff:ee  ca-ff-ee-ca-ff-ee  caff.eeca.ffee  caffeecaffee  ->  caff.eeca.ffee
	$naked = strtolower(str_replace([':', '-', '.'], '', $input));
	if(strlen(str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'], '', $naked)) > 0
	|| strlen($naked) != 12) {
		return false;
	}
	return addMacSeparator($naked);
}
function addMacSeparator($nakedMac) {
	$result = '';
	while(strlen($nakedMac) > 0) {
		$sub = substr($nakedMac, 0, 4);
		$result .= $sub . '.';
		$nakedMac = substr($nakedMac, 4, strlen($nakedMac));
	}
	// remove trailing dot
	$result = substr($result, 0, strlen($result) - 1);
	return $result;
}

/* Maps */
function switchOnMapAvail($MAPS, $switch) {
	foreach($MAPS as $currentmap) {
		foreach($currentmap['items'] as $item) {
			if($item['switch'] == $switch) {
				return $currentmap['name'];
			}
		}
	}
	return false;
}

/* Interface Matrix */
function getPort($interfaces, $port) {
	foreach($interfaces as $iface) {
		if(isset($iface['port']) && $iface['port'] == $port)
			return $iface;
	}
	return false;
}

function getPortStatus($interfaces, $port) {
	foreach($interfaces as $iface) {
		if(isset($iface['port']) && $iface['port'] == $port) {
			if($iface['stat'] == "connected" || $iface['stat'] == "notconnect")
				return $iface['stat'];
			else
				return "other";
		}
	}
	return "none";
}
