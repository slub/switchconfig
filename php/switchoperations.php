<?php

require_once('functions.php');


function getAllPortsOnSwitch($switch, $fullinfo = true, $voiceinfo = false) {

	$connection = ssh2_connect($switch, 22);

	if($connection === false)
		return "ERR:CONN";

	if(ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']) === false)
		return "ERR:AUTH";

	// first command
	$stream = ssh2_exec($connection, 'sh int status');
	stream_set_blocking($stream, true);
	$sh_int_status = @stream_get_contents($stream);
	fclose($stream);

	// second command
	if($fullinfo == true) { // time-intensive... use only if really necessary
		$connection2 = ssh2_connect($switch, 22);
		ssh2_auth_password($connection2, $_SESSION['username'], $_SESSION['password']);
		$stream_detail = ssh2_exec($connection2, 'sh int');
		stream_set_blocking($stream_detail, true);
		$sh_int_detail = @stream_get_contents($stream_detail);
		fclose($stream_detail);
	}
	// third command
	if($voiceinfo == true) { // time-intensive... use only if really necessary
		$connection3 = ssh2_connect($switch, 22);
		ssh2_auth_password($connection3, $_SESSION['username'], $_SESSION['password']);
		$stream_switchport = ssh2_exec($connection3, 'sh int switchport');
		stream_set_blocking($stream_switchport, true);
		$sh_int_switchport = @stream_get_contents($stream_switchport);
		fclose($stream_switchport);
	}

	// parse ssh output
	$linecounter = 0;
	$columns = [];
	foreach(explode("\n", $sh_int_status) as $if_line) {
		// ignore empty lines
		if(trim($if_line) == "") continue;
		$linecounter ++;

		// parse table head line
		if($linecounter == 1) {
			$columns = parseIntStatusHead($if_line);
			continue;
		}

		// parse port table
		$_newport['port'] = trim(substr($if_line, $columns[0]['offset'], $columns[0]['length']));
		$_newport['altn'] = str_replace("Gi", "GigabitEthernet", $_newport['port']);
		$_newport['desc'] = trim(substr($if_line, $columns[1]['offset'], $columns[1]['length']));
		$_newport['stat'] = trim(substr($if_line, $columns[2]['offset'], $columns[2]['length']));
		$_newport['vlan'] = trim(substr($if_line, $columns[3]['offset'], $columns[3]['length']));
		$_newport['dupl'] = trim(substr($if_line, $columns[4]['offset'], $columns[4]['length']));
		$_newport['spee'] = trim(substr($if_line, $columns[5]['offset'], $columns[5]['length']));
		$_newport['type'] = trim(substr($if_line, $columns[5]['offset']+$columns[5]['length']));
		$_newport['voip'] = "";
		$_newport['errs'] = false;
		$_newport['errt'] = "";

		// get details from second command
		$currentport = "";
		if(!empty($sh_int_detail)) foreach(explode("\n", $sh_int_detail) as $if_line2) {
			// new interface section starting
			if(!startsWith($if_line2, " "))
			$currentport = explode(" ", $if_line2)[0];

			// if we are in the section of our current interface
			if($currentport == $_newport['port'] || $currentport == $_newport['altn']) {

				// get full description
				if(startsWith($if_line2, "  Description:"))
				$_newport['desc'] = str_replace("  Description: ", "", $if_line2);

				// line conatins reliability information
				if(strpos($if_line2, "reliability") !== false) {
					$rel_text = trim(explode(",", $if_line2)[0]);
					$rel_values = explode(" ", $rel_text)[1];
					$rel_curr = explode("/", $rel_values) [0];

					if($rel_curr != 255) { // errors exist if reliability lower than 255
						$_newport['errs'] = true;
						$_newport['errt'] .= "\n" . trim($if_line2);
					}
				}

				// line conatins error information
				if(strpos($if_line2, "error") !== false) {
					$_newport['errt'] .= "\n" . trim($if_line2);
				}
			}
		}

		// get more details from third command
		$currentport = "";
		if(!empty($sh_int_switchport)) foreach(explode("\n", $sh_int_switchport) as $if_line2) {
			// new interface section starting
			if(startsWith($if_line2, "Name: "))
			$currentport = trim(str_replace("Name: ", "", $if_line2));

			// if we are in the section of our current interface
			if($currentport == $_newport['port'] || $currentport == $_newport['altn']) {

				// get full description
				if(startsWith($if_line2, "Voice VLAN: "))
				$_newport['voip'] = trim(str_replace(["Voice VLAN: ", "(default)"], "", $if_line2));

			}
		}

		// add to result array
		$interfaces[] = array(
			'port' => $_newport['port'], // switchport
			'desc' => $_newport['desc'], // description
			'stat' => $_newport['stat'], // status
			'vlan' => $_newport['vlan'], // vlan
			'dupl' => $_newport['dupl'], // duplex
			'spee' => $_newport['spee'], // speed
			'type' => $_newport['type'], // type
			'voip' => $_newport['voip'], // voip
			'errs' => $_newport['errs'], // has errors (true|false)
			'errt' => $_newport['errt'], // error text
		);
	}

	return $interfaces;

}

function setValues($switch, $switchport, $vlan, $desc, $voip) {
	$connection = ssh2_connect($switch, 22);
	ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']);

	$stdio_stream = ssh2_shell($connection);

	// if description is blank, send command "no description"
	if($desc == "") $desc_cmd = "no description";
	else $desc_cmd = "description $desc";

	// create voip command
	if(VOICE_VLAN == -1) $voip_cmd = "";
	elseif($voip == "enabled")
		$voip_cmd = "switchport voice vlan " . VOICE_VLAN . "\n" .
		            (DO_SET_POE ? "power inline auto" : "");
	else
		$voip_cmd = "no switchport voice vlan" . "\n" .
		            (DO_SET_POE ? "power inline never" : "");

	if($vlan == "disabled") {
		// disable port
		fwrite($stdio_stream, "conf t" . "\n" .
							  "int $switchport" . "\n" .
							  "shutdown" . "\n" .
							  $voip_cmd . "\n" .
							  $desc_cmd . "\n" .
							  "end" . "\n" .
							  (DO_WR_MEM ? "wr mem" : "") . "\n" .
							  "exit" . "\n");
	} else {
		fwrite($stdio_stream, "conf t" . "\n" .
							  "int $switchport" . "\n" .
							  "no shutdown" . "\n" .
							  "switchport access vlan $vlan" . "\n" .
							  $voip_cmd . "\n" .
							  $desc_cmd . "\n" .
							  "end" . "\n" .
							  (DO_WR_MEM ? "wr mem" : "") . "\n" .
							  "exit" . "\n");
	}

	stream_set_blocking($stdio_stream, true); // wait until command executed
	return stream_get_contents($stdio_stream);
}

function executeRawCommand($switch, $cmd) {
	$connection = ssh2_connect($switch, 22);
	ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']);

	$stream = ssh2_exec($connection, $cmd);
	stream_set_blocking($stream, true);
	$result = stream_get_contents($stream);

	fclose($stream);
	return $result;
}

function executeRawShell($switch, $cmd) {
	$connection = ssh2_connect($switch, 22);
	ssh2_auth_password($connection, $_SESSION['username'], $_SESSION['password']);

	$stream = ssh2_shell($connection, 'vanilla', null, 1000, 10000, SSH2_TERM_UNIT_CHARS);
	fwrite($stream, $cmd);
	stream_set_blocking($stream, true);
	$result = stream_get_contents($stream);

	fclose($stream);
	return $result;
}

function parseIntStatusHead($line) {
	$position = 0;
	$columnWidths = [];
	$currentColumnWidth = 0;
	$flag = false;
	foreach(str_split(trim($line)) as $char) {
		if($flag && $char != ' ' && $currentColumnWidth > 0) {
			$title = trim(substr($line, $position, $currentColumnWidth));

			// this is an ultra-ugly but necessary workaround, have a look at the original Cisco switch output, the "Speed" column is shifted by one char:
			// Port         Name               Status       Vlan       Duplex  Speed Type
			// Gi1/0/1      really cool port   connected    20         a-full a-1000 10/100/1000BaseTX
			if($title == 'Duplex') {
				$currentColumnWidth -= 1;
			}

			$columnWidths[] = ['title'=>$title, 'offset'=>$position, 'length'=>$currentColumnWidth];
			$position += $currentColumnWidth;
			$currentColumnWidth = 0;
			$flag = false;
		}
		$currentColumnWidth ++;
		if($char == ' ') {
			$flag = true;
		}
	}
	return $columnWidths;
}
