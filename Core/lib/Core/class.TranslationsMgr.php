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
final class TranslationsMgr extends SimpleXmlMgr {
	
	const TRANS_FRONT			= 'translations_front';
	const TRANS_BACK			= 'translations_back';
	const TRANS_COMMON			= 'translations_common';
	private $sTransTpl			= 'translations.tpl';
	private $sEmptyXml			= 'emptyLang.xml';
	private $sFile				= '';
	private $sLocPath			= '';
	private $sDraftLocPath		= '';
	private $sRefLang			= '';
	private $sLangToTranslate	= '';
	private $sItemTpl			= '';
	private $sTransType			= '';
	private $bIsDraft			= false;
	
	public function __construct($sTransType, $sRefLang, $sLangToTranslate, $sFile='') {
		$this->sLocPath			= self::getXmlRootPath($sTransType);
		$this->sDraftLocPath	= self::getXmlRootPath($sTransType, true);
		$this->sRefLang			= $sRefLang;
		$this->sLangToTranslate = $sLangToTranslate;
		$this->sFile			= $sFile;
		$this->sItemTpl			= file_get_contents(ADMIN_PARTS_TPL_PATH.'translations.item.tpl');
		$this->sTransType		= $sTransType;
		if(!empty($sFile) && $this->sFile !== SessionCore::getLangObject()->sCommonFilename) {
			$this->createDraft($sFile);
		}
		return parent::__construct();
	}
	
	private function createDraft($sFile) {
		if(!file_exists(self::getXmlRootPath($this->sTransType, true).DEFAULT_LANG.'/'.$sFile)) {
			foreach(self::getLangAvailableBySide($this->sTransType) as $sLang) {
				$sFilePath = self::getXmlRootPath($this->sTransType).$sLang.'/'.$sFile;
				if(!file_exists($sFilePath)) {
					$sFilePath = self::getXmlRootPath($this->sTransType).DEFAULT_LANG.'/'.$sFile;
				}
				copy(
					$sFilePath, 
					self::getXmlRootPath($this->sTransType, true).$sLang.'/'.$sFile
				);
			}
		} else {
			$this->bIsDraft = true;
		}
		return true;
	}
	
	public static function getLangAvailableBySide($sTransType) {
		//remplacer par des if elseif
		switch($sTransType) {
			case self::TRANS_FRONT:
				return SessionCore::getLangObject()->getFrontAvailable();
			case self::TRANS_BACK:
			case self::TRANS_COMMON:
				return SessionCore::getLangObject()->aAdminLangAvailable;
			default :
				throw new CoreException('unknow type');
		}
	}
	
	public static function getXmlRootPath($sTransType, $bIsDraft=false) {
		//remplacer par des if elseif
		switch($sTransType) {
			case self::TRANS_FRONT:
				return $bIsDraft ? DRAFTS_LOC_PATH : LOC_PATH;
			case self::TRANS_BACK:
				return $bIsDraft ? DRAFTS_ADMIN_LOC_PATH : ADMIN_LOC_PATH;
			case self::TRANS_COMMON:
				// pas de draft pour les trads communes, pas de page à prévisualiser
				return COMMON_LOC_PATH;
			default :
				throw new CoreException('unknow page');
		}
	}
	
	private function getFilesAvailableByLang() {
		if(!is_dir($this->sLocPath.$this->sLangToTranslate)) {
			mkdir($this->sLocPath.$this->sLangToTranslate);
		}
		$sFilePath = $this->sLocPath.$this->sLangToTranslate.'/'.$this->sFile;
		// -- Si le fichier de la langue à traduire n'existe pas, 
		// -- on le crée et on l'initialise avec les noeuds de la langue par défaut.
		if (!file_exists($sFilePath)) {
			file_put_contents(
				$sFilePath, 
				file_get_contents(CORE_TPL_PATH.$this->sEmptyXml)
			);
			$this->loadFile($this->sLocPath.DEFAULT_LANG.'/'.$this->sFile);
			$aTranslates = $this->getIemsList();
			$this->loadFile($sFilePath);
			foreach($aTranslates as $sKey=>$sValue) {
				$this->setItem($sKey, '');
			}
		}
		$aFileList = array();
		foreach(scandir($this->sLocPath.DEFAULT_LANG) as $sFilename) {
			if(!in_array($sFilename, Toolz_Main::$aScandirIgnore)) {
				$aFileList[$sFilename] = basename($sFilename, '.xml');
			}
		}
		if ($this->sFile === basename($this->sEmptyXml)) {
			$aFileList['-'] = '-';
		}
		ksort($aFileList);
		return $aFileList;
	}
	
	private function getLangAvailable() {
		$aLang = array();
		foreach(scandir($this->sLocPath) as $sLang) {
			if(!in_array($sLang, Lang::$aDirNotLang)) {
				$aLang[$sLang] = $sLang;
			}
		}
		if(empty($aLang)) {
			$aLang[DEFAULT_LANG] = DEFAULT_LANG;
		}
		return $aLang;
	}
	
	public function getLocaleFileInterface() {
		$aFileParts = pathinfo($this->sFile);
		$aFileList = $this->getFilesAvailableByLang();
		if(empty($this->sFile)) {
			$this->sFile = key($aFileList);
			$this->createDraft($this->sFile);
		}
		// gestion des brouillons
		$oDraftMgr = new DraftMgr();
		$sContents = str_replace(
							array(
								'{__REF_LANG_LIST__}',
								'{__LANG_TO_TRANS_LIST__}',
								'{__FILE_LIST__}',
								'{__ORIGINE__}',
								'{__CONTENTS__}',
								'{__TRANSLATES_PREVIEW__}',
								'{__DRAFT_CONTROLS__}',
							),
							array(
								Toolz_Form::optionsList($this->sRefLang, $this->getLangAvailable()),
								Toolz_Form::optionsList($this->sLangToTranslate, $this->getLangAvailable()),
								Toolz_Form::optionsList($aFileParts['filename'], $aFileList),
								$this->bIsDraft ? '{__DRAFT__}' : '{__PROD_DATA__}',
								$this->getFileContentsForm(),
								$oDraftMgr->getPreviewComponent(basename($this->sFile, '.xml'), '{__PAGE_PREVIEW__}'),
								$oDraftMgr->getDraftControls('Translations'),
							),
							file_get_contents(ADMIN_CONTENT_TPL_PATH.$this->sTransTpl)
				);
		unset($oDraftMgr);
		return $sContents;
	}
	
	private function getFileContentsForm() {
		if(!file_exists($this->sLocPath.$this->sRefLang.'/'.$this->sFile)) {
			$sXmlPath = $this->sLocPath.DEFAULT_LANG.'/'.$this->sFile;
		} else {
			$sXmlPath = $this->sLocPath.$this->sRefLang.'/'.$this->sFile;
		}
		$this->loadFile($sXmlPath);
		$aRefLang = $this->getIemsList();
		if(!file_exists($this->sDraftLocPath.$this->sLangToTranslate.'/'.$this->sFile)) {
			copy(
				$this->sLocPath.$this->sLangToTranslate.'/'.$this->sFile,
				$this->sDraftLocPath.$this->sLangToTranslate.'/'.$this->sFile);
		}
		$sXmlPath = $this->sDraftLocPath.$this->sLangToTranslate.'/'.$this->sFile;
		$this->loadFile($sXmlPath);
		$this->sFile = basename($sXmlPath);
		$sContents = '';
		foreach($this->getIemsList() as $sNodeName=>$mValue) {
			if (!array_key_exists($sNodeName, $aRefLang)) {
				$aRefLang[$sNodeName] = '';
			}
			if(is_array($mValue)) {
				$mValue = '';
			}
			if(is_array($aRefLang[$sNodeName])) {
				$aRefLang[$sNodeName] = '';
			}
			$sContents .= str_replace(
									array(
										'{__TAG_NAME__}',
										'{__REF_LANG_DISPLAY__}',
										'{__LANG_TO_TRANS_DISPLAY__}'
									),
									array(
										$sNodeName,
										$aRefLang[$sNodeName],
										trim($mValue)
									),
									$this->sItemTpl
						);
		}
		return $sContents;
	}
	
	public function saveTranslations($aTranslations) {
		if($this->sFile !== SessionCore::getLangObject()->sCommonFilename) {
			$sTransFilePath = $this->sDraftLocPath.$this->sLangToTranslate.'/'.$this->sFile;
		} else {
			$sTransFilePath = $this->sLocPath.$this->sLangToTranslate.'/'.$this->sFile;
		}
		if($this->save2pathFromArray('translations', $sTransFilePath, $aTranslations)) {
			if($this->sTransType === TranslationsMgr::TRANS_BACK) {
				$sTplRootPath = ADMIN_CONTENT_TPL_PATH;
			} else {
				$sTplRootPath = CONTENT_TPL_PATH;
			}
			if(file_exists($sTplRootPath.basename($sTransFilePath, '.xml').'.tpl')) {
				copy(
					$sTplRootPath.basename($sTransFilePath, '.xml').'.tpl',
					DraftMgr::getRootPath($this->sTransType, 'TPL').basename($sTransFilePath, '.xml').'.tpl'
				);
				SessionCore::getLangObject()->LOC_PATH = DRAFTS_LOC_PATH;
				$oCacheMgr = new CacheMgrPreview(SessionCore::getLangObject(), $this->sTransType);
				$oCacheMgr->resetCache();
				unset($oCacheMgr);
				SessionCore::getLangObject()->LOC_PATH = ADMIN_LOC_PATH;
			}
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('translations', 'SUCCESS_SAVE_TRANS');
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('translations', 'ERROR_SAVE_TRANS');
		}
		return true;
	}
}