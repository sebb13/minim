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
abstract class CacheMgr extends CoreCommon {

	const DEFAULT_CACHE_EXT			= '.html';
	private $aDebug					= array();
    protected $sCachePath			= '';
    protected $sLocPath				= '';
    protected $sTplPath				= '';
    protected $sCache				= '';
    protected $sDraftLocPath		= '';
    protected $sDraftTplPath		= '';
    protected $sDraftCachePath		= '';
    protected $sVersionLocPath		= '';
    protected $sVersionTplPath		= '';
    protected $sVersionCachePath	= '';
    protected $oTplMgr				= NULL;
	/*a virer et metre dans une conf*/
	protected $aNoCache	= array(
							'index.php',
							'resetCache',
							'minim',
							'coreResult'
						);

    public function __construct() {
		parent::__construct();
		$this->sDraftCachePath		= DRAFTS_CACHE_PATH;
		$this->sDraftLocPath		= DRAFTS_LOC_PATH;
		$this->sDraftTplPath		= DRAFTS_TPL_PATH;
		$this->sVersionCachePath	= BACKUP_CACHE_PATH;
		$this->sVersionLocPath		= BACKUP_LOC_PATH;
		$this->sVersionTplPath		= BACKUP_TPL_PATH;
        $this->oTplMgr = new TplMgr($this->oLang, $this->sLocPath, $this->sTplPath);
    }

    public static function getUniqId() {
		$oConfig = new Config();
		$sUniqId = $oConfig->getGlobalConf('SYS_UNIQ_ID');
		unset($oConfig);
        return $sUniqId;
    }
	
	public function checkIfCacheExists($sPageName, $sLang) {
		// In draft mode
		if(UserRequest::getRequest('bInDraftMode') !== false && $sPageName !== 'menu') {
			$sTplPath = $this->sDraftTplPath;
			$sLocPath = $this->sDraftLocPath;
			$sCachePath = $this->sDraftCachePath;
		// In version mode
		} elseif(UserRequest::getRequest('version') !== false && $sPageName !== 'menu') {
			$sTplPath = $this->sVersionTplPath;
			$sLocPath = $this->sVersionLocPath;
			$sCachePath = $this->sVersionCachePath;
		// In prod mode
		} else {
			$sTplPath = $this->sTplPath;
			$sLocPath = $this->sLocPath;
			$sCachePath = $this->sCachePath;
		}
		try {
			$this->oTplMgr = new TplMgr($this->oLang, $sLocPath, $sTplPath);
			if(UserRequest::getRequest('nocache') !== false) {
				$this->sCache = $this->oTplMgr->buildMultilingualTpl($sTplPath.$sPageName.'.tpl', $sLang);
				return true;
			}
			try {
				$this->sCache = file_get_contents($sCachePath.$sPageName.'_'.$sLang.'.html');
				return true;
			} catch(Exception $e) {
				if ($this->buildContentCache($sPageName, $sLang)) {
					return $this->checkIfCacheExists($sPageName, $sLang);
				} else {
					throw new CoreException('cache build error');
				}
			}
		} catch(CoreException $e){
			echo $e;
		} catch(Exception $e) {
			$sMsg = $e->getMessage().'<pre>'; 
			$sMsg .= print_r($e->getTrace(), true);
			$sMsg .= '</pre>';
			throw new CoreException($sMsg);
		}
        return false;
	}

    public function getCache() {
        if (empty($this->sCache)) {
            throw new CoreException('Cache empty');
        }
        return $this->sCache;
	}

    public function purgeCache() {
        foreach (new DirectoryIterator(CACHE_PATH) as $oFileInfos) {
            unlink($oFileInfos->getFilename());
        }
        return $this->_resetCache();
    }

    protected function _resetCache() {
		$this->aDebug = array();
		foreach (new DirectoryIterator($this->sLocPath) as $oFileInfos) {
			if ($oFileInfos->isDir() && !$oFileInfos->isDot() && in_array($oFileInfos->getFilename(), LangSwitcher::$aFlagsName)) {
				$sLang = $oFileInfos->getFilename();
				$this->oLang->LOCALE = $sLang;
				try {
					$this->aDebug[$sLang] = $this->checkCache($sLang);
				} catch(Exception $e) {
					$sError = implode(
									"\n",
									array(
										$e->getMessage(),
										print_r($e->getTrace(), true)
									)
								);
					UserRequest::$oAlertBoxMgr->danger = nl2br($sError);
					continue;
				}
			}
		}
		$this->setUniqId();
		// reset locale as courant locale
		$this->oLang->LOCALE = UserRequest::getLang();
		return $this->aDebug;
    }
	
	protected function _deleteCache($sPageName, $sSide, $sExt) {
		$sCachePath = $sSide === TranslationsMgr::TRANS_FRONT ? CACHE_PATH : ADMIN_CACHE_PATH;
		$aFrontLang = TranslationsMgr::getLangAvailableBySide($sSide);
		foreach (new DirectoryIterator($sCachePath) as $oFileInfos) {
			foreach($aFrontLang as $sLang) {
				if($sPageName.'_'.$sLang.$sExt === $oFileInfos->getFilename()) {
					unlink($sCachePath.$oFileInfos->getFilename());
				}
			}
        }
		return true;
	}
	
	private function setUniqId() {
		$oConfig = new Config();
		$oConfig->setGlobalConf('SYS_UNIQ_ID', uniqid());
		unset($oConfig);
		return true;
	}

    private function checkCache($sLang) {
        if (!is_dir($this->sLocPath.$sLang)) {
            throw new GenericException($this->sLocPath.$sLang.' is not a valid directory');
		}
		$aFiles = array();
        foreach (new DirectoryIterator($this->sTplPath) as $oFileInfos) {
            if ($oFileInfos->isFile() && !in_array($oFileInfos->getFilename(), $this->aNoCache)) {
                if (!$this->buildContentCache($oFileInfos->getBasename('.tpl'), $sLang)) {
                    throw new CoreException('build cache failed for '.$oFileInfos->getFilename());
                }
                $aFiles[] = $oFileInfos->getBasename('.tpl');
            }
        }
        return $aFiles;
    }

    private function buildContentCache($sTplName, $sLang) {
		// In draft mode
		if(UserRequest::getRequest('bInDraftMode') !== false && 
		UserRequest::getParams('content') !== 'menu') {
			return $this->buildContentDraftCache($sTplName, $sLang);
		}
		if (in_array($sTplName, $this->aNoCache)) {
			// nothing to do
			return true;
		}
        if (!file_exists($this->sTplPath.$sTplName.'.tpl')) {
            throw new CoreException($sTplName.'.tpl not found');
        }
        $sCache = $this->oTplMgr->buildMultilingualTpl($this->sTplPath.$sTplName.'.tpl', $sLang);
        return file_put_contents(
                                $this->sCachePath.$sTplName.'_'.$sLang.'.html', 
                                Minifier::minifyHtml($sCache)
                        );
    }
	
	private function buildContentDraftCache($sTplName, $sLang) {
		if (in_array($sTplName, $this->aNoCache)) {
			// nothing to do
			return true;
		}
        if (!file_exists($this->sDraftTplPath.$sTplName.'.tpl')) {
            throw new CoreException($sTplName.'.tpl not found');
        }
        $sCache = $this->oTplMgr->buildMultilingualTpl($this->sDraftTplPath.$sTplName.'.tpl', $sLang);
        return file_put_contents(
                                $this->sDraftCachePath.$sTplName.'_'.$sLang.'.html', 
                                Minifier::minifyHtml($sCache)
                        );
	}
}