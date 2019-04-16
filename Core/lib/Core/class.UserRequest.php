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
final class UserRequest {
	
	private static $aRequest		= array();
	private static $aParams			= array();
	private static $aEnv			= array();
	private static $aFiles			= array();
	private static $aCookies		= array();
	private static $iTimeStart		= 0;
	private static $iTimeStop		= 0;
	private static $aTimes			= array();
	private static $sBenchmarkTpl	= '';
	public static $oAlertBoxMgr		= NULL;
	
	public static function init(array $aRequest, $mParams=array(), $mFiles=array(), $aCookies=array(), $aServer=array()) {
		self::$aRequest = $aRequest;
		self::$aCookies = $aCookies;
		self::$aEnv = $aServer;
		if(is_array($mParams)) {
			self::$aParams = $mParams;
		} elseif(is_string($mParams)) {
			parse_str($mParams, self::$aParams);
		} else {
			self::$aParams = (array)$mParams;
		}
		self::$aFiles = $mFiles;
		self::$oAlertBoxMgr = new AlertBoxMgr();
		return true;
	}
	
	public static function setRequest($aNewRequest) {
		Toolz_Checker::checkParams(array(
									'required'	=> array('sPage', 'sLang'),
									'data'	=> $aNewRequest
								));
		self::$aRequest = $aNewRequest;
		SessionNav::setCurrentPage($aNewRequest['sPage']);
		SessionLang::setLang($aNewRequest['sLang']);
		return true;
	}
	
	public static function getRequest($sKey='') {
		if(!empty($sKey)) {
			return akead($sKey, self::$aRequest, false);
		}
		return !empty(self::$aRequest) ? self::$aRequest : false;
	}
	
	public static function setParams($sKey, $mValue) {
		self::$aParams[$sKey] = $mValue;
		return true;
	}
	
	public static function setAllParams(array $aParams) {
		self::$aParams = $aParams;
		return true;
	}
	
	public static function getParams($sKey='') {
		if(!empty($sKey)) {
			return akead($sKey, self::$aParams, false);
		}
		return !empty(self::$aParams) ? self::$aParams : false;
	}
	
	public static function setEnv($sKey, $mValue) {
		self::$aEnv[$sKey] = $mValue;
		return true;
	}
	
	public static function getEnv($sKey='') {
		if(!empty($sKey)) {
			// -- IF IN $_SERVER
			if (!empty(self::$aEnv[$sKey])) {
				return self::$aEnv[$sKey];
			// -- ELSE
			} elseif (getenv($sKey) !== false) {
				return getenv($sKey);
			// -- IF APACHE
			} elseif (function_exists('apache_getenv') && apache_getenv($sKey) !== false) {
				return apache_getenv($sKey);
			} else {
				return false;
			}
		}
		return !empty(self::$aEnv) ? self::$aEnv : getenv();
	}
	
	public static function setCookie($sKey, $sValue, $sTTL) {
		return setcookie($sKey, $sValue, $sTTL);
	}
	
	public static function getCookie($sKey) {
		return isset(self::$aCookies[$sKey]) ? self::$aCookies[$sKey] : false;
	}
	
	public static function getFiles($sKey='') {
		if(!empty($sKey)) {
			return akead($sKey, self::$aFiles, false);
		}
		return !empty(self::$aFiles) ? self::$aFiles : false;
	}
	
	public static function getLang() {
		return self::$aRequest['sLang'];
	}
	
	public static function getPage() {
		return self::$aRequest['sPage'];
	}
	
	public static function getUrl($sPage='', $sLang='') {
		if (empty($sPage)) {
			$sPage = self::getPage();
		}
		if (empty($sLang)) {
			$sLang = self::getLang();
		}
		return WEB_PATH.$sLang.'/'.$sPage;
	}
	
	///////////////////////////////BENCHMARK TOOLS//////////////////////////////////
	public static function startBenchmark($sIndex='') {
		if(empty($sIndex)) {
			self::$iTimeStart = microtime(true);
		} else {
			self::$aTimes[$sIndex] = array('start'=> microtime(true));
		}
		return true;
	}
	
	public static function stopBenchmark($sIndex='', $bInTpl=false) {
		if(empty($sIndex)) {
			self::$iTimeStop = microtime(true);
		} else {
			if(isset(self::$aTimes[$sIndex])) {
				self::$aTimes[$sIndex]['stop'] = microtime(true);
			} else {
				throw new CoreException('Benchmark not started', Core::INTERNAL_ERROR);
			}
		}
		return self::getBenchmark($sIndex, $bInTpl);
	}
	
	public static function getBenchmark($sIndex='', $bInTpl=false) {
		if(empty($sIndex)) {
			$fBenchmark = round(self::$iTimeStop - self::$iTimeStart, 5);
		} else {
			$fBenchmark = round(self::$aTimes[$sIndex]['stop'] - self::$aTimes[$sIndex]['start'], 5);
		}
		return $bInTpl ? self::getBenchmarkTpl($fBenchmark) : $fBenchmark;
	}
	
	private static function getBenchmarkTpl($sBenchmarkValue) {
		if(empty(self::$sBenchmarkTpl)) {
			self::$sBenchmarkTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'benchmark.tpl');
		}
		return str_replace('{__BENCHMARK_VALUE__}', $sBenchmarkValue, self::$sBenchmarkTpl);
	}
	////////////////////////////END BENCHMARK TOOLS//////////////////////////////////
}