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
final class Configuration extends CoreCommon {
	
	public function __construct() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		parent::__construct();
	}
	
	public function getGlobalConfPage() {
		UserRequest::startBenchmark('getConfInterface');
		$oConfig = new Config('minim');
		$sContents = '';
		foreach(ModulesMgr::getModulesAvailable() as $sModuleName) {
			try {
				$sContents .= $oConfig->getConfInterface($sModuleName);
			} catch (CoreException $e) {
				UserRequest::$oAlertBoxMgr->danger = $e->getMessage();
			}
		}
		unset($oConfig);
		$sContents .= UserRequest::stopBenchmark('getConfInterface', true);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$sContents, 
															ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_conf.xml'
														),
				'sPage'	=> 'system_conf'
			);
	}
	
	public function saveGlobalConf() {
		$oConfig = new Config(UserRequest::getParams('moduleToUpdate'));
		$aConf = UserRequest::getParams();
		if(!empty($aConf['callback'])) {
			$sCallback = $aConf['callback'];
		}
		unset(
			$aConf['exw_action'], 
			$aConf['app_token'], 
			$aConf['moduleToUpdate'],
			$aConf['callback']
		);
		$sModuleToDisplay = ' ('.UserRequest::getParams('moduleToUpdate').')';
		if ($oConfig->saveGlobalConf($aConf)) {
			$sMsg = SessionCore::getLangObject()->getMsg('system_conf', 'SUCCESS_CONFIGURATION_SAVE');
			UserRequest::$oAlertBoxMgr->success = $sMsg.$sModuleToDisplay;
		} else {
			$sMsg = SessionCore::getLangObject()->getMsg('system_conf', 'ERROR_CAN_NOT_SAVE');
			UserRequest::$oAlertBoxMgr->danger = $sMsg.$sModuleToDisplay;
		}
		if(isset($sCallback)) {
			list($sClassName, $sMethodName) = explode('::', $sCallback);
			$oCallback = new $sClassName();
			return $oCallback->$sMethodName();
		} else {
			return $this->getGlobalConfPage();
		}
	}
}