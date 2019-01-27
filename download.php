<?php
/*
 *	minim - PHP framework
    Copyright (C) 2019  Sébastien Boulard

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; see the file COPYING. If not, write to the
    Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
try {
	require 'Core/inc/inc.coreAutoConfig.php';
	$aFiles = Config::get('download');
	if (!isset($_GET['file_id']) 
		|| !array_key_exists($_GET['file_id'], $aFiles) 
		|| !file_exists($aFiles[$_GET['file_id']])) {
		die();
	}
	$sFilePath = $aFiles[$_GET['file_id']];
	$sFileName = basename($sFilePath);
	ini_set('zlib.output_compression', 0);
	switch(pathinfo($sFilePath, PATHINFO_EXTENSION)) {
		case 'txt':
			header("Content-Type: text/plain");
			break;
		case 'pdf':
			header("Content-Type: application/pdf");
			break;
		case 'jpg':
			header("Content-Type: image/jpg");
			break;
		case 'png':
			header("Content-Type: image/png");
			break;
		case 'zip':
		case 'gz':	
			header("Content-Type: application/zip");
			break;
		default:
			die();
	}
	//counter
	DownloadMgr::counterUpdate($aFiles, $_GET['file_id']);
	header('Pragma: public');
	header("Expires: 0"); // obligé
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // obligé
	header("Content-Type: image/jpg");
	header('Content-Type: application/octetstream; name="'.$sFileName.'"');
	header("Content-Disposition: attachment; filename=\"".$sFileName."\";" );
	header('Content-MD5: '.base64_encode(md5_file($sFilePath)));
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($sFilePath));
	header('Date: '.gmdate(DATE_RFC1123));
	header('Expires: '.gmdate(DATE_RFC1123, time()+1));
	header('Last-Modified: '.gmdate(DATE_RFC1123, filemtime($sFilePath)));
	readfile($sFilePath);
	exit;
} catch (GenericException $e) {
	echo $e;
} catch (Exception $e) {
	if (defined('DEV') && DEV === true) { 
		echo $e-> getMessage().'<br />';
		print_r($e->getTrace());
	}
}



$iTest = $sFileName;
