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
final class Toolz_FileSystem {
	
	public static function getRecursivePathList($sDirPath) {
		if (!is_dir($sDirPath)) { 
			throw new Exception ($sDirPath.' is not a valid directory');
		}
		$aPathArray = array();
		$oIt = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sDirPath));
		foreach ($oIt as $oDir) {
			$aPathArray[] = $oDir->getPathname();
		}
		unset($oIt);
		return $aPathArray;	
	}
	
	public static function getPathList($sDirPath) {
		$aPathArray = array();
		foreach(new DirectoryIterator($sDirPath) as $oFileInfo) {
			if(!$oFileInfo->isDot()) {
				$aPathArray[] = $oFileInfo->getPathname();
			}
		}
		return $aPathArray;	
	}
	
	public static function purgeDir($sDirPath) {
		foreach(self::getPathList($sDirPath) as $sFilePath) {
			if(is_dir($sFilePath)) {
				Toolz_FileSystem::purgeDir($sFilePath);
			} else {
				unlink($sFilePath);
			}
		}
		return true;
	}
	
	public static function clearDir($sDir, $bDelete=false) {
		$rDir = opendir($sDir); 
		while($sFile = readdir($rDir)) { 
			if(!in_array($sFile, array('.', '..'))){
				if(is_dir($sDir.'/'.$sFile)) {
					Toolz_FileSystem::clearDir($sDir.'/'.$sFile, true);
				} else {
					unlink($sDir.'/'.$sFile);
				}
			}
		} 
		closedir($rDir);
		if($bDelete == true) {
			rmdir($sDir);
		}
	}
	
	public static function uploadFile($sIndex, $sUploadPath) {
		if(!file_exists($sUploadPath.'.htaccess')) {
			file_put_contents(
				$sUploadPath.'.htaccess', 
				'<Files *.php>Deny from all</Files>'
			);
		}
		$aFile = UserRequest::getFiles();
		$sFileTmp = $aFile[$sIndex]['tmp_name'];
		if($sFileTmp === '.htaccess' || strpos($sFileTmp, '.php')) {
			return false;
		}
		$sFilename = 'msg_'.uniqid().'_'.$aFile[$sIndex]['name'];
		if (!empty($sFileTmp)) { 
			$sFileErrorMsg = $aFile[$sIndex]['error'];
			if(!move_uploaded_file($sFileTmp, $sUploadPath.$sFilename)){
				UserRequest::$oAlertBoxMgr->danger = $sFileErrorMsg;
				return false;
			} else {
				chmod($sUploadPath.$sFilename, 0604);
				return $sFilename;
			}
		}
		return false;
	}
	
	public static function downloadFile($sFilename, $sFilePath) {
		$sFilePath = $sFilePath.$sFilename;
		if (!file_exists($sFilePath)) {
			UserRequest::setRequest(array('sPage'=>'404', 'sLang'=>DEFAULT_LANG));
			return true;
		}
		ini_set('zlib.output_compression', 0);
		//factory
		switch(strtolower(pathinfo($sFilePath, PATHINFO_EXTENSION))) {
			case 'txt':
			case 'md':
			case 'sql':
				header("Content-Type: text/plain");
				break;
			case 'xml':
				header("Content-type: text/xml");
				break;
			case 'html':
			case 'hml':
				header("Content-type: text/html");
				break;
			case 'json':
				header("Content-type: application/json");
				break;
			case 'pdf':
				header("Content-Type: application/pdf");
				break;
			case 'jpg':
			case 'jpeg':
				header("Content-Type: image/jpg");
				break;
			case 'png':
				header("Content-Type: image/png");
				break;
			case 'gif':
				header("Content-Type: image/gif");
				break;
			case 'zip':
				header("Content-Type: application/zip");
				break;
			case 'rar':
				header("Content-Type: application/rar");
				break;
			case 'tar':
				header("Content-Type: application/tar");
				break;
			case 'gz':
			case 'tgz':
			case 'gz2':
				header("Content-Type: application/tar+gzip");
				break;
			default:
				header("Content-Type: application/octet-stream");
		}
		//fire
		header('Pragma: public');
		header("Expires: 0"); // obligé
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // obligé
		header("Content-Type: image/jpg");
		header('Content-Type: application/octetstream; name="'.$sFilename.'"');
		header("Content-Disposition: attachment; filename=\"".$sFilename."\";" );
		header('Content-MD5: '.base64_encode(md5_file($sFilePath)));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($sFilePath));
		header('Date: '.gmdate(DATE_RFC1123));
		header('Expires: '.gmdate(DATE_RFC1123, time()+1));
		header('Last-Modified: '.gmdate(DATE_RFC1123, filemtime($sFilePath)));
		readfile($sFilePath);
		die();
	}
}