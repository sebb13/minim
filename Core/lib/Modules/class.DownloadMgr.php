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
final class DownloadMgr {
	
	private $aFiles = array();
	private $sCounterFilePath = '';
	private $sConfFilePath = '';
	private $sFilesPath = '';
	private $sCounterFilename = 'download.json';
	private $sConfFilename = 'files.json';
	private $oLang = NULL;
	public static $sModuleName = 'Download';
	
	public function __construct() {
		$this->sCounterFilePath = ModulesMgr::getFilePath(self::$sModuleName, 'data').$this->sCounterFilename;
		$this->sConfFilePath = ModulesMgr::getFilePath(self::$sModuleName, 'data').$this->sConfFilename;
		$this->aFiles = (array)json_decode(
							file_get_contents($this->sConfFilePath)
		);
		$this->sFilesPath = ModulesMgr::getFilePath(self::$sModuleName, 'data').'files/';
		$sLocPath = str_replace('/'.DEFAULT_LANG, '', ModulesMgr::getFilePath(self::$sModuleName, 'backLocales'));
		$this->oLang = new Lang(UserRequest::getLang(), DEFAULT_LANG, $sLocPath);
	}
	
	private function checkRight() {
		//your code here
		return true;
	}
	
	public function startDownload() {
		$sFileId = UserRequest::getRequest('file_id');
		if($this->checkRight()) {
			if (!isset($sFileId) 
				|| !array_key_exists($sFileId, $this->aFiles) 
				|| !file_exists($this->sFilesPath.$this->aFiles[$sFileId])) {
					UserRequest::setRequest(array('sPage'=>'404', 'sLang'=>DEFAULT_LANG));
					return true;
			}
			$sFilePath = $this->sFilesPath.$this->aFiles[$sFileId];
			$sFileName = basename($sFilePath);
			ini_set('zlib.output_compression', 0);
			//factory
			switch(strtolower(pathinfo($sFilePath, PATHINFO_EXTENSION))) {
				case 'txt':
				case 'md':
				case 'sql':
					header("Content-Type: text/plain");
					break;
				case 'xml':
					header("Content-type: text/xml");
					break;
				case 'html':
				case 'hml':
					header("Content-type: text/html");
					break;
				case 'json':
					header("Content-type: application/json");
					break;
				case 'pdf':
					header("Content-Type: application/pdf");
					break;
				case 'jpg':
				case 'jpeg':
					header("Content-Type: image/jpg");
					break;
				case 'png':
					header("Content-Type: image/png");
					break;
				case 'gif':
					header("Content-Type: image/gif");
					break;
				case 'zip':
					header("Content-Type: application/zip");
					break;
				case 'rar':
					header("Content-Type: application/rar");
					break;
				case 'tar':
					header("Content-Type: application/tar");
					break;
				case 'gz':
				case 'tgz':
				case 'gz2':
					header("Content-Type: application/tar+gzip");
					break;
				default:
					header("Content-Type: application/octet-stream");
			}
			//stats
			// no stat from test
			if(!ADMIN) {
				$this->counterUpdate($this->aFiles, $sFileId);
			}
			//fire
			header('Pragma: public');
			header("Expires: 0"); // obligé
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // obligé
			header("Content-Type: image/jpg");
			header('Content-Type: application/octetstream; name="'.$sFileName.'"');
			header("Content-Disposition: attachment; filename=\"".$sFileName."\";" );
			header('Content-MD5: '.base64_encode(md5_file($sFilePath)));
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($sFilePath));
			header('Date: '.gmdate(DATE_RFC1123));
			header('Expires: '.gmdate(DATE_RFC1123, time()+1));
			header('Last-Modified: '.gmdate(DATE_RFC1123, filemtime($sFilePath)));
			readfile($sFilePath);
		}
		die();
	}
	
	private function counterUpdate(array $aFiles, $sFileId) {
		// et les ips ??
		$aCounterFiles = array();
		if(!file_exists($this->sCounterFilePath)) {
			foreach($aFiles as $sId=>$sPath) {
				if($sId === $sFileId) {
					$aCounterFiles[$sId] = '1';
				} else {
					$aCounterFiles[$sId] = '0';
				}
			}
		} else {
			$aCounterFilesTmp = json_decode(
									file_get_contents($this->sCounterFilePath)
			);
			foreach($aFiles as $sId=>$sPath) {
				if(!isset($aCounterFilesTmp->$sId)) {
					$aCounterFiles[$sId] = '1';
				} else {
					foreach($aCounterFilesTmp as $sId=>$sCount) {
						if($sId === $sFileId) {
							$aCounterFiles[$sId] = (int)$sCount+1;
						} else {
							$aCounterFiles[$sId] = $sCount;
						}
					}
				}
			}
		}
		return file_put_contents(
							$this->sCounterFilePath, 
							json_encode($aCounterFiles)
						);
	}
	
	public function getHomePage() {
		$oConfig = new Config(self::$sModuleName);
		$sConfInterface = $oConfig->getConfInterface(self::$sModuleName, 'Download::getHomePage');
		$sTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backContentsTpl').'home.tpl');
		return str_replace(
						array(
							'{__STATS__}', 
							'{__CONFIG__}',
						), 
						array(
							$this->getDownloadStats(3),
							$sConfInterface,
						), 
						$sTpl
					);
	}
	
	public function getDownloadStats($iCol) {
		$sStatsTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'stats.tpl');
		return str_replace('{__STATS__}', $this->getDownloadRawStats($iCol), $sStatsTpl);
	}
	
	public function getDownloadRawStats($iCol) {
		$aCounterFiles = $this->getStatsInArray();
		$sItemTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'stats.item.tpl');
		$sStats = '';
		foreach($aCounterFiles as $sFileId=>$sCount) {
			$sStats .= str_replace(
				array('{__COL__}','{__FILE_ID__}', '{__COLOR__}', '{__COUNT__}'), 
				array($iCol, $sFileId, $sCount==='0'?'red':'green', $sCount), 
				$sItemTpl
			);
		}
		return $sStats;
	}
	
	private function getStatsInArray() {
		return (array)json_decode(file_get_contents($this->sCounterFilePath));
	}
	
	private function saveCounterFile(array $aProps) {
		return file_put_contents($this->sCounterFilePath, json_encode($aProps));
	}
	
	private function addToCounter($sFileId) {
		$aCounterFiles = $this->getStatsInArray();
		$aCounterFiles[$sFileId] = '0';
		return $this->saveCounterFile($aCounterFiles);
	}
	
	private function removeToCounter($sFileId) {
		$aCounterFiles = $this->getStatsInArray();
		if(isset($aCounterFiles[$sFileId])) {
			unset($aCounterFiles[$sFileId]);
		}
		return $this->saveCounterFile($aCounterFiles);
	}
	
	public function getManageFilesPage() {
		$sFileInputs = '';
		$sItemTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'manageFiles.item.tpl');
		foreach($this->aFiles as $sFileId=>$sFilename) {
			$sFileInputs .= str_replace(
				array('{__FILE_ID__}','{__FILE_NAME__}'), 
				array($sFileId,$sFilename),
				$sItemTpl
			);
		}
		return str_replace(
					array(
						'{__FILES__}', 
						'{__FILE_ID_TOOLTIP__}'
					), 
					array(
						$sFileInputs, 
						Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag('FILE_ID'))
					), 
					file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backContentsTpl').'manageFiles.tpl')
		);
	}
	
	private function checkFile($sFileId='') {
		if(empty($sFileId)) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('download', 'ERROR_MISSING_FILE_ID');
			return false;
		}
		if(isset($this->aFiles[$sFileId])) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('download', 'ERROR_FILE_ID_ALREADY_EXIST');
			return false;
		}
		if(is_numeric($sFileId[0])) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('download', 'ERROR_FILE_ID_MUST_BEGIN_WITH_LETTER');
			return false;
		}
		preg_match("/([^A-Za-z0-9])/",$sFileId,$mResult);
		if(!empty($mResult)){//si on trouve des caractère autre que A-Za-z ou 0-9
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('download', 'ERROR_FILE_ID_MUST_CONTAIN_ONLY_NUMS_AND_LETTERS');
			return false;
		}
		return true;
	}
	
	public function addFile() {
		if(!$this->checkFile(UserRequest::getParams('addFileId'))) {
			return false;
		}
		$aFile = UserRequest::getFiles();
		$sFileTmp = $aFile['addFileInput']['tmp_name'];
		if (!empty($sFileTmp)) {
			$aFileFormated['addFileInput'] = $aFile['addFileInput']['name'];
			$sFileErrorMsg = $aFile['addFileInput']['error'];
			if(!move_uploaded_file($sFileTmp, $this->sFilesPath.$aFileFormated['addFileInput'])){
				$aFileFormated['addFileInput'] = '';
				UserRequest::$oAlertBoxMgr->danger = $sFileErrorMsg;
				return true;
			}
		} else {
			$aFileFormated['addFileInput'] = '';
		}
		$this->aFiles[UserRequest::getParams('addFileId')] = $aFileFormated['addFileInput'];
		$this->setConf();
		$this->addToCounter(UserRequest::getParams('addFileId'), $aFileFormated['addFileInput']);
		UserRequest::$oAlertBoxMgr->success = $this->oLang->getMsg('download', 'SUCCESS_ADD_FILE');
		return true;
	}
	
	public function setConf() {
		return file_put_contents(
						$this->sConfFilePath, 
						json_encode($this->aFiles)
					);
	}
	
	public function deleteFile($sFileId) {
		if(!file_exists($this->sFilesPath.$this->aFiles[$sFileId])) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('download', 'ERROR_DELETE_FILE');
			return false;
		}
		if(unlink($this->sFilesPath.$this->aFiles[$sFileId])) {
			unset($this->aFiles[$sFileId]);
			$this->setConf();
			$this->removeToCounter($sFileId);
			UserRequest::$oAlertBoxMgr->success = $this->oLang->getMsg('download', 'SUCCESS_DELETE_FILE');
		} else {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('download', 'ERROR_DELETE_FILE');
			return false;
		}
	}
	
	public function __destruct() {
		$this->oLang = NULL;
		return $this->setConf();
	}
}