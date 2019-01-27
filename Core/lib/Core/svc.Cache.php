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
final class Cache extends CoreCommon implements iCacheSystem {
	
	private $sTplPartName	= 'resetCache.item.tpl';
	private $sTplLangName	= 'resetCache.langContainer.tpl';
	private $sTplTypeName	= 'resetCache.typeContainer.tpl';
	private $aData			= array();
	
	public function __construct() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		parent::__construct();
	}
	
	public function resetCache() {
		try {
			UserRequest::startBenchmark('resetCache');
			$oConfig = new Config();
			$oConfig->setGlobalConf('SYS_LAST_CACHE', date('Ymd - H:i:s'));
			unset($oConfig);
			$this->oLang->LOC_PATH = LOC_PATH;
			$this->aData['MODULES'] = $this->resetModuleCache();
			$oCacheMgr = new CacheMgrFront($this->oLang);
			$this->aData['FRONT'] = $oCacheMgr->resetCache();
			$this->oLang->LOC_PATH = ADMIN_LOC_PATH;
			$this->aData['SITE_MAP'] = $oCacheMgr->buildSiteMap();
			$oCacheMgr = new CacheMgrBack($this->oLang);
			$this->aData['ADMIN'] = $oCacheMgr->resetCache();
			$this->oLang->LOC_PATH = DRAFTS_LOC_PATH;
			$oCacheMgr = new CacheMgrPreview($this->oLang, TranslationsMgr::TRANS_BACK);
			$this->aData['DRAFTS'] = $oCacheMgr->resetCache();
			$oCacheMgr = new CacheMgrPreview($this->oLang, TranslationsMgr::TRANS_FRONT);
			$this->aData['DRAFTS'] += $oCacheMgr->resetCache();
			unset($oCacheMgr);
		}  catch (GenericException $e) {
			echo $e->getMessage();
			print_r($e->getTrace());
			$this->aData['ERROR'] = 'build cache error';
			return $this->getView();
		}
		return $this->getView();
	}
	
	public function resetModuleCache($sModuleToReset='all') {
		$aDataTmp = array();
		ModulesMgr::setModulesAvailable();
		$aModules = ModulesMgr::getModulesAvailable();
		if (empty($aModules)) {
			$aDataTmp['no module found'] = 'no module found';
		} elseif(isset($aModules[$sModuleToReset])) {
			$sClassName = $sModuleToReset.'Mgr';
			if(class_exists($sClassName) && method_exists($sClassName, 'resetCache')) {
				$aDataTmp[$sModuleToReset] = $sClassName::resetCache($this->oLang);
				$oConf = new Config($sModuleToReset);
				$oConf->setGlobalConf('SYS_LAST_CACHE', date('Ymd - H:i:s')); //20170313 - 11:04:28
			}
		} else {
			foreach($aModules as $sModuleName) {
				$sClassName = trim($sModuleName).'Mgr';
				try {
					if(class_exists($sClassName) && method_exists($sClassName, 'resetCache')) {
						$oClass = new $sClassName();
						$aDataTmp[$sModuleName] = $oClass->resetCache($this->oLang);
						$oConf = new Config($sModuleName);
						$oConf->setGlobalConf('SYS_LAST_CACHE', date('Ymd - H:i:s')); //20170313 - 11:04:28
					}
				} catch(CoreException $e) {}
			}
		}
		return $aDataTmp;
	}
	
	private function getView() {
		$this->oLang->LOC_PATH = ADMIN_LOC_PATH;
		$this->oLang->LOCALE = SessionLang::getLang();
		//get templates
		$sItemTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sTplPartName);
		$sLangTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sTplLangName);
		$sTypeTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sTplTypeName);
		$sContent = '';
		foreach($this->aData as $sCacheType=>$aCacheData) {
			$sTypeContent = '';
			if (is_array($aCacheData)) {
				foreach($aCacheData as $sLang=>$aElmts) {
					$sLangContent = '';
					if (is_array($aElmts)) {
						foreach($aElmts as $sElmtName) {
							$sLangContent .= str_replace('{__NAME__}', $sElmtName, $sItemTpl);
						}
						$sTypeContent .= str_replace(
												array('{__LANG__}','{__CONTENTS__}'), 
												array($sLang,$sLangContent),
												$sLangTpl
											);
					}
				}
			} else {
				$sContent.= '';
			}
			$sContent .= str_replace(
									array('{__CACHE_NAME__}','{__CONTENTS__}'), 
									array($this->oLang->getMsg('system_resetCache', $sCacheType),$sTypeContent),
									$sTypeTpl
								);
			$sContent .= str_replace(
									array('{__LANG__}','{__CONTENTS__}'), 
									array($this->oLang->getMsg('system_resetCache', 'DONE'),''),
									$sLangTpl
								);
		}
		$sContent .= UserRequest::stopBenchmark('resetCache', true);
		return $this->getCoreResult($sContent);
	}
}