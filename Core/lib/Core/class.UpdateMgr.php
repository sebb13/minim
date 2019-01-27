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
final class UpdateMgr extends SimpleXmlMgr {
	
	const UNABLE_TO_EXTRACT_UPDATE		= 'UNABLE_TO_EXTRACT_UPDATE';
	const SUCCESS_GENERATE_UPDATES		= 'SUCCESS_GENERATE_UPDATES';
	const ERROR_GENERATE_UPDATES		= 'ERROR_GENERATE_UPDATES';
	const SUCCESS_BACKUP				= 'SUCCESS_BACKUP';
	const SUCCESS_UPDATE_CONF			= 'SUCCESS_UPDATE_CONF';
	const ERROR_UPDATE_CONF				= 'ERROR_UPDATE_CONF';
	const SUCCESS_INSTALL_UPDATES		= 'SUCCESS_INSTALL_UPDATES';
	const ERROR_INSTALL_UPDATES			= 'ERROR_INSTALL_UPDATES';
	const SUCCESS_ROLLBACK				= 'SUCCESS_ROLLBACK';
	const ERROR_ROLLBACK				= 'ERROR_ROLLBACK';
	const SUCCESS_PURGE_BACKUP_UPDATE	= 'SUCCESS_PURGE_BACKUP_UPDATE';
	const ERROR_PURGE_BACKUP_UPDATE	= 'ERROR_PURGE_BACKUP_UPDATE';
	private $sHttpAuthIdent				= 'guest:guest'; // username:password 
	private $sWebsitesStateXml			= 'websitesState.xml'; // username:password 
	private $sSysUpdateSvcUrl			= ''; // username:password 
	private $aInstallList				= array();
	private $aRawInstallList			= array();
	
	public function __construct() {
		
		$this->sSysUpdateSvcUrl = DEV ? SYS_DEV_UPDATES_URL : SYS_UPDATES_URL;
		return parent::__construct(DATA_PATH.$this->sWebsitesStateXml);
	}
	
	public function getUpdatesInterface() {
		$sModuleTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.updates.tpl');
		$sUpdateButtonTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.update.button.tpl');
		$sCheckUpdateButtonTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.checkUpdate.button.tpl');
		$sRollbackButtonsTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.rollback.buttons.tpl');
		$sUpdateInterface = '';
		foreach(ModulesMgr::getModulesAvailable() as $sModuleName) {
			$sModuleDetails = '';
			$sRollbackButtons = '';
			$sVersion = ModulesMgr::getVersion($sModuleName);
			if (($sModuleDetails = $this->hasUpdatesWaiting($sModuleName)) !== false) {
				$sButton = str_replace('{__MODULE_NAME__}', $sModuleName, $sUpdateButtonTpl);
			} else {
				$sModuleDetails = '{__NO_UPDATE_TO_INSTALL__}';
				$sButton = str_replace('{__MODULE_NAME__}', $sModuleName, $sCheckUpdateButtonTpl);
			}
			if(ModulesMgr::getPreviousVersion($sModuleName) !== false) {
				$sRollbackButtons = str_replace(
											array('{__MODULE_NAME__}','{__VERSION__}'), 
											array($sModuleName, $sVersion), 
											$sRollbackButtonsTpl
										);
			} else {
				$sRollbackButtons = '{__NO_ROLLBACK_POSSIBLE__}';
			}
			$sUpdateInterface .= str_replace(
											array(
												'{__MODULE_NAME__}',
												'{__CURRENT_VERSION__}',
												'{__RELEASES__}',
												'{__UPDATE_BUTTON__}',
												'{__ROLLBACK_BUTTONS__}',
											), 
											array(
												$sModuleName,
												ModulesMgr::getVersion($sModuleName),
												$sModuleDetails,
												$sButton,
												$sRollbackButtons,
											),
											$sModuleTpl
									);
		}
		$oLang = SessionCore::getLangObject();
		$oTplMgr = new TplMgr($oLang);
		return $oTplMgr->buildSimpleCacheTpl(
										$sUpdateInterface, 
										ADMIN_LOC_PATH.$oLang->LOCALE.'/system_updates.xml'
				);
	}
	
	public function getRepositoryInterface() {
		$sContents = '';
		$sTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.updates.generateForm.tpl');
		foreach(ModulesMgr::getModulesAvailable() as $sModuleName) {
			$sCurrentVersion = ModulesMgr::getVersion($sModuleName);
			$aVersion = explode('.', $sCurrentVersion);
			$sContents .= str_replace(
									array(
										'{__MODULE_NAME__}',
										'{__CURRENT_VERSION__}',
										'{__MAIN_VERSION__}',
										'{__SUB_VERSION__}',
										'{__RELEASE__}'
									), 
									array(
										$sModuleName,
										$sCurrentVersion,
										$aVersion[0],
										$aVersion[1],
										++$aVersion[2]
									),
									$sTpl
								);
		}
		$oLang = SessionCore::getLangObject();
		$oTplMgr = new TplMgr($oLang);
		return $oTplMgr->buildSimpleCacheTpl(
										$sContents, 
										ADMIN_LOC_PATH.$oLang->LOCALE.'/system_updates.xml'
				);
	}
	
	public function setWebsitesState($sUrl, array $aModules) {
		$aWebsites = $this->getIemsList();
		$aWebsites[Toolz_Format::formatTanslateNodeName($sUrl)] = array('url'=>$sUrl, 'state'=>$aModules);
		$oNewXml = $this->array2xml($this->getEmptyXmlObject('websites'), $aWebsites);
		return $this->save2path($oNewXml, DATA_PATH.$this->sWebsitesStateXml);
	}
	
	public function getWebsitesState() {
		$sModuleTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.updates.websiteState.module.tpl');
		$sWebsiteTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.updates.websiteState.item.tpl');
		$sWebsites = '';
		foreach($this->getIemsList() as $aWebsite) {
			$sStates = '';
			foreach($aWebsite['state'] as $sModuleName=>$sVersion) {
				$sStates .= str_replace(
									array(
										'{__MODULE_NAME__}',
										'{__COMPLETE_CLASS__}',
										'{__VERSION__}'
									), 
									array(
										$sModuleName,
										ModulesMgr::getVersion($sModuleName) !== $sVersion ? 'text-danger' : 'text-success',
										$sVersion
									), 
									$sModuleTpl
								);
			}
			$sWebsites .= str_replace(
									array(
										'{__SITE_NAME__}',
										'{__SITE_LINK__}',
										'{__MODULES__}'
									), 
									array(
										$aWebsite['url'],
										$aWebsite['url'].'FR/system/updates.html',
										$sStates
									), 
									$sWebsiteTpl
								);
		}
		return $sWebsites;
	}
	
	public function sendState($bForce=false) {
		if(strtotime("-1day") > filemtime(DATA_PATH.'websitesState.rpr') || $bForce) {
			$aModulesAvailable = ModulesMgr::getModulesAvailable();
			$aStates = array();
			foreach($aModulesAvailable as $sModuleName) {
				$aStates[$sModuleName] = ModulesMgr::getVersion($sModuleName);
			}
			$aPost = array(
						'app_token'	=> SessionCore::getSessionHash(),
						'exw_action'=> SYS_SEND_STATE_SVC,
						'sSiteUrl'	=> DEV ? SITE_URL_DEV : SITE_URL_PROD,
						'sVersions'	=> $aStates
					);
			return Toolz_WebSvc::curlMinim($this->sSysUpdateSvcUrl, $aPost, $this->sHttpAuthIdent);
		}
		return true;
	}
	
	public function getRepositoryState() {
		$sTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'system.updates.repositoryState.item.tpl');
		$sRepositoryState = '';
		$aModules = array();
		foreach(scandir(REPOSITORY_PATH) as $sRelease) {
			$aRelease = explode('-', $sRelease);
			if(count($aRelease) === 2) {
				if(empty($aModules[$aRelease[0]])) {
					$aModules[$aRelease[0]] = array();
				}
				$sLink = Toolz_Tpl::getA(
									REPOSITORY_URL.$sRelease, 
									$this->getVersionFromRelease($aRelease[0], $aRelease[1]), 
									'', 
									'', 
									true
								);
				$aModules[$aRelease[0]][str_replace('.', '', $sRelease)] = $sLink;
			}
		}
		foreach(ModulesMgr::getModulesAvailable() as $sModuleName) {
			if(empty($aModules[$sModuleName])) {
				$sVersions = '{__NO_UPDATE_AVAILABLE__}';			
			} else {
				ksort($aModules[$sModuleName]);
				$sVersions = implode(' - ', $aModules[$sModuleName]);
			}
			$sRepositoryState .= str_replace(
											array(
												'{__MODULE_NAME__}',
												'{__CURRENT_VERSION__}',
												'{__VERSIONS__}'
											), 
											array(
												$sModuleName,
												ModulesMgr::getVersion($sModuleName),
												$sVersions
											), 
											$sTpl
										);
		}
		return $sRepositoryState;
	}
	
	private function checkVersion($sCurrentVersion, $sVersionToCheck) {
		$iCurentVersion = (int)str_replace('.', '', $sCurrentVersion);
		$iVersionToCheck = (int)str_replace('.', '', $sVersionToCheck);
		return $iVersionToCheck > $iCurentVersion;
	}
	
	private function hasUpdatesWaiting($sModuleName) {
		$aReleases = array();
		foreach(scandir(UPDATES_PATH) as $sRelease) {
			if(strpos($sRelease, $sModuleName) === 0) {
				$aReleases[] = $this->getVersionFromRelease($sModuleName, $sRelease);
			}
		}
		return !empty($aReleases) ? implode(' ; ', $aReleases) : false;
	}
	
	public function hasUpdates($sModuleName, $sVersion) {
		$aReleases = array();
		foreach(scandir(REPOSITORY_PATH) as $sRelease) {
			if(strpos($sRelease, $sModuleName) === 0) {
				$sReleaseVersion = $this->getVersionFromRelease($sModuleName, $sRelease);
				if($this->checkVersion($sVersion, $sReleaseVersion)) {
					$aReleases[str_replace('.', '', $sRelease)] = $sRelease;
				}
			}
		}
		ksort($aReleases);
		die(array_shift($aReleases));
	}
	
	public function hasNewReleases($sModuleName) {
		$aPost = array(
					'app_token'		=> SessionCore::getSessionHash(),
					'exw_action'	=> SYS_UPDATES_SVC,
					'sModuleName'	=> $sModuleName,
					'sVersion'		=> ModulesMgr::getVersion($sModuleName)
				);
		$sRelease = Toolz_WebSvc::curlMinim($this->sSysUpdateSvcUrl, $aPost, $this->sHttpAuthIdent);
		if(trim($sRelease) === '200') {
			return false;
		}
		return $this->storeNewRelease($sRelease);
	}
	
	private function storeNewRelease($sRelease) {
		$aPost = array(
					'app_token'		=> SessionCore::getSessionHash(),
					'exw_action'	=> SYS_GET_RELEASE_SVC,
					'sRelease'		=> $sRelease
				);
		$mResult = Toolz_WebSvc::curlMinim($this->sSysUpdateSvcUrl, $aPost, $this->sHttpAuthIdent);
		file_put_contents(UPDATES_PATH.$sRelease, $mResult);
		if(!$this->extract($sRelease)) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', self::UNABLE_TO_EXTRACT_UPDATE);
			return false;
		}
		$this->sendState(true);
		return true;
	}
	
	private function extract($sRelease) {
		$sFolderToExtract = basename($sRelease, '.zip');
		$oZip = new ZipArchive;
		if ($oZip->open(UPDATES_PATH.$sRelease) === true) {
			$oZip->extractTo(UPDATES_PATH.$sFolderToExtract);
			$oZip->close();
			unset($oZip);
			unlink(UPDATES_PATH.$sRelease);
			return true;
		} else {
			unset($oZip);
			return false;
		}
	}
	
	private function getVersionFromRelease($sModuleName, $sRelease) {
		return str_replace($sModuleName.'-', '', basename($sRelease, '.zip'));
	}
	
	private function buildBackup($sModuleName) {
		// suppression du backup précédent
		$this->purgeBackup($sModuleName, ModulesMgr::getPreviousVersion($sModuleName));
		$sVersion = ModulesMgr::getVersion($sModuleName);
		$aMapping = $this->getMapping($sModuleName);
		foreach($aMapping['dir'] as $sType=>$sPath) {
			$aPath = explode('/', substr($sPath, 0, -1));
			$sDirName = array_pop($aPath);
			$sDirName.= '-'.$sVersion;
			$sPathDest = implode('/', $aPath).'/'.$sDirName;
			if(!file_exists($sPathDest)) {
				shell_exec("cp -r $sPath $sPathDest");
			}
		}
		foreach($aMapping['files'] as $sFilePath) {
			$sPathDest = $sFilePath.'-'.$sVersion;
			if(!copy($sFilePath, $sPathDest)) {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', self::ERROR_BACKUP);
				return false;
			}
		}
		UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_updates', self::SUCCESS_BACKUP);
		return true;
	}
	
	public function rollback($sModuleName) {
		$sVersion = ModulesMgr::getPreviousVersion($sModuleName);
		$aMapping = $this->getMapping($sModuleName);
		// tour de vérification
		foreach($aMapping['dir'] as $sPath) {
			if(!file_exists(substr($sPath, 0, -1).'-'.$sVersion)) {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', self::ERROR_ROLLBACK);
				return false;
			}
		}
		foreach($aMapping['files'] as $sFilePath) {
			if(!file_exists($sFilePath.'-'.$sVersion)) {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', self::ERROR_ROLLBACK);
				return false;
			}
		}
		// rollback
		foreach($aMapping['dir'] as $sPath) {
			$sOldName = substr($sPath, 0, -1).'-'.$sVersion;
			$sDestPath = substr($sPath, 0, -1);
			shell_exec("rm -r $sDestPath && mv $sOldName $sDestPath");
		}
		foreach($aMapping['files'] as $sFilePath) {
			rename($sFilePath.'-'.$sVersion, $sFilePath);
		}
		ModulesMgr::setVersion($sModuleName, $sVersion);
		UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_updates', self::SUCCESS_ROLLBACK);
		return true;
	}
	
	private function purgeUpdateDir($sModuleName, $sVersion) {
		$sPath = UPDATES_PATH.$sModuleName.'-'.$sVersion;
		return Toolz_FileSystem::clearDir($sPath, true);
	}
	
	public function purgeBackup($sModuleName) {
		$sVersion = ModulesMgr::getPreviousVersion($sModuleName);
		if(!$sVersion) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', self::ERROR_PURGE_BACKUP_UPDATE);
			return false;
		}
		$aMapping = $this->getMapping($sModuleName);
		foreach($aMapping['dir'] as $sPath) {
			$sDirPath = substr($sPath, 0, -1).'-'.$sVersion;
			if(file_exists($sDirPath)) {
				Toolz_FileSystem::clearDir($sDirPath, true);
			}
		}
		foreach($aMapping['files'] as $sFilePath) {
			if(file_exists($sFilePath.'-'.$sVersion)) {
				unlink($sFilePath.'-'.$sVersion);
			}
		}
		UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_updates', self::SUCCESS_PURGE_BACKUP_UPDATE);
		return true;
	}
	
	public function installUpdates($sModuleName) {
		foreach(scandir(UPDATES_PATH) as $sRelease) {
			if(strpos($sRelease, $sModuleName) === 0) {
				//on range les mises à jour pour les mettre dans l'ordre avant de lancer l'installation
				$sReleasePath = UPDATES_PATH.$sRelease;
				$sVersion = $this->getVersionFromRelease($sModuleName, $sRelease);
				//backup
				if(!$this->buildBackup($sModuleName, $sVersion)) {
					UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', self::ERROR_INSTALL_UPDATES);
					return false;
				}
				break;
			}
		}
		$aMapping = $this->getMapping($sModuleName);
		foreach($aMapping['dir'] as $sType=>$sPath) {
			$sOldName = UPDATES_PATH.$sModuleName.'-'.$sVersion.'/'.str_replace('_', '/', $sType);
			$aPath = explode('/', substr($sPath, 0, -1));
			$sDirName = array_pop($aPath);
			$sPathDest = implode('/', $aPath).'/';
			shell_exec("cp -r $sOldName $sPathDest");
		}
		foreach($aMapping['files'] as $sPath) {
			if(basename($sPath) === 'minim.conf.xml') {
				$oConf = new Config();
				if($oConf->updateMinimConfFromXml($sReleasePath.'/'.'minim.conf.xml')) {
					$oConf->setGlobalConf('SYS_MOD_VERSION', $sVersion);
					UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_updates', self::SUCCESS_UPDATE_CONF);
				} else {
					UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', self::ERROR_UPDATE_CONF);
				}
			} else {
				copy($sReleasePath.'/'.basename($sPath), $sPath);
			}
		}
		ModulesMgr::setVersion($sModuleName, $sVersion);
		$this->purgeUpdateDir($sModuleName, $sVersion);
		UserRequest::$oAlertBoxMgr->success = ModulesMgr::setVersion($sModuleName, $sVersion);
		$this->sendState(true);
		UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_updates', self::SUCCESS_INSTALL_UPDATES);
		return true;
	}
	
	public function buildUpdatePackages($sVersion, $sModuleName='minim') {
		$oZip = new ZipArchive;
		$aMapping = array();
		$sZipFilename = $sModuleName.'-'.$sVersion.'.zip';
		$sZipFilePath = REPOSITORY_PATH.$sZipFilename;
		if(file_exists($sZipFilePath)) {
			unlink($sZipFilePath);
		}
		if($oZip->open($sZipFilePath, ZipArchive::CREATE) === true) {
			$aMapping = $this->getMapping($sModuleName);
			//répertoires
			foreach($aMapping['dir'] as $sType=>$sDirPath) {
				$aMapping[$sType] = array();
				foreach(new DirectoryIterator($sDirPath) as $oFileInfo) {
					if(!$oFileInfo->isDot()) {
						$sParentFolder = str_replace('_', '/', $sType);
						$oZip->addEmptyDir($sParentFolder);
						if(strpos($sType, 'locales_') === 0) {
							foreach(TranslationsMgr::getLangAvailableBySide(TranslationsMgr::TRANS_BACK) as $sLang) {
								if($oZip->addEmptyDir($sParentFolder.'/'.$sLang)) {
									foreach(new DirectoryIterator($sDirPath.$sLang) as $oLangFileInfo) {
										if(!$oLangFileInfo->isDot()) {
											$oZip->addFile(
												$oLangFileInfo->getPathname(), 
												$sParentFolder.'/'.$sLang.'/'.basename($oLangFileInfo->getPathname())
											);
											$aMapping[$sType][] = $sParentFolder.'/'.$sLang.'/'.basename($oLangFileInfo->getPathname());
										}
									}
								}
							}
						} else {
							if(file_exists($oFileInfo->getPathname()) && $oFileInfo->isFile()) {
								$oZip->addFile(
									$oFileInfo->getPathname(), 
									$sParentFolder.'/'.basename($oFileInfo->getPathname())
								);
								$aMapping[$sType][] = basename($oFileInfo->getPathname());
							} elseif($oFileInfo->isDir()) {
								$sParentParentFolder = $sParentFolder.'/'.$oFileInfo->getBasename();
								$oZip->addEmptyDir($sParentParentFolder);
								foreach(new DirectoryIterator($oFileInfo->getPathname()) as $oDeepFileInfo) {
									if(file_exists($oDeepFileInfo->getPathname()) && $oDeepFileInfo->isFile()) {
										$oZip->addFile(
											$oDeepFileInfo->getPathname(), 
											$sParentParentFolder.'/'.basename($oDeepFileInfo->getPathname())
										);
									}
								}
							}
						}
					}
				}
			}
			//fichiers spécifiques
			foreach($aMapping['files'] as $sFilePath) {
				if(file_exists($sFilePath) && is_file($sFilePath)) {
					$oZip->addFile(
						$sFilePath, 
						basename($sFilePath)
					);
					$aMapping[$sType][] = basename($sFilePath);
				}
			}
			foreach($aMapping as $sType=>$aFiles) {
				$oZip->addFromString($sType.'.data', implode("\n", $aFiles));
			}
			$oZip->close();
			//mise à jour de la version
			ModulesMgr::setVersion($sModuleName, $sVersion);
			$oConf = new Config($sModuleName);
			$oConf->setGlobalConf('SYS_MOD_VERSION', $sVersion);
			unset($oConf);
			$this->sendState(true);
			$sMsg = SessionCore::getLangObject()->getMsg('system_updates', 'SUCCESS_GENERATE_UPDATES').' ';
			$sLinkDisplay = '('.$sZipFilename.')';
			$sMsg .= Toolz_Tpl::getA(WEB_PATH.'repository/'.$sZipFilename, $sLinkDisplay, '', '', true);
			UserRequest::$oAlertBoxMgr->success = $sMsg;
			return true;
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_updates', 'ERROR_GENERATE_UPDATES');
			return false;
		}
	}
	
	private function getMapping($sModuleName) {
		return ModulesMgr::isMinim($sModuleName) 
			? $this->getMinimFilesMapping() 
			: $this->getModuleFilesMapping($sModuleName);
	}
	
	private function getMinimFilesMapping() {
		return array(
			'dir' => array(
				'inc' => INC_PATH,
				'lib_Core' => CORE_CLASS_PATH,
				'lib_Modules' => MODULES_CLASS_PATH,
				'lib_Toolz' => TOOLZ_CLASS_PATH,
				'lib_interfaces' => CORE_INTERFACE_PATH,
				'locales_admin' => ADMIN_LOC_PATH,
				'locales_common' => COMMON_LOC_PATH,
				'tpl_admin' => ADMIN_TPL_PATH,
				'tpl_inc' => INC_TPL_PATH,
				'tpl_core' => CORE_TPL_PATH,
				'tpl_socialNetwork' => SOC_NET_TPL_PATH,
				'js' => JS_PATH
			),
			'files' => array(
				 DATA_PATH.'minim.routes.front.xml',
				 DATA_PATH.'minim.routes.back.xml',
				 DATA_PATH.'minim.conf.xml',
			)
		);
	}
	
	private function getModuleFilesMapping($sModuleName) {
		return array(
			'dir' => array(
				'lib' => MODULES_PATH.$sModuleName.'/'.GEN_LIB_PATH,
				'locales_admin' => MODULES_PATH.$sModuleName.'/'.GEN_ADMIN_LOC_PATH,
				'tpl_admin_contents' => MODULES_PATH.$sModuleName.'/'.GEN_ADMIN_TPL_CONTENTS_PATH,
				'tpl_admin_parts' => MODULES_PATH.$sModuleName.'/'.GEN_ADMIN_TPL_PARTS_PATH,
				'js' => MODULES_PATH.$sModuleName.'/'.GEN_JS_PATH
			),
			'files' => array(
				 MODULES_PATH.$sModuleName.'/'.GEN_DATA_PATH.$sModuleName.'.conf.xml',
				 MODULES_PATH.$sModuleName.'/'.GEN_DATA_PATH.$sModuleName.'.routes.back.xml',
			)
		);
	}
}