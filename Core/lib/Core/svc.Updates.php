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
final class Updates extends CoreCommon {
	
	private $oUpdateMgr = NULL;
	
	public function __construct() {
		parent::__construct();
		$this->oUpdateMgr = new UpdateMgr();
	}
	
	public function getHomePage() {
		UserRequest::startBenchmark('Updates');
		if(WEB_PATH === 'https://dev-admin.minim.webearthquake.com/' || WEB_PATH === 'https://admin.minim.webearthquake.com/') {
			$sContent = str_replace(
					array(
						'{__UPDATES__}',
						'{__REPOSITORY__}',
						'{__WEBSITES__}',
						'{__BENCHMARK__}'
					), 
					array(
						$this->oUpdateMgr->getRepositoryInterface(),
						$this->oUpdateMgr->getRepositoryState(),
						$this->oUpdateMgr->getWebsitesState(),
						UserRequest::stopBenchmark('Updates', true)
					), 
					file_get_contents(ADMIN_CONTENT_TPL_PATH.'system_updates.repository.tpl')
				);
		} else {
			$sContent = str_replace(
					array(
						'{__UPDATES__}',
						'{__BENCHMARK__}'
					), 
					array(
						$this->oUpdateMgr->getUpdatesInterface(), 
						UserRequest::stopBenchmark('Updates', true)
					), 
					file_get_contents(ADMIN_CONTENT_TPL_PATH.'system_updates.tpl')
				);
		}
		$oTplMgr = new TplMgr($this->oLang);
		return $oTplMgr->buildSimpleCacheTpl(
										$sContent, 
										ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_updates.xml'
				);
	}
	
	public function checkUpdate() {
		$sModuleName = UserRequest::getParams('sModuleToCheck');
		if($this->oUpdateMgr->hasNewReleases($sModuleName)) {
			$sMsg = SessionCore::getLangObject()->getMsg('system_updates', 'DOWNLOADED_UPDATES');
		} else {
			$sMsg = SessionCore::getLangObject()->getMsg('system_updates', 'NO_UPDATE_AVAILABLE');
		}
		
		UserRequest::$oAlertBoxMgr->success = $sMsg.' ('.$sModuleName.')';
		return $this->getHomePage();
	}
	
	public function installUpdates() {
		$this->oUpdateMgr->installUpdates(UserRequest::getParams('sModuleName'));
		return $this->getHomePage();
	}
	
	public function rollback() {
		$this->oUpdateMgr->rollback(
								UserRequest::getParams('sModuleName'), 
								UserRequest::getParams('sVersion')
							);
		return $this->getHomePage();
	}
	
	public function purgeBackup() {
		$this->oUpdateMgr->purgeBackup(
								UserRequest::getParams('sModuleName')
							);
		return $this->getHomePage();
	}
	
	public function generateUpdates() {
		$sMainVersion = !UserRequest::getParams('sMainVersion') ? '0' : UserRequest::getParams('sMainVersion');
		$sSubVersion = !UserRequest::getParams('sSubVersion') ? '0' : UserRequest::getParams('sSubVersion');
		$sRelease = !UserRequest::getParams('sRelease') ? '0' : UserRequest::getParams('sRelease');
		$sModuleName = UserRequest::getParams('sModuleName');
		$sVersion = $sMainVersion.'.'.$sSubVersion.'.'.$sRelease;
		$this->oUpdateMgr->buildUpdatePackages($sVersion, $sModuleName);
		return $this->getHomePage();
	}
	
	public function checkRepository() {
		return $this->oUpdateMgr->hasUpdates(
			UserRequest::getParams('sModuleName'),
			UserRequest::getParams('sVersion')
		);
	}
	
	public function getRelease() {
		die(file_get_contents(REPOSITORY_PATH.UserRequest::getParams('sRelease')));
	}
	
	public function sendSiteState() {
		return $this->oUpdateMgr->setWebsitesState(
			UserRequest::getParams('sSiteUrl'),
			UserRequest::getParams('sVersions')
		);
	}
	
	public function __destruct() {
		$this->oUpdateMgr = null;
	}
}