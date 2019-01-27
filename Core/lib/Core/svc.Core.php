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
final class Core extends CoreCommon {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function logout() {
		return AdminAuthMgr::logout();
	}
	
	public function login() {
		return AdminAuthMgr::getLoginForm();
	}
	
	public function getCoreInfos() {
		return array(
				'{__PHP_VERSION__}' => phpversion(),
				'{__MINIM_VERSION__}' => trim(file_get_contents(DATA_PATH.'minim.version'))
		);
	}
	
	public function getMinimPage() {
		$sContents = str_replace(
								array_keys($this->getCoreInfos()), 
								array_values($this->getCoreInfos()), 
								file_get_contents(ADMIN_CONTENT_TPL_PATH.'minim.tpl')
							);
		return array(
					'content' => $this->oTplMgr->buildSimpleCacheTpl(
											$sContents, 
											ADMIN_LOC_PATH.$this->oLang->LOCALE.'/minim.xml'
					),
					'sPage' => UserRequest::getPage()
				);
	}
	
	public function getHomePage() {
		UserRequest::startBenchmark('dashboard');
		//$oUpdateMgr = new UpdateMgr();
		//$oUpdateMgr->sendState();
		//unset($oUpdateMgr);
		$oModuleMgr = new ModulesMgr();
		$sDashboard = '';
		foreach($oModuleMgr->getModulesAvailable() as $sModuleName) {
			try {	
				$oModule = new $sModuleName();
				if(method_exists($oModule, 'getDashboard')) {
					$sDashboard .= $oModule->getDashboard();
				}
				unset($oModule);
			} catch(Exception $e) {
				
			}
		}
		$sContent = str_replace(
					array(
						'{__SITE_URL__}', 
						'{__DASHBOARD__}', 
						'{__BENCHMARK__}'
					),
					array(
						DEV ? SITE_URL_DEV : SITE_URL_PROD,
						$sDashboard, 
						UserRequest::stopBenchmark('dashboard', true)
					),
					file_get_contents(ADMIN_CONTENT_TPL_PATH.'home.tpl')
				);
		return $this->oTplMgr->buildSimpleCacheTpl(
											$sContent, 
											ADMIN_LOC_PATH.$this->oLang->LOCALE.'/home.xml'
					);
	}
	
	public function sessionGC() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		$iFiles = 0;
		foreach(scandir(SESSION_PATH) as $sSessionFilePath) {
			if (!in_array($sSessionFilePath, Toolz_Main::$aScandirIgnore)) {
				if (time() > strtotime('+2 days', filemtime(SESSION_PATH.'/'.$sSessionFilePath))) {
					unlink(SESSION_PATH.'/'.$sSessionFilePath);
					$iFiles++;
				}
			}
		}
		$sContent = (string)$iFiles.$this->oLang->getMsg('core', 'X_FILES_DELETED');
		return $this->getCoreResult($sContent);
	}
	
	private function getStaticRouting() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		$sTpl = file_get_contents(TPL_PATH.'admin/parts/routing.summary.item.tpl');
		$oRoutingMgr = new RoutingMgr();
		$aFront = $oRoutingMgr->getAllRoutes('front');
		$aBack = $oRoutingMgr->getAllRoutes('back');
		ksort($aFront);
		ksort($aBack);
		unset($oRoutingMgr);
		$aStaticRouting = array(
							'front'=>array(), 
							'back'=>array()
						);
		foreach($aFront as $sPage=>$sRoute) {
			$sItem = str_replace(
							array('{__PAGE__}','{__ROUTE__}'), 
							array(str_replace('_', '/', $sPage).'.html',$sRoute), 
							$sTpl
						);
			$aStaticRouting['front'][] = $sItem;
		}
		foreach($aBack as $sPage=>$sRoute) {
			$sItem = str_replace(
							array('{__PAGE__}','{__ROUTE__}'), 
							array(str_replace('_', '/', $sPage).'.html',$sRoute), 
							$sTpl
						);
			$aStaticRouting['back'][] = $sItem;
		}
		return $aStaticRouting;
	}
	
	public function getSystemPage() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		$aRouting = $this->getStaticRouting();
		if(count($aRouting['front']) === 0) {
			$aRouting['front'][] = '-';
		}
		$oErrorLogs = new ErrorLogs();
		$sSysAdminLink = WEB_PATH.'?'.hash('crc32b', UserMgr::$SysAdmin).'='.hash('crc32b', date('YmdH'));
		$oFile = new SplFileInfo(DATA_PATH.'codeCounter.html');
		if($oFile->getMTime() < strtotime("-1 day")) {
			$this->resetCodeCounterCache();
		}
		$sContent = str_replace(
							array(
								'{__NB_ERRORS__}',
								'{__CODE_LINES__}',
								'{__BENCHMARK_VALUE__}',
								'{__SYS_ADMIN_LINK__}',
								'{__ROUTING_FRONT__}',
								'{__ROUTING_BACK__}'
							),
							array(
								$oErrorLogs->getNbErrorsFromDaysLog(),
								file_get_contents(DATA_PATH.'codeCounter.html'),
								UserRequest::stopBenchmark(),
								$sSysAdminLink,
								implode('', $aRouting['front']),
								implode('', $aRouting['back']),
							),
							file_get_contents(ADMIN_CONTENT_TPL_PATH.'system_summary.tpl')
				);
		return array(
					'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$sContent, 
															ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_summary.xml'
														),
					'sPage'	=> 'system_summary'
				);
	}
	
	private function resetCodeCounterCache() {
		$aExtAllowed = array(
							'php'=>0, 
							'js'=>0, 
							'css'=>0, 
							'xml'=>0, 
							'tpl'=>0,
							'html'=>0
						);
		foreach(Toolz_FileSystem::getRecursivePathList(ROOT_PATH) as $sFilePath) {
			$oFile = new SplFileInfo($sFilePath);
			if(isset($aExtAllowed[$oFile->getExtension()])) {
				$aFile = file($sFilePath);
				$aExtAllowed[$oFile->getExtension()] += count($aFile);
			}
		}
		$sCodeCounter = '';
		foreach($aExtAllowed as $sExt=>$sNbLines) {
			$sCodeCounter .= $sExt.' &#x21E8; '.$sNbLines.'<br />';
		}
		return file_put_contents(DATA_PATH.'codeCounter.html', $sCodeCounter);
	}
	
	public function CookiesConsent() {
		return CookiesConsent::setCookiesConsent();
	}
	
	public function getCookiesConsentBanner() {
		return $this->oTplMgr->buildSimpleCacheTpl(
										CookiesConsent::getCookiesConsentBanner(), 
										COMMON_LOC_PATH.UserRequest::getRequest('sLang').'/common.xml'
									);
	}
}