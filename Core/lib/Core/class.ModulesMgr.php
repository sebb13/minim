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
final class ModulesMgr {
	
	private static $aModules		= array();
	const TPL_CONTENTS_PATH			= 'frontContentsTpl';
	const TPL_PARTS_PATH			= 'frontPartsTpl';
	const ADMIN_TPL_CONTENTS_PATH	= 'backContentsTpl';
	const ADMIN_TPL_PARTS_PATH		= 'backPartsTpl';
	const LOCALES_PATH				= 'locales';
	const ADMIN_LOCALES_PATH		= 'backLocales';
	const DATA_PATH					= 'data';
	const CSS_PATH					= 'css';
	const JS_PATH					= 'js';
	
	public static function getModulesAvailable() {
		if(empty(self::$aModules)) {
			$sCachePath = CORE_CACHE_PATH.'modulesAvailable.list';
			if(strtotime('-1 week') > filemtime($sCachePath)) {
				self::setModulesAvailable();
			}
			self::$aModules = array_map(
							'trim', 
							file($sCachePath)
						);
		}
		return self::$aModules;
	}
	
	public static function isMinim($sModuleName) {
		return $sModuleName === 'minim';
	}
	
	public static function getVersion($sModuleName) {
		if(self::isMinim($sModuleName)) {
			return trim(file_get_contents(DATA_PATH.$sModuleName.'.version'));
		}
		if(!is_dir(MODULES_PATH.$sModuleName)) {
			return false;
			throw new CoreException($sModuleName.' is not a valid module');
		}
		return trim(file_get_contents(self::getFilePath($sModuleName, 'data').$sModuleName.'.version'));
	}
	
	public static function setVersion($sModuleName, $sVersion) {
		if(self::isMinim($sModuleName)) {
			file_put_contents(DATA_PATH.$sModuleName.'.version', $sVersion);
		} else {
			if(!is_dir(MODULES_PATH.$sModuleName)) {
				throw new CoreException($sModuleName.' is not a valid module');
			}
			file_put_contents(
							self::getFilePath($sModuleName, 'data').$sModuleName.'.version', 
							$sVersion
						);
		}
		return $sModuleName.'@version:'.$sVersion;
	}
	
	public static function getPreviousVersion($sModuleName) {
		if(self::isMinim($sModuleName)) {
			$sDataPath = DATA_PATH;
		} else {
			$sDataPath = self::getFilePath($sModuleName, 'data');
		}
		if(!file_exists($sDataPath)) {
			return false;
		}
		foreach(scandir($sDataPath) as $sFilename) {
			if(strpos($sFilename, $sModuleName.'.conf.xml-') === 0) {
				$aVersion = explode('-', $sFilename);
				return $aVersion[count($aVersion)-1];
			}
		}
		return false;
	}
	
	public static function setModulesAvailable() {
		self::$aModules = array('minim');
		foreach(scandir(MODULES_PATH) as $sModuleName) {
			if (is_dir(MODULES_PATH.$sModuleName) && !in_array($sModuleName, Toolz_Main::$aScandirIgnore)) {
				self::$aModules[] = trim($sModuleName);
			}
		}
		/*
		 passer par un xml !!!
		 */
		return file_put_contents(
							CORE_CACHE_PATH.'modulesAvailable.list', 
							implode("\n", self::$aModules)
						);
	}
	
	public static function getFilePath($sModuleName, $sType, $sLang='') {
		if(empty($sLang)) {
			$sLang = DEFAULT_LANG;
		}
		if(self::isMinim($sModuleName)) {
			if(in_array($sType, array('css', 'js', 'fonts', 'img'))) {
				$sFilePath = ROOT_PATH;
			} else {
				$sFilePath = CORE_PATH;
			}
		} else {
			$sFilePath = MODULES_PATH.$sModuleName.'/';
		}
		switch($sType) {
			case 'frontContentsTpl':
				$sFilePath .= GEN_TPL_CONTENTS_PATH;
				break;
			case 'frontPartsTpl':
				$sFilePath .= GEN_TPL_PARTS_PATH;
				break;
			case 'backContentsTpl':
				$sFilePath .= GEN_ADMIN_TPL_CONTENTS_PATH;
				break;
			case 'backPartsTpl':
				$sFilePath .= GEN_ADMIN_TPL_PARTS_PATH;
				break;
			case 'locales':
				$sFilePath .= GEN_LOC_PATH.$sLang.'/';
				break;
			case 'backLocales':
				$sFilePath .= GEN_ADMIN_LOC_PATH.$sLang.'/';
				break;
			case 'data':
				$sFilePath .= GEN_DATA_PATH;
				break;
			case 'css':
				$sFilePath .= GEN_CSS_PATH;
				break;
			case 'js':
				$sFilePath .= GEN_JS_PATH;
				break;
			default: 
				throw new CoreException('unknow type '.$sType);
		}
		return $sFilePath;
	}
	
	public static function install($sModuleConf) {
		
	}
	
	public static function remove($sModuleConf) {
		
	}
	
	public static function getMenu() {
		
	}
	
	public static function hasModule($sModuleName) {
		
	}
}