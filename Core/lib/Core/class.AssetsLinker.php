<?php
/*
 *	PHP framework
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
final class AssetsLinker extends SimpleXmlMgr {
	
	private $sCssXmlPath		= 'css.xml';
	private $sJsXmlPath			= 'js.xml';
	private $sFrontCssCachePath = 'css.front.html';
	private $sBackCssCachePath	= 'css.back.html';
	private $sFrontJsCachePath	= 'js.front.html';
	private $sBackJsCachePath	= 'js.back.html';
	private $sCssCachePath		= '';
	private $sJsCachePath		= '';
	
	public function __construct() {
		parent::__construct();
		// DATA PATH
		$this->sCssXmlPath = DATA_PATH.$this->sCssXmlPath;
		$this->sJsXmlPath = DATA_PATH.$this->sJsXmlPath;
		// FRONT CACHE PATH
		$this->sFrontCssCachePath = CACHE_PATH.$this->sFrontCssCachePath;
		$this->sFrontJsCachePath = CACHE_PATH.$this->sFrontJsCachePath;
		// ADMIN CACHE PATH
		$this->sBackCssCachePath = CACHE_PATH.$this->sBackCssCachePath;
		$this->sBackJsCachePath = CACHE_PATH.$this->sBackJsCachePath;
		// CURRENT CACHE PATH
		$this->sCssCachePath = ADMIN ? $this->sBackCssCachePath : $this->sFrontCssCachePath;
		$this->sJsCachePath = ADMIN ? $this->sBackJsCachePath : $this->sFrontJsCachePath;
	}
	
	public function getCSS() {
		if(!file_exists($this->sCssCachePath) && !$this->buildCSS()) {
			throw new CoreException('CSS error');
		}
		return file_get_contents($this->sCssCachePath);
	}
	
	public function getJS() {
		if(!file_exists($this->sJsCachePath) && !$this->buildJS()) {
			throw new CoreException('JS error');
		}
		return file_get_contents($this->sJsCachePath);
	}
	
	public function buildCSS() {
		$aRoles = $this->getConf($this->sCssXmlPath);
		$aRequired = $aRoles['required'];
		$aFront = $aRoles['front'];
		$aBack = $aRoles['back'];
		if(!file_put_contents(
						$this->sFrontCssCachePath, 
						$this->getLinks('css', $aRequired, $aFront)
					)) return false;
		if(!file_put_contents(
						$this->sBackCssCachePath, 
						$this->getLinks('css', $aRequired, $aBack, true)
					)) return false;
		unset($aRequired, $aFront, $aBack);
		return true;
	}
	
	public function buildJS() {
		$aRoles = $this->getConf($this->sJsXmlPath);
		$aRequired = $aRoles['required'];
		$aFront = $aRoles['front'];
		$aBack = $aRoles['back'];
		if(!file_put_contents(
						$this->sFrontJsCachePath, 
						$this->getLinks('js', $aRequired, $aFront)
					)) return false;
		if(!file_put_contents(
						$this->sBackJsCachePath, 
						$this->getLinks('js', $aRequired, $aBack, true)
					)) return false;
		unset($aRequired, $aFront, $aBack);
		return true;
	}
	
	private function getConf($sXmlPath) {
		if(!$this->loadFile($sXmlPath)) {
			throw new CoreException('invalid asserts configuration');
		}
		$aTmp = $this->getIemsList();
		$aRoles = $this->rolesSort($aTmp['minim']['file']);
		unset($aTmp['minim']);
		$aRequired = $aRoles['required'];
		$aFront = $aRoles['front'];
		$aBack = $aRoles['back'];
		foreach($aTmp as $sModuleName=>$aValues) {
			$aRoles = $this->rolesSort($aValues['file']);
			$aRequired += $aRoles['required'];
			$aFront += $aRoles['front'];
			$aBack += $aRoles['back'];
		}
		return array('required'=>$aRequired,'front'=>$aFront,'back'=>$aBack);
	}
	
	private function rolesSort(array $aLinks) {
		$aRoles = array(
					'required'=>array(),
					'front'=>array(),
					'back'=>array()
				);
		foreach($aLinks as $aLink) {
			Toolz_Checker::checkParams(array(
									'required'	=> array('url', 'level', 'role'),
									'data'	=> $aLink
								));
			if(strpos($aLink['url'], '{__STATIC_SERVER_URL__}') === 0) {
				$aLink['url'] = str_replace(
					'{__STATIC_SERVER_URL__}', 
					DEV ? STATIC_DEV_SERVER_URL : STATIC_SERVER_URL, 
					$aLink['url']
				);
			}
			if($aLink['role'] === 'required') {
				$aRoles['required'][$aLink['level']] = $aLink;
			} elseif($aLink['role'] === 'front') {
				$aRoles['front'][$aLink['level']] = $aLink;
			} elseif($aLink['role'] === 'back') {
				$aRoles['back'][$aLink['level']] = $aLink;
			} else {
				throw new CoreException('unknown role "'.$aLink['role'].'"');
			}
		}
		return $aRoles;
	} 
	
	private function getLinks($sType, array $aRequired, array $aOther, $bAdmin=false) {
		ksort($aRequired);
		ksort($aOther);
		$aLinks = array();
		foreach($aRequired as $aLink) {
			$aLinks[] = $this->getHtmlLink($aLink, $sType, $bAdmin);
		}
		foreach($aOther as $aLink) {
			$aLinks[] = $this->getHtmlLink($aLink, $sType, $bAdmin);
		}
		return implode('', $aLinks);
	}
	
	private function getHtmlLink(array $aLink, $sStype, $bAdmin=false) {
		if (!empty($aLink['prefixUrl'])) {
			if($bAdmin) {
				$aLink['url'] = (DEV ? ADMIN_URL_DEV : ADMIN_URL_PROD).$aLink['url'];
			} else {
				$aLink['url'] = (DEV ? SITE_URL_DEV : SITE_URL_PROD).$aLink['url'];
			}
		}
		if(!empty($aLink['needQueryString'])) {
			$aLink['url'] .= '?'.ModulesMgr::getVersion('minim');
		}
		$sLink = str_replace('{__URL__}', $aLink['url'], file_get_contents(INC_TPL_PATH.$sStype.'.link.tpl'));
		if(!empty($aLink['wrapper_pattern'])) {
			$sLink = str_replace(
								'{__LINK__}', 
								$sLink, 
								$aLink['wrapper_pattern']
							);
		}
		$aMisc = array();
		if(!empty($aLink['integrity'])) {
			$aMisc[] = 'integrity="'.$aLink['integrity'].'"';
		}
		if(!empty($aLink['crossorigin'])) {
			$aMisc[] = 'crossorigin="'.$aLink['crossorigin'].'"';
		}
		$sMisc = implode(' ', $aMisc);
		return str_replace('{__MISC__}', $sMisc, $sLink);
	}
}