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
final class VersionsContentsMgr  {
	
	private static $aVersions				= array();
	private static $sPageBlocTplName		= 'pages.versions.pageBloc.tpl';
	private static $sPageVersionItemTplName	= 'pages.versions.item.tpl';
	private static $sPageVersionsTplName	= 'pages_versions.tpl';
	private static $sButtonPreviewTplName	= 'version.preview.button.tpl';
	private static $sButtonDeleteTplName	= 'version.delete.button.tpl';
	private static $sButtonApplyTplName		= 'version.apply.button.tpl';
	private static $sQueryString			= '?2af72f100c356273d46284f6fd1dfc08=';
	public static $sParamUrl				= '2af72f100c356273d46284f6fd1dfc08';
	const SUCCESS_APPLY_VERSION_MSG			= 'SUCCESS_APPLY_VERSION_MSG';
	const ERROR_APPLY_VERSION_MSG			= 'ERROR_APPLY_VERSION_MSG';
	const SUCCESS_PURGE_VERSIONS_MSG		= 'SUCCESS_PURGE_VERSIONS_MSG';
	const ERROR_PURGE_VERSIONS_MSG			= 'ERROR_PURGE_VERSIONS_MSG';
	const DELETE_VERSION_CONFIRM			= 'DELETE_VERSION_CONFIRM';
	const SUCCESS_DELETE_VERSION_MSG		= 'SUCCESS_DELETE_VERSION_MSG';
	const ERROR_DELETE_VERSION_MSG			= 'ERROR_DELETE_VERSION_MSG';
	
	public static function backup2Version($sTransType, $sTplPath) {
		$sPageName =  basename($sTplPath, '.tpl');
		$sTime = time();
		try {
			/*TPL*/
			copy(
				$sTplPath, 
				self::getTplPath($sTransType, $sPageName, $sTime)
			);
			/*LOCALES & CACHE*/
			if($sTransType === TranslationsMgr::TRANS_BACK) {
				$sDraftRootPath = DRAFTS_ADMIN_CACHE_PATH;
			} else {
				$sDraftRootPath = DRAFTS_CACHE_PATH;
			}
			foreach (TranslationsMgr::getLangAvailableBySide($sTransType)  as $sLang) {
				copy(
					TranslationsMgr::getXmlRootPath($sTransType).$sLang.'/'.$sPageName.'.xml', 
					self::getXmlRootPath($sTransType).$sLang.'/'.$sTime.'_'.$sPageName.'.xml'
				);
				copy(
					$sDraftRootPath.$sPageName.'_'.$sLang.'.html', 
					self::getVersionCachePath($sPageName, $sLang, $sTime)
				);
			}
			return true;
		} catch(Exception $e) {
			throw new CoreException($e->getMessage());
		}
	}
	
	public static function applyVersion($sTransType, $sTpl, $sVersion) {
		$sPageName =  basename($sTpl, '.tpl');
		try {
			/*TPL*/
			$sTplPath = self::getTplPath($sTransType, $sPageName, $sVersion);
			if($sTplPath !== false) {
				copy(
					$sTplPath, 
					self::getTplPath($sTransType, $sPageName, $sVersion, true)
				);
			}
			/*LOCALES & CACHE*/
			foreach (TranslationsMgr::getLangAvailableBySide($sTransType)  as $sLang) {
				$sLocPath = self::getVersionLocPath($sTransType, $sLang, $sPageName, $sVersion);
				if($sLocPath !== false) {
					copy(
						$sLocPath, 
						TranslationsMgr::getXmlRootPath($sTransType).$sLang.'/'.$sPageName.'.xml'
					);
				}
				$sCachePath = self::getVersionCachePath($sPageName, $sLang, $sVersion);
				if(file_exists($sCachePath)) {
					copy(
						$sCachePath, 
						CACHE_PATH.$sPageName.'_'.$sLang.'.html'
					);
				}
			}
			return true;
		} catch(Exception $e) {
			throw new CoreException($e);
		}
	}
	
	private static function getXmlRootPath($sTransType) {
		switch($sTransType) {
			case TranslationsMgr::TRANS_FRONT:
				return BACKUP_PATH.GEN_LOC_PATH;
			case TranslationsMgr::TRANS_BACK:
				return BACKUP_PATH.GEN_ADMIN_LOC_PATH;
			case TranslationsMgr::TRANS_COMMON:
				return BACKUP_PATH.GEN_COMMON_LOC_PATH;
			default :
				throw new CoreException('unknow type '.$sTransType);
		}
	}
	
	private static function getTplRootPath($sTransType, $bProd=false) {
		if($sTransType === TranslationsMgr::TRANS_FRONT) {
			return $bProd ? CONTENT_TPL_PATH : BACKUP_PATH.GEN_TPL_CONTENTS_PATH;
		} elseif($sTransType === TranslationsMgr::TRANS_BACK) {
			/*
			 * TODO
			 * RENOMMER COMME IL FAUT LES CONSTANTES 
			 * GEN_ADMIN_TPL_CONTENTS_PATH
			 * &
			 * ADMIN_CONTENT_TPL_PATH
			 * C'est pas super logique quoi...
			 */
			return $bProd ? ADMIN_CONTENT_TPL_PATH : BACKUP_PATH.GEN_ADMIN_TPL_CONTENTS_PATH;
		} else {
			throw new CoreException('unknow type '.$sTransType);
		}
	}
	
	private static function getTplPath($sTransType, $sPageName, $sTime, $bProd=false) {
		$sPrefix = $bProd ? '' : $sTime.'_';
		return self::getTplRootPath($sTransType, $bProd).$sPrefix.$sPageName.'.tpl';
	}
	
	private static function getVersionLocPath($sTransType, $sLang, $sPageName, $sTime) {
		return self::getXmlRootPath($sTransType).$sLang.'/'.$sTime.'_'.$sPageName.'.xml';
	}
	
	private static function getVersionCachePath($sPageName, $sLang, $sTime) {
		return BACKUP_PATH.GEN_CACHE_PATH.$sTime.'_'.$sPageName.'_'.$sLang.'.html';
	}
	
	private static function getVersionsInArray() {
		if(!empty(self::$aVersions)) {
			return self::$aVersions;
		}
		foreach(scandir(BACKUP_CACHE_PATH) as $sCache) {
			$aVersion = explode('_', $sCache);
			if(count($aVersion) < 3) {
				continue;
			}
			$sVersion = array_shift($aVersion);
			$sLang = array_pop($aVersion);
			$sPagename = implode('_', $aVersion);
			if(self::checkIfValidVersions($sPagename, $sVersion)) {
				if(!isset(self::$aVersions[$sPagename])) {
					self::$aVersions[$sPagename] = array();
				}
				if(!in_array($sVersion, self::$aVersions[$sPagename])) {
					self::$aVersions[$sPagename][] = $sVersion;
				}
			}
		}
		return self::$aVersions;
	}
	
	private static function checkIfValidVersions($sPagename, $sVersion) {
		$bValid = true;
		if(!file_exists(BACKUP_TPL_PATH.$sVersion.'_'.$sPagename.'.tpl')) {
			$bValid = false;
		}
		foreach(SessionCore::getLangObject()->getFrontAvailable() as $sLang) {
			if(!file_exists(BACKUP_LOC_PATH.$sLang.'/'.$sVersion.'_'.$sPagename.'.xml')) {
				$bValid = false;
			}
			if(!file_exists(BACKUP_CACHE_PATH.$sVersion.'_'.$sPagename.'_'.$sLang.'.html')) {
				$bValid = false;
			}
		}
		return $bValid;
	}
	
	public static function getVersionsInterface() {
		$sPageBlocTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.self::$sPageBlocTplName);
		$sPageVersionItemTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.self::$sPageVersionItemTplName);
		$sPageTpl = file_get_contents(ADMIN_CONTENT_TPL_PATH.self::$sPageVersionsTplName);
		$sBlocs = '';
		foreach(self::getVersionsInArray() as $sPageName=>$aVersions) {
			$sVersions = '';
			foreach($aVersions as $sVersion) {
				$sVersions .= str_replace(
										array(
											'{__VERSION__}',
											'{__VERSION_PREVIEW__}',
											'{__DELETE_VERSION__}',
											'{__APPLY_VERSION_BUTTON__}'
										), 
										array(
											date('Y:m:d H:i:s', $sVersion),
											self::getPreviewComponent($sPageName, $sVersion),
											self::getDeleteButton($sPageName, $sVersion),
											self::getApplyButton($sPageName, $sVersion)
										), 
										$sPageVersionItemTpl
									);
			}
			$sBlocs .= str_replace(
								array('{__PAGE_NAME__}','{__VERSIONS__}'), 
								array($sPageName, $sVersions), 
								$sPageBlocTpl
							);
		}
		return str_replace('{__VERSIONS__}', $sBlocs, $sPageTpl);
	}
	
	private static function getApplyButton($sPage, $sVersion) {
		return str_replace(
						array(
							'{__VERSION__}',
							'{__PAGE_NAME__}'
						), 
						array(
							$sVersion,
							$sPage
						), 
						file_get_contents(ADMIN_PARTS_TPL_PATH.self::$sButtonApplyTplName)
					);
	}
	
	private static function getDeleteButton($sPage, $sVersion) {
		return str_replace(
						array(
							'{__VERSION__}',
							'{__PAGE_NAME__}'
						), 
						array(
							$sVersion,
							$sPage
						), 
						file_get_contents(ADMIN_PARTS_TPL_PATH.self::$sButtonDeleteTplName)
					);
	}
	
	private static function getPreviewComponent($sPage, $sVersion) {
		$aLangs = SessionCore::getLangObject()->getFrontAvailable();
		return str_replace(
						array(
							'{__VERSION__}',
							'{__PAGE_NAME__}',
							'{__LANG_LIST__}'
						), 
						array(
							$sVersion,
							$sPage,
							Toolz_Form::optionsList(DEFAULT_LANG, $aLangs)
						), 
						file_get_contents(ADMIN_PARTS_TPL_PATH.self::$sButtonPreviewTplName)
					);
	}
	
	public static function getVersionUrl($sPage, $sLang, $sVersion) {
		$sFrontUrl = DEV ? SITE_URL_DEV : SITE_URL_PROD;
		$sRequest = '/'.$sLang.'/'.str_replace('_', '/', $sPage).'.html';
		return $sFrontUrl.$sRequest.self::$sQueryString.$sVersion;
	}
	
	public static function deleteVersion($sFile) {
		if(file_exists(BACKUP_TPL_PATH.$sFile.'.tpl')) {
			unlink(BACKUP_TPL_PATH.$sFile.'.tpl');
		}
		foreach(SessionCore::getLangObject()->getFrontAvailable() as $sLang) {
			if(file_exists(BACKUP_LOC_PATH.$sFile.'.xml')) {
				unlink(BACKUP_LOC_PATH.$sLang.'/'.$sFile.'.xml');
			}
		}
		if(file_exists(BACKUP_CACHE_PATH.$sFile.'.html')) {
			unlink(BACKUP_CACHE_PATH.$sFile.'.html');
		}
	}
	
	public static function purgeVersions($sPageName) {
		foreach(scandir(BACKUP_TPL_PATH) as $sFile) {
			$aFile = explode('_', basename($sFile, '.tpl'));
			array_shift($aFile);
			if(implode('_', $aFile) === $sPageName) {
				unlink(BACKUP_TPL_PATH.$sFile);
			}
		}
		foreach(SessionCore::getLangObject()->getFrontAvailable() as $sLang) {
			foreach(scandir(BACKUP_LOC_PATH.$sLang) as $sFile) {
				$aFile = explode('_', basename($sFile, '.xml'));
				array_shift($aFile);
				if(implode('_', $aFile) === $sPageName) {
					unlink(BACKUP_LOC_PATH.$sLang.'/'.$sFile);
				}
			}
			foreach(scandir(BACKUP_CACHE_PATH) as $sFile) {
				$aFile = explode('_', basename($sFile, '.xml'));
				array_shift($aFile);
				if(implode('_', $aFile) === $sPageName.'_'.$sLang) {
					unlink(BACKUP_CACHE_PATH.$sFile);
				}
			}
		}
		return true;
	}
}