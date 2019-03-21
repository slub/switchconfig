<?php

// delivers the map image to the user if authenticated
// (/maps dir is protected by .htaccess)

const DS = DIRECTORY_SEPARATOR;
require_once('php/session.php');


if(!empty($_GET['img'])) {
	$imgPath = __DIR__.DS.'maps'.DS.basename($_GET['img']);
	if(file_exists($imgPath)) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		header('Content-Type: ' . finfo_file($finfo, $imgPath));
		finfo_close($finfo);
		readfile($imgPath);
		exit;
	}
}
