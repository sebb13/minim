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
final class DraftMgr {
	
	private $sDraftTplName		= 'draft.container.tpl';
	private $sDraftTpl			= '';
	private $sPreviewTplName	= 'draft.preview.button.tpl';
	private $sPreviewTpl		= '';
	private $sControlsTplName	= 'draft.controls.tpl';
	private $sControlsTpl		= '';
	private $sQueryString		= '?f91b2829bf8603e358094e1dedb6f947=1';
	public static $sParamUrl	= 'f91b2829bf8603e358094e1dedb6f947';
	public static $sTplKey		= 'TPL';
	public static $sLocKey		= 'LOC';
	public static $sCacheKey	= 'CACHE';
	
	public function __construct() {
		$this->sDraftTpl = file_get_contents(ADMIN_CONTENT_TPL_PATH.$this->sDraftTplName);
		$this->sPreviewTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sPreviewTplName);
		$this->sControlsTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sControlsTplName);
	}
	
	public function getDraftUrl($sPage, $sLang, $sSide) {
		if($sSide === 'FRONT') {
			$sUrl = DEV ? SITE_URL_DEV : SITE_URL_PROD;
		} elseif($sSide === 'BACK') {
			$sUrl = DEV ? ADMIN_URL_DEV : ADMIN_URL_PROD;
		} else {
			throw new CoreException('unknow side '.$sSide);
		}
		$sRequest = $sLang.'/'.str_replace('_', '/', $sPage).'.html';
		return $sUrl.$sRequest.$this->sQueryString;
	}
	
	public static function getRootPath($sSide, $sType) {
		if($sType === self::$sTplKey) {
			if($sSide === TranslationsMgr::TRANS_BACK) {
				return DRAFTS_ADMIN_TPL_PATH;
			} elseif($sSide === TranslationsMgr::TRANS_FRONT) {
				return DRAFTS_TPL_PATH;
			} else {
				
			}
		} elseif($sType === self::$sLocKey) {
			if($sSide === TranslationsMgr::TRANS_BACK) {
				return DRAFTS_ADMIN_LOC_PATH;
			} elseif($sSide === TranslationsMgr::TRANS_FRONT) {
				return DRAFTS_LOC_PATH;
			} else {
				
			}
		} elseif($sType === self::$sCacheKey) {
			if($sSide === TranslationsMgr::TRANS_BACK) {
				return DRAFTS_ADMIN_CACHE_PATH;
			} elseif($sSide === TranslationsMgr::TRANS_FRONT) {
				return DRAFTS_CACHE_PATH;
			} else {
				
			}
		}
	}
	
	public function getDraft($sPage, $sLang) {
		return str_replace(
						'{__CONTENTS__}',
						$this->getDraftFromFront($sPage, $sLang),
						$this->sDraftTpl
				);
	}
	
	public function getSourceCode($sPage, $sLang) {
		return str_replace(
						'{__CONTENTS__}',
						Toolz_Tpl::getBasicElmt('pre', $this->getDraftFromFront($sPage, $sLang)),
						$this->sDraftTpl
				);
	}
	
	private function getDraftFromFront($sPage, $sLang) {
		return file_get_contents($this->getDraftUrl($sPage, $sLang));
	}
	
	public function getPreviewComponent($sPagename, $sDraftButtonValue) {
		$aLangs = SessionCore::getLangObject()->getFrontAvailable();
		return str_replace(
						array(
							'{__SELECT_LANG_NAME__}',
							'{__SELECT_LANG_ID__}',
							'{__LANG_LIST__}',
							'{__PAGE_NAME__}',
							'{__BUTTON_ID__}',
							'{__BUTTON_VALUE__}'
						),
						array(
							$sPagename.'DraftSelectLang', 
							$sPagename.'DraftSelectLang',
							Toolz_Form::optionsList(DEFAULT_LANG, $aLangs),
							$sPagename,
							$sPagename.'DraftButton', 
							$sDraftButtonValue
						),
						$this->sPreviewTpl
			);
	}
	
	public function getDraftControls($sPageName) {
		return str_replace('{__PAGE_NAME__}', $sPageName, $this->sControlsTpl);
	}
	
	public function resetDrafts($sTransType, $sTplPath) {
		$bError = false;
		$sFilename = basename($sTplPath, '.xml');
		// TPL
		$sTplPath = self::getRootPath($sTransType, self::$sTplKey).$sFilename.'.tpl';
		if(file_exists($sTplPath)) {
			unlink($sTplPath);
		} else {
			$bError = true;
		}
		foreach(TranslationsMgr::getLangAvailableBySide($sTransType) as $sLang) {
			// CACHE
			$sCacheFilePath = self::getRootPath($sTransType, self::$sCacheKey).$sFilename.'_'.$sLang.'.html';
			if(file_exists($sCacheFilePath)) {
				unlink($sCacheFilePath);
			} else {
				$bError = true;
			}
			// LOCALES
			$sLocFilePath = self::getRootPath($sTransType, self::$sLocKey).$sLang.'/'.$sFilename.'.xml';
			if(file_exists($sLocFilePath)) {
				unlink($sLocFilePath);
			} else {
				$bError = true;
			}
		}
		return !$bError;
	}
	
	public function publish($sTransType, $sNewVersion, $sOldVersion) {
		$sPageName = basename($sOldVersion, '.tpl');
		// BACKUP
		VersionsContentsMgr::backup2Version($sTransType, $sOldVersion);
		// TPL
		if(file_exists($sNewVersion)) {
			rename($sNewVersion, $sOldVersion);
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('drafts', 'ERROR_PUBLISH_DRAFT').' ('.$sPageName.')';
			return false;
		}
		foreach(TranslationsMgr::getLangAvailableBySide($sTransType) as $sLang) {
			// LOCALES
			if(file_exists(TranslationsMgr::getXmlRootPath($sTransType, true).$sLang.'/'.$sPageName.'.xml')) {
				rename(
					TranslationsMgr::getXmlRootPath($sTransType, true).$sLang.'/'.$sPageName.'.xml', 
					TranslationsMgr::getXmlRootPath($sTransType).$sLang.'/'.$sPageName.'.xml'
				);
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('drafts', 'SUCCESS_PUBLISH_DRAFT').' ('.$sPageName.' - '.$sLang.')';
			} else {
				debug('here');
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('drafts', 'ERROR_PUBLISH_DRAFT').' ('.$sPageName.' - '.$sLang.')';
			}
			// CACHE
			if($sTransType === TranslationsMgr::TRANS_BACK) {
				$sRootPath = ADMIN_CACHE_PATH;
			} else {
				$sRootPath = CACHE_PATH;
			}
			$sDraftPath = self::getRootPath($sTransType, self::$sCacheKey).$sPageName.'_'.$sLang.'.html';
			if(file_exists($sDraftPath)) {
				rename(
					$sDraftPath,
					$sRootPath.$sPageName.'_'.$sLang.'.html'
				);
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_resetCache', 'SUCCESS_UPDATE_CACHE').' ('.$sPageName.' - '.$sLang.')';
			} else {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_resetCache', 'ERROR_UPDATE_CACHE').' ('.$sPageName.' - '.$sLang.')';
			}
		}
		return true;
	}
}