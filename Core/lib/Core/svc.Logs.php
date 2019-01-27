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
final class Logs extends CoreCommon {
	
	private $oErrorLogs = null;
	
	public function __construct() {
		$this->oErrorLogs = new ErrorLogs();
		parent::__construct();
	}
	
	public function getDaysLogs() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		return $this->getCoreResult($this->oErrorLogs->getDaysLogs());
	}
	
	public function getErrorLogs() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		UserRequest::startBenchmark('getErrorLogs');
		if(($sDay = UserRequest::getParams('sDay')) !== false) {
			$sLogFilePath = LOG_PATH.'log_'.str_replace('-', '', $sDay).'.log';
			$sContent = $this->oErrorLogs->getDaysLogs($sLogFilePath);
		} else {
			$sContent = $this->oErrorLogs->getErrorLogs();
		}
		return array(
					'content' => $this->oTplMgr->buildSimpleCacheTpl(
													str_replace(
														'{__BENCHMARK__}', 
														UserRequest::stopBenchmark('getErrorLogs', true),
														$sContent
													), 
													ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_errorLogs.xml'
												),
					'sPage'	=> 'system_errorLogs'
				);
	}
	
	public function purgeOldLogs() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		try {
			$this->oErrorLogs->purgeOldLogs();
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_errorLogs', ErrorLogs::PURGE_OLD_LOGS_SUCCESS);
		} catch (GenericException $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_errorLogs', ErrorLogs::PURGE_OLD_LOGS_ERROR);
		}
		return $this->getErrorLogs();
	}
	
	public function getJsonForCharts() {
		$aDataTmp = $this->oErrorLogs->getNbErrorsInArray();
		$aX = array('x');
		$aData = array('nbErrors');
		$sJson = json_encode(
					array(
						array_merge($aX, array_keys($aDataTmp)), 
						array_merge($aData, array_values($aDataTmp))
					)
				);
		die($sJson);
	}
	
	public function __destruct() {
		$this->oErrorLogs = null;
	}
}