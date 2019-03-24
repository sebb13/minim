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
		$aFile = UserRequest::getFiles();
		$sFileTmp = $aFile[$sIndex]['tmp_name'];
		if (!empty($sFileTmp)) { 
			$sFileErrorMsg = $aFile[$sIndex]['error'];
			if(!move_uploaded_file($sFileTmp, $sUploadPath.$aFile[$sIndex]['name'])){
				UserRequest::$oAlertBoxMgr->danger = $sFileErrorMsg;
				return false;
			} else {
				chmod($sUploadPath.$aFile[$sIndex]['name'], 0604);
				return $aFile[$sIndex]['name'];
			}
		}
		return false;
	}
} 