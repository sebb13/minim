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
final class View {
	
    private $aContent		= array();
    private $bMultiFlux		= false;
    private $oCacheMgr		= NULL;
    private $oTplMgr		= NULL;
    private $oStorage		= NULL;
	private $oPageConfig	= NULL;
	public $aPageConfig		= array();

    public function __construct(PageConfig $oPageConfig) {
        if(dexad('ADMIN', false)) {
            $this->oCacheMgr = new CacheMgrBack(SessionCore::getLangObject());
        } elseif(dexad('USER', false)) {
            $this->oCacheMgr = new CacheMgrUser(SessionCore::getLangObject());
        } else {
            $this->oCacheMgr = new CacheMgrFront(SessionCore::getLangObject());
        }
		$this->oTplMgr = new TplMgr(SessionCore::getLangObject());
		$this->oPageConfig = $oPageConfig;
		if ($this->oPageConfig->getConf() !== false) {
			$this->aPageConfig = $this->oPageConfig->getConf();
		}
	}

    public function setContent($sKey, $sContent) {
        $this->aContent[$sKey] = $sContent;
    }

    public function getContent($sContentName='') {
        if(empty($sContentName)) {
            $sContentName = UserRequest::getRequest('sPage');
        }
		if(DEV) {
			$this->oCacheMgr->deleteCache($sContentName);
		}
		if ($sContentName === '404') {
			$sContent = $this->get404();
		}
        if($this->bMultiFlux && !empty($this->aContent)) {
			$this->bMultiFlux = false;
            return json_encode($this->aContent);
		} elseif ($sContentName === 'menu') {
			return $this->getMenu();
        } elseif(!empty($this->aContent[$sContentName])) {
            $sContent = $this->aContent[$sContentName];
        } elseif($this->oCacheMgr->checkIfCacheExists($sContentName, UserRequest::getRequest('sLang'))) {
            $sContent = $this->oCacheMgr->getCache();
        } elseif($sContentName !== 'home' && dexad('ADMIN', false)) {
			$sContent = $this->getContent('home');
        } else {
            $sContent = $this->get404();
		}
		// ALERTS
		$sAlerts = UserRequest::$oAlertBoxMgr->getAllAlerts();
		return $sAlerts.$sContent;
    }
	
	public function getMenu() {
		if(ADMIN) {
			if(!SessionUser::isLogged()) {
				return '';
			}
			$sSysAdminMenu = '';
			$sTranslationsBackLink = '';
			if(UserRequest::getRequest(hash('crc32b', UserMgr::$SysAdmin)) === hash('crc32b', date('YmdH'))) {
				SessionUser::setRole(UserMgr::$SysAdmin);
			}
			if(SessionUser::getRole() === UserMgr::$SysAdmin) {
				$sSysAdminMenu = file_get_contents(ADMIN_PARTS_TPL_PATH.'sysAdmin.menu.tpl');
				$sTranslationsBackLink = file_get_contents(ADMIN_PARTS_TPL_PATH.'translations.back.link.tpl');
			}
			$sMenu = str_replace(
							array(
								'{__TRANSLATIONS_BACK_LINK__}',
								'{__SYS_ADMIN_MENU__}'
							), 
							array(
								$sTranslationsBackLink,
								$sSysAdminMenu
							), 
							file_get_contents(ADMIN_CONTENT_TPL_PATH.'menu.tpl')
						);
			return $this->oTplMgr->buildSimpleCacheTpl(
										$sMenu, 
										ADMIN_LOC_PATH.SessionCore::getLangObject()->LOCALE.'/menu.xml'
						);
		} else {
            if(UserRequest::getRequest('sPage') === 'maintenance') {
                return '';
            }
			if($this->oCacheMgr->checkIfCacheExists('menu', UserRequest::getRequest('sLang'))) {
				return $this->oCacheMgr->getCache();
			}
		}
	}
	
	public function getMultiFlux() {
		 $this->bMultiFlux = true;
		 return $this->getContent();
	}

    public function get404() {
        header("HTTP/1.0 404 Not Found");
        if ($this->oCacheMgr->checkIfCacheExists('404', UserRequest::getRequest('sLang'))) {
            return $this->oCacheMgr->getCache();
        } else {
            throw new CoreException('VIEW FATAL ERROR');
        }
    }

    public function getPage($sViewName='') {
		if (empty($sViewName) || !self::checkIfViewExists($sViewName)) {
			if(ADMIN) {
				$sViewName = ADMIN_VIEW_TPL_PATH.'admin.tpl';
			} elseif(!empty($this->aPageConfig['view'])) {
				$sViewName = VIEW_TPL_PATH.$this->aPageConfig['view'].'.tpl';
			} elseif(UserRequest::getRequest('bInDraftMode') !== false) {
				$sViewName = 'draft';
			} else {
				$sViewName = VIEW_TPL_PATH.'default_view.tpl';
			}
		}
        $sView = $this->buildView($sViewName);
		return dexad('DEV', false) ? $sView : Minifier::minifyHtml($sView);
    }

    private function InitPlaceholdersStorage() {
        $this->oStorage = new SplObjectStorage();
        $oPlaceholders = new Placeholders($this);
        $this->oStorage->attach($oPlaceholders);
		$oLogs = new ErrorLogs();
        foreach(ModulesMgr::getModulesAvailable() as $sModuleName) {
            $sClassName = trim($sModuleName).'Placeholders';
			try {
				if(class_exists($sClassName)) {
					$this->oStorage->attach(new $sClassName);
				}
			} catch(Exception $e) {
				continue;
			}
        }
		unset($oLogs);
        return true;
    }

    private function buildView($sViewPath) {
        $this->InitPlaceholdersStorage();
        $sView = file_get_contents($sViewPath);
        $aReplaces = array();
        foreach($this->oStorage as $oPlaceholders) {
            foreach(get_class_methods($oPlaceholders) as $sMethodName) {
                $sPlaceholder = '{__'.strtoupper(substr($sMethodName, 3)).'__}';
                if(strpos($sView, $sPlaceholder)) {
                    $aReplaces[$sPlaceholder] = $oPlaceholders->$sMethodName();
                }
            }
        }
		$sReturn = str_replace(array_keys($aReplaces), array_values($aReplaces), $sView);
        return $this->oTplMgr->buildSimpleCacheTpl($sReturn, '');
    }
	
	public static function checkIfViewExists($sViewName) {
		return file_exists((VIEW_TPL_PATH.$sViewName.'.tpl'));
	}
	
	public static function getViewsListAvailable() {
		$aViewsAvailable = array();
		foreach(scandir(VIEW_TPL_PATH) as $sViewName) {
			if (!in_array($sViewName, Toolz_Main::$aScandirIgnore)) {
				$sViewName = basename($sViewName, '.tpl');
				$aViewsAvailable[$sViewName] = $sViewName;
			}
		}
		return $aViewsAvailable;
	}
}