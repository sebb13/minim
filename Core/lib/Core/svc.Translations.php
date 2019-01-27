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
final class Translations extends CoreCommon {
	
	private $oDraftMgr		= null;
	private $sTransLocales	= 'translations.xml';
	
	public function __construct() {
		parent::__construct();
		$this->oDraftMgr = new DraftMgr();
	}
	
	public function getTranslationsInterface() {
		UserRequest::startBenchmark('getTranslationsInterface');
		if(($sRefLang = UserRequest::getParams('sRefLang')) === false) {
			$sRefLang = DEFAULT_LANG;
		}
		if(($sLangToTranslate = UserRequest::getParams('sLangToTranslate')) === false) {
			$sLangToTranslate = DEFAULT_LANG;
		}
		$oTranslationsMgr = new TranslationsMgr(
												UserRequest::getRequest('sPage'), 
												$sRefLang,
												$sLangToTranslate,
												UserRequest::getParams('sFileToTranslate')
									);
		$sContents = $oTranslationsMgr->getLocaleFileInterface();
		$sContents .= UserRequest::stopBenchmark('getTranslationsInterface', true);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
														$sContents, 
														ADMIN_LOC_PATH.$this->oLang->LOCALE.'/'.$this->sTransLocales
							),
				'sPage'	=> UserRequest::getRequest('sPage')
			);
	}
	
	public function saveTranslations() {
		$aInputs = array();
		foreach(UserRequest::getParams('translates') as $sKey=>$sValue) {
			if (strpos($sKey, 'input_') === 0) {
				$aInputs[str_replace('input_', '',$sKey)] = trim($sValue);
			}
		}
		$oTranslationsMgr = new TranslationsMgr(
												UserRequest::getRequest('sPage'),
												UserRequest::getParams('sRefLang'),
												UserRequest::getParams('sLangToTranslate'),
												UserRequest::getParams('sFileToTranslate')
											);
		$oTranslationsMgr->saveTranslations($aInputs);
		return $this->getTranslationsInterface();
	}
	
	public function resetDraft() {
		try {
			$bResetSuccess = $this->oDraftMgr->resetDrafts(
										UserRequest::getRequest('sPage'), 
										UserRequest::getParams('sFileToTranslate')
									);
			if($bResetSuccess) {
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('drafts', 'DRAFT_SUCCESSFULLY_RESET');
			} else {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('drafts', 'CAN_NOT_RESET_DRAFT');
			}
		} catch(CoreException $e) {
			$e->getMessage();
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('drafts', 'CAN_NOT_RESET_DRAFT');
		}
		return $this->getTranslationsInterface();
	}
	
	public function publishTranslations() {
		$sFilename = basename(UserRequest::getParams('sFileToTranslate'), '.xml').'.tpl';
		if(UserRequest::getRequest('sPage') === TranslationsMgr::TRANS_FRONT) {
			$sPath = CONTENT_TPL_PATH;
			$sDraftPath = DRAFTS_TPL_PATH;
		} elseif(UserRequest::getRequest('sPage') === TranslationsMgr::TRANS_BACK) {
			$sPath = ADMIN_CONTENT_TPL_PATH;
			$sDraftPath = DRAFTS_ADMIN_TPL_PATH;
		} else {
				
		}
		$this->oDraftMgr->publish(
						UserRequest::getRequest('sPage'), 
						$sDraftPath.$sFilename, 
						$sPath.$sFilename
					);
		return $this->getTranslationsInterface();
	}
	
	public function __destruct() {
		$this->oDraftMgr = null;
	}
}