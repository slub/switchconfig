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
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) return true;
	return (substr($haystack, -$length) === $needle);
}

function str_chop_lines($str, $lines = 3) {
	return implode("\n", array_slice(explode("\n", $str), $lines));
}

function getStatusImg($status, $htmlclass = "") {
	if ($status == "connected")
		return "<img src='img/connected.svg' title='connected' class='$htmlclass'></img>";
	elseif ($status == "notconnect")
		return "<img src='img/notconnected.svg' title='" . $status . "' class='$htmlclass'></img>";
	else
		return "<img src='img/disabled.svg' title='" . $status . "' class='$htmlclass'></img>";
}

/* Maps */
function switchOnMapAvail($MAPS, $switch) {
	foreach ($MAPS as $currentmap) {
		foreach ($currentmap['items'] as $item) {
			if ($item['switch'] == $switch) {
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
