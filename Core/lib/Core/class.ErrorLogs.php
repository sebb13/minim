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
final class ErrorLogs {
	
	private $sDaysLogFilePath		= '';
	private $sErrorItemTplName		= 'errorLogs.item.tpl';
	private $sErrorLogsTplName		= 'system_errorLogs.tpl';
	private $sEndOfLogPatern		= "\nEND OF LOG";
	const PURGE_OLD_LOGS_SUCCESS	= 'PURGE_OLD_LOGS_SUCCESS';
	const PURGE_OLD_LOGS_ERROR		= 'PURGE_OLD_LOGS_ERROR';
	
	public function __construct() {
		$this->sDaysLogFilePath = LOG_PATH.'log_'.date('Ymd').'.log';
	}
	
	public function getNbErrorsFromDaysLog($sLogsFile='') {
		if(empty($sLogsFile)) {
			$sLogsFile = $this->sDaysLogFilePath;
			$sDate = date('Ymd');
		} else {
			$sDate = str_replace('log_', '', basename($sLogsFile, '.log'));
		}
		$iErrors = 0;
		if(file_exists($sLogsFile)) {
			$aLogs = file($sLogsFile);
			foreach($aLogs as $sLine) {
				if(strpos($sLine, 'END OF LOG') !== false) {
					$iErrors++;
				}
			}
		}
		return $iErrors;
	}
	
	public function getDaysLogs($sLogsFile='') {
		if(empty($sLogsFile)) {
			$sLogsFile = $this->sDaysLogFilePath;
		}
		if(file_exists($sLogsFile)) {
			$aLogs = explode($this->sEndOfLogPatern, file_get_contents($sLogsFile));
			$sLogs = '';
			$sLogContainer = file_get_contents(ADMIN_PARTS_TPL_PATH.'errorLog.box.tpl');
			foreach($aLogs as $sLog) {
				$sMsg = substr($sLog, 0, strpos($sLog, 'Stack trace:'));
				$sTrace = substr($sLog, strpos($sLog, 'Stack trace:'));
				if(!empty($sMsg) && !empty($sTrace)) {
					$sLogs .= str_replace(
						array(
							'{__MSG__}',
							'{__TRACE__}'
						), 
						array(
							nl2br($sMsg), 
							nl2br($sTrace)
						), 
						$sLogContainer
					);
				}
			}
			return $sLogs;
		} else {
			return '0 '.SessionCore::getLangObject()->getMsg('system_summary', 'NB_ERRORS_WORDING');
		}
	}
	
	public function getDebugEnv($bForce=false) {
		$sDev = DEV ? 'true' : 'false';
		$sTest = TEST ? 'true' : 'false';
		$sAdmin = ADMIN ? 'true' : 'false';
		$aReturn = array(
					0 => 'TIME............. '.date('Ymd H:i:s'),
					1 => 'PHP_VERSION...... '.phpversion(),
					2 => 'DEV.............. '.$sDev,
					3 => 'TEST............. '.$sTest,
					4 => 'ADMIN............ '.$sAdmin,
					5 => 'WEB_PATH......... '.WEB_PATH,
					6 => 'DEFAULT_LANG..... '.DEFAULT_LANG,
					7 => 'sLang............ '.UserRequest::getLang(),
					8 => 'sPage............ '.UserRequest::getPage(),
					9 => 'HTTP_USER_AGENT.. '.UserRequest::getEnv('HTTP_USER_AGENT'),
					10 => 'IP............... '.UserRequest::getEnv('REMOTE_ADDR'),
					11 => ''
				);
		return dexad('DEV', false)||$bForce ? "\n".implode("\n", $aReturn) : '';
	}

	public function addLog($sString) {
		$sString .= $this->sEndOfLogPatern;
		if(file_exists($this->sDaysLogFilePath)) {
			$sString = $sString.file_get_contents($this->sDaysLogFilePath);
		}
		return file_put_contents($this->sDaysLogFilePath, $sString);
	}
	
	private function getLogDate($sLogsFile, $sSep='') {
		$sDate = str_replace('log_', '', basename($sLogsFile, '.log'));
		if(!empty($sSep)) {
			$sDate = substr($sDate, 0, 4).$sSep.substr($sDate, 4, 2).$sSep.substr($sDate, 6, 2);
		}
		return $sDate;
	}
	
	private function getLogTimeStamp($sDate) {
		return mktime(0, 0, 0, substr($sDate, 4, 2), substr($sDate, 6, 2), substr($sDate, 0, 4));
	}
	
	public function purgeOldLogs() {
		$oConfig = new Config();
		$aConf = $oConfig->getGlobalConf();
		unset($oConfig);
		foreach(scandir(LOG_PATH) as $sLogsFile) {
			if(!in_array($sLogsFile, Toolz_Main::$aScandirIgnore) && is_file(LOG_PATH.$sLogsFile)) {
				if($this->getLogTimeStamp($this->getLogDate($sLogsFile)) < strtotime($aConf['LOGS_TTL'])) {
					unlink(LOG_PATH.$sLogsFile);
				}
			}
		}
		return true;
	}
	
	public function getNbErrorsInArray() {
		$aErrors = array();
		foreach(scandir(LOG_PATH) as $sLogsFile) {
			if(!in_array($sLogsFile, Toolz_Main::$aScandirIgnore) && is_file(LOG_PATH.$sLogsFile)) {
				$sLabel = $this->getLogDate($sLogsFile, '-');
				$aErrors[$sLabel] = (int)$this->getNbErrorsFromDaysLog(LOG_PATH.$sLogsFile);
			}
		}
		ksort($aErrors);
		end($aErrors);
		$iEnd = (int)str_replace('-', '', key($aErrors));
		reset($aErrors);
		$iStart = (int)str_replace('-', '', key($aErrors));
		$start = DateTime::createFromFormat('Ymd', $iStart); // DateTime::createFromFormat('d/m/Y', '15/06/2015');
		$end = DateTime::createFromFormat('Ymd', $iEnd); // DateTime::createFromFormat('d/m/Y', '21/06/2015');
		foreach (new DatePeriod($start, DateInterval::createFromDateString('1 day'), $end) as $dt) {
			$sKey = $dt->format('Y-m-d');
			if(empty($aErrors[$sKey])) {
				$aErrors[$sKey] = 0;
			}
		}
		ksort($aErrors);
		return $aErrors;
	}
	
	public function getErrorLogs() {
		$sErrorItemTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sErrorItemTplName);
		$sContent = '';
		$aErrors = array_reverse($this->getNbErrorsInArray());
		foreach($aErrors as $sLogsFile=>$sNbErrors) {
			$sContent .= str_replace(
									array(
										'{__LOGS_DATE__}',
										'{__NB_ERRORS__}'
									), 
									array(
										$sLogsFile,
										$sNbErrors
									), 
									$sErrorItemTpl
								);
		}
		return str_replace(
					'{__LOGS_LIST__}', 
					$sContent,
					file_get_contents(ADMIN_CONTENT_TPL_PATH.$this->sErrorLogsTplName)
				);
	}
}