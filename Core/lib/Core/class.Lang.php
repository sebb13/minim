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
class Lang {

	private $aMessages			= array();
	private $aDefaultMessages	= array();
	private $sLang				= '';
	private $sDefaultLang		= '';
	private $sRoot				= '';
	public $sCommonFilename	= 'common.xml';
	public $aAdminLangAvailable = array('FR'=>'FR','GB'=>'GB','DE'=>'DE','ES'=>'ES','IT'=>'IT');
	public $aCommonMessages		= array();
	public static $aDirNotLang	= array('.','..','admin','modules','common');

	public function __construct($sLoc=DEFAULT_LANG, $sDefaultLoc=DEFAULT_LANG, $sRoot=LOC_PATH) {
		$this->sRoot = $sRoot;
		if (!is_dir($this->sRoot.$sDefaultLoc)) {
			throw new GenericException(str_replace('__LOC__', $sDefaultLoc, LangException::DEFAULT_LOCALE_LANG_NOT_FOUND));
		}
		$this->sLang = $sLoc;
		$this->sDefaultLang = $sDefaultLoc;
		// COMMON
		return $this->setCommonMessages();
	}
	
	private function setCommonMessages() {
		$sCommonFilePath = COMMON_LOC_PATH.$this->sLang.'/'.$this->sCommonFilename;
		if(!file_exists($sCommonFilePath)) {
			$sCommonFilePath = COMMON_LOC_PATH.DEFAULT_LANG.'/'.$this->sCommonFilename;
		}
		$oXml = simplexml_load_file($sCommonFilePath);
		foreach ($oXml as $sKey => $sVal) {
			$this->aCommonMessages[$sKey] = (string)$sVal;
		}
		unset($oXml);
		return true;
	}

	private function setaMessages ($sCat) {
		if (!file_exists($this->sRoot.$this->sLang.'/'.$sCat.'.xml')) {
			$this->setaDefaultMessages($sCat);
			return true;
		}
		$oXml = simplexml_load_file($this->sRoot.$this->sLang.'/'.$sCat.'.xml');
		foreach ($oXml as $sKey => $sVal) {
			if((string)$sVal === '__EMPTY__')  {
				$this->aMessages[$sCat][$sKey] = '';
			} elseif((string)$sVal === '') {
				$this->aMessages[$sCat][$sKey] = '__NOT__';
			} else {
				$this->aMessages[$sCat][$sKey] = $sVal;
			}
		}
		unset($oXml);
		return true;
	}

	private function setaDefaultMessages ($sCat) {
		if (!file_exists($this->sRoot.$this -> sDefaultLang.'/'.$sCat.'.xml')) {
			throw new GenericException(str_replace('__MOD__', $sCat, LangException::DEFAULT_LOCALE_MOD_NOT_FOUND));
		}
		$oXml = simplexml_load_file($this->sRoot.$this->sDefaultLang.'/'.$sCat.'.xml');
		foreach ($oXml as $sKey => $sVal) {
			if((string)$sVal === '__EMPTY__')  {
				$this->aDefaultMessages[$sCat][$sKey] = '';
			} else {
				$this->aDefaultMessages[$sCat][$sKey] = $sVal;
			}
		}
		unset($oXml);
		return true;
	}

	public function getMsg ($sCat, $sKey) {
		if (!empty($this->aCommonMessages[$sKey])) {
			return str_replace('"','&#34;',(string)$this->aCommonMessages[$sKey]);
		}
		if (!isset($this->aMessages[$sCat]) && !isset($this->aDefaultMessages[$sCat])) {
			$this->setaMessages($sCat);
		}
		if (!isset($this->aMessages[$sCat][$sKey]) || $this->aMessages[$sCat][$sKey] === '__NOT__') {
			if (!isset($this->aDefaultMessages[$sCat])) {
				$this->setaDefaultMessages($sCat);
				if (!isset($this->aDefaultMessages[$sCat][$sKey])) {
					if(dexad('DEV', false)) {
						return $this->warnMissingConstantForDev($sKey);
					} else {
						throw new GenericException(str_replace('__TRANS__', $sKey, LangException::DEFAULT_LOCALE_TRANS_NOT_FOUND));
					}
				}
			}
			return str_replace('"','&#34;',(string)$this->aDefaultMessages[$sCat][$sKey]);
		}
		return str_replace('"','&#34;',(string)$this->aMessages[$sCat][$sKey]);
	}
	
	private function warnMissingConstantForDev($sKey) {
		echo $sKey;
		if(!file_exists($this->sRoot.$this->sDefaultLang.'/missing.xml')) {
			$oDom = new DOMDocument('1.0', 'utf-8');
			$oRootNode = $oDom->createElement('missing');
			$oRoot = $oDom->appendChild($oRootNode);
		} else {
			$oDom = new DOMDocument;
			$oDom->load($this->sRoot.$this->sDefaultLang.'/missing.xml');
			$oRoot = $oDom->firstChild;
		}
		$oKeyNode = $oDom->createElement($sKey);
		$cdata = $oDom->createCDATASection('__XXXXXX__');
		$oKeyNode->appendChild($cdata);
		$oRoot->appendChild($oKeyNode);
		$oDom->save($this->sRoot.$this->sDefaultLang.'/missing.xml');
		return '__XXXXXX__';
	}
	
	public function getFrontAvailable() {
		$aLang = array();
		foreach(scandir(LOC_PATH) as $sLang) {
			if(!in_array($sLang, self::$aDirNotLang) && is_dir(LOC_PATH.$sLang)) {
				$aLang[trim($sLang)] = trim($sLang);
			}
		}
		if(empty($aLang)) {
			$aLang[DEFAULT_LANG] = DEFAULT_LANG;
		}
		return $aLang;
	}

	public function getAvailable() {
		if(ADMIN) {
			return $this->aAdminLangAvailable;
		} else {
			return $this->getFrontAvailable();
		}
		
	}

	public function __set($sProp, $mVal) {
		switch($sProp) {
			case 'LOCALE':
				$this->sLang = $mVal;
				$this->aMessages = null;
				break;
			case 'DEFAULT':
				$this->sDefaultLang = $mVal;
				$this->aDefaultMessages = null;
				break;
			case 'LOC_PATH':
				$this->sRoot = $mVal;
				break;
			default:
				throw new GenericException(str_replace('__PROP__', $sProp, LangException::PROP_NOT_SETTABLE));
		}
	}

	public function __get($sProp) {
		switch($sProp) {
			case 'LOC_PATH':
				return $this->sRoot;
				break;
			case 'LOCALE':
				return $this->sLang;
				break;
			case 'DEFAULT':
				return $this->sDefaultLang;
				break;
			case 'AVAILABLE' :
				return $this->getAvailable();
				break;
			default:
				throw new GenericException(str_replace('__PROP__', $sProp, LangException::PROP_NOT_GETTABLE));
		}
	}
	
	public function formatToTranslate($sMsg) {
		return '{__'.$sMsg.'__}';
	}
}