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
final class ContactMgr extends ContactModel {
	
	private $sContactFrontPageTpl	= 'contact.tpl';
	private $sUploadPath			= '';
	private $oLang					= NULL;
	private $aConfig				= array();
	public static $sModuleName		= 'Contact';
	const ERROR_SEND_MSG			= 'ERROR_SEND_MSG';
	const ERROR_MISSING_NAME		= 'ERROR_MISSING_NAME';
	const ERROR_MISSING_EMAIL		= 'ERROR_MISSING_EMAIL';
	const ERROR_INVALID_EMAIL		= 'ERROR_INVALID_EMAIL';
	const ERROR_MISSING_MSG			= 'ERROR_MISSING_MSG';
	const SUCCESS_SEND_MSG			= 'SUCCESS_SEND_MSG';
	const SUCCESS_ARCHIVE_MSG		= 'SUCCESS_ARCHIVE_MSG';
	const ERROR_ARCHIVE_MSG			= 'ERROR_ARCHIVE_MSG';
	const SUCCESS_RESTORE_MSG		= 'SUCCESS_RESTORE_MSG';
	const ERROR_RESTORE_MSG			= 'ERROR_RESTORE_MSG';
	const SUCCESS_DELETE_MSG		= 'SUCCESS_DELETE_MSG';
	const ERROR_DELETE_MSG			= 'ERROR_DELETE_MSG';
	const SUCCESS_SAVE_COMMENT		= 'SUCCESS_SAVE_COMMENT';
	const ERROR_SAVE_COMMENT		= 'ERROR_SAVE_COMMENT';
	
	public function __construct(){
		$oConfig = new Config(self::$sModuleName);
		$this->aConfig = $oConfig->getGlobalConf();
		unset($oConfig);
		$bDb = $this->aConfig['DATA_FORMAT'] === 'SQL';
		parent::__construct($bDb);
		$this->oLang = SessionCore::getLangObject();
		$this->sUploadPath = ModulesMgr::getFilePath(self::$sModuleName, 'data').'files/';
	}
	
	public function getFrontPage(array $aMsgData=array()) {
		$sTplPath = ModulesMgr::getFilePath(self::$sModuleName, 'frontContentsTpl').$this->sContactFrontPageTpl;
		return str_replace(
						array(
							'{__NAME__}',
							'{__EMAIL__}',
							'{__SUBJECT__}',
							'{__MSG__}',
							'{__ERROR_MISSING_NAME__}',
							'{__ERROR_MISSING_EMAIL__}',
							'{__ERROR_INVALID_EMAIL__}',
							'{__ERROR_MISSING_MSG__}'
						), 
						array(
							!empty($aMsgData['contact_name']) ? $aMsgData['contact_name'] : '',
							!empty($aMsgData['contact_email']) ? $aMsgData['contact_email'] : '',
							!empty($aMsgData['contact_subject']) ? $aMsgData['contact_subject'] : '',
							!empty($aMsgData['contact_msg']) ? $aMsgData['contact_msg'] : '',
							$this->oLang->getMsg('contact', self::ERROR_MISSING_NAME),
							$this->oLang->getMsg('contact', self::ERROR_MISSING_EMAIL),
							$this->oLang->getMsg('contact', self::ERROR_INVALID_EMAIL),
							$this->oLang->getMsg('contact', self::ERROR_MISSING_MSG)
						),
						file_get_contents($sTplPath)
					);
	}
	
	private function checkMsg(array $aMsgData) {
		// spam
		/*
		if ($_SERVER['HTTP_REFERER'] !== 'http://www.mondomaine.tld/contact'){
			$message = 'Désolé, une erreur est survenue...';
		}
		 *
		 */
		try {
			Toolz_Checker::checkParams(array(
									'required'	=> array(
													'contact_name',
													'contact_email', 
													'contact_msg'
												),
									'data'	=> $aMsgData
								));
		} catch(Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_SEND_MSG).'checkParams';
			return false;
		}
		// robot bien sage
		if ($aMsgData['contact_email_2'] !== ''){
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_SEND_MSG).'robot';
			return false;
		}
		// mandatory fields
		$iMandatory = 0;
		if(empty($aMsgData['contact_name'])) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_MISSING_NAME);
			$iMandatory++;
		}
		if(empty($aMsgData['contact_email'])) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_MISSING_EMAIL);
			$iMandatory++;
		}
		if(!Toolz_Checker::checkMail($aMsgData['contact_email'])) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_INVALID_EMAIL);
			$iMandatory++;
		}
		if(empty($aMsgData['contact_msg'])) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_MISSING_MSG);
			$iMandatory++;
		}
		if($iMandatory !== 0) {
			return false;
		}
		return array(
					'contact_name' => $aMsgData['contact_name'],
					'contact_email' => $aMsgData['contact_email'],
					'contact_subject' => $aMsgData['contact_subject'],
					'contact_msg' => $aMsgData['contact_msg'],
					'contact_file' => !empty($aMsgData['contact_file']) ? $aMsgData['contact_file'] : '',
					'contact_ip' => Session::getSubSession('system', 'HTTP_REMOTE_IP'),
					'contact_date' => time(),
					'contact_active' => 1,
			);
	}
	
	public function newMsg(array $aMsgData) {
		if(($aMsgFormated = $this->checkMsg($aMsgData)) !== false) {
			$sContactFilename = Toolz_FileSystem::uploadFile('contact_file', $this->sUploadPath);
			if($sContactFilename !== false) {
				$aMsgFormated['contact_file'] = $sContactFilename;
			} else {
				$aMsgFormated['contact_file'] = '';
			}
			$this->add($aMsgFormated);
			if(empty($this->aConfig['EMAIL_ALERT'])) {
				mail(
					EMAIL_CONTACT,
					'Un nouveau message sur '.WEB_PATH,
					'L\'adresse mail pour les nouveaux messages n\'est pas configurée !'
				);
			} else {
				mail(
					$this->aConfig['EMAIL_ALERT'],
					'Un nouveau message sur '.WEB_PATH,
					$aMsgData['contact_name'].' a envoyé un nouveau message !'
				);
			}
			$aMsgData=array();
			UserRequest::$oAlertBoxMgr->success = $this->oLang->getMsg('contact', self::SUCCESS_SEND_MSG);
		}
		return $this->getFrontPage($aMsgData);
	}
	
	public function getHomePage() {
		$oConfig = new Config(self::$sModuleName);
		$sConfInterface = $oConfig->getConfInterface(self::$sModuleName, 'Contact::getHomePage');
		$sSearchTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'header.form.tpl');
		$sTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backContentsTpl').'home.tpl');
		return str_replace(
						array(
							'{__HEADER__}', 
							'{__CONFIG__}',
							'{__KEYWORD__}'
						), 
						array(
							$sSearchTpl,
							$sConfInterface,
							''
						), 
						$sTpl
					);
	}
	
	public function getMsgsPage($bActive=true, $sKeyword='') {
		if(!empty($sKeyword)) {
			$aMsgs = $this->searchMsg($sKeyword);
		} else {
			$aMsgs = $this->getAll($bActive);
		}
		$sMsgs = '';
		$sMsgTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'msg.item.tpl');
		$sArchivedMsgTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'archived.msg.item.tpl');
		$sSearchTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'header.form.tpl');
		$sCommentTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'update.comment.tpl');
		$sCommentFormTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'add.comment.tpl');
		foreach($aMsgs as $aMsg) {
			if(!empty($aMsg['contact_comment'])) {
				$sComment = str_replace(
									array(
										'{__USER__}', 
										'{__CONTACT_COMMENT__}'
									), 
									array(
										$aMsg['contact_comment_user'],
										$aMsg['contact_comment']
									), 
									$sCommentTpl
								);
			} else {
				$sComment = str_replace(
									array('{__CONTACT_COMMENT__}', '{__MSG_ID__}'), 
									array('', $aMsg['contact_id']), 
									$sCommentFormTpl
								);
			}
			$sMsgs .= str_replace(
								array(
									'{__CONTACT_DATE__}',
									'{__CONTACT_NAME__}',
									'{__CONTACT_EMAIL__}',
									'{__CONTACT_SUBJECT__}',
									'{__CONTACT_FILE__}',
									'{__CONTACT_MSG__}',
									'{__COMMENT__}',
									'{__CONTACT_ID__}'
								), 
								array(
									$aMsg['contact_date'],
									$aMsg['contact_name'],
									$aMsg['contact_email'],
									!empty($aMsg['contact_subject']) ? $aMsg['contact_subject'] : '',
									!empty($aMsg['contact_file']) ? $aMsg['contact_file'] : '',
									$aMsg['contact_msg'],
									$sComment,
									$aMsg['contact_id']
								), 
								$bActive ? $sMsgTpl : $sArchivedMsgTpl
							);
		}
		$sPageTplPath = ModulesMgr::getFilePath(self::$sModuleName, 'backContentsTpl').'contact.tpl';
		$sPageTpl = file_get_contents($sPageTplPath);
		$sTitle = $bActive ? '{__MESSAGES_RECEIVED__}' : '{__ARCHIVED_MESSAGES__}';
		if(!empty($sKeyword)) {
			$sTitle = '{__MESSAGES_FOUND__}'.count($aMsgs).' ('.$sKeyword.')';
		}
		return str_replace(
						array(
							'{__HEADER__}',
							'{__KEYWORD__}',
							'{__CONTACT_LIST_TITLE__}',
							'{__MSGS__}'
						), 
						array(
							$sSearchTpl,
							$sKeyword,
							$sTitle,
							$sMsgs
						), 
						$sPageTpl
					);
	}
	
	public function getFile($sFilename) {
		$sFilePath = $this->sUploadPath.$sFilename;
		if (!file_exists($sFilePath)) {
			UserRequest::setRequest(array('sPage'=>'404', 'sLang'=>DEFAULT_LANG));
			return true;
		}
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
		//fire
		header('Pragma: public');
		header("Expires: 0"); // obligé
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // obligé
		header("Content-Type: image/jpg");
		header('Content-Type: application/octetstream; name="'.$sFilename.'"');
		header("Content-Disposition: attachment; filename=\"".$sFilename."\";" );
		header('Content-MD5: '.base64_encode(md5_file($sFilePath)));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($sFilePath));
		header('Date: '.gmdate(DATE_RFC1123));
		header('Expires: '.gmdate(DATE_RFC1123, time()+1));
		header('Last-Modified: '.gmdate(DATE_RFC1123, filemtime($sFilePath)));
		readfile($sFilePath);
		die();
	}
	
	public function saveUserComment($iMsgId, $sComment) {
		$sCommentTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'update.comment.tpl');
		$sCommentFormTpl = file_get_contents(ModulesMgr::getFilePath(self::$sModuleName, 'backPartsTpl').'add.comment.tpl');
		if($this->saveComment($iMsgId, $sComment)) {
			UserRequest::$oAlertBoxMgr->success = $this->oLang->getMsg('contact', self::SUCCESS_SAVE_COMMENT);
			return str_replace(
							array(
								'{__USER__}', 
								'{__CONTACT_COMMENT__}'
							), 
							array(
								SessionUser::get('user'),
								$sComment
							), 
							$sCommentTpl
						);
		} else {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_SAVE_COMMENT);
			return str_replace(
							array('{__CONTACT_COMMENT__}', '{__MSG_ID__}'), 
							array($sComment, $iMsgId), 
							$sCommentFormTpl
						);
		}
	}
	
	public function deleteMsg($iMsgId) {
		try {
			if(($aMsg = $this->getMsg($iMsgId)) !== false) {
				if(!empty($aMsg['contact_file']) && file_exists($this->sUploadPath.$aMsg['contact_file'])) {
					unlink($this->sUploadPath.$aMsg['contact_file']);
				}
				$this->delete($iMsgId);
			}
			UserRequest::$oAlertBoxMgr->success = $this->oLang->getMsg('contact', self::SUCCESS_DELETE_MSG);
			return true;
		} catch(Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_DELETE_MSG);
			return false;
		}
	}
	
	public function archiveMsg($iMsgId) {
		try {
			$this->archive($iMsgId);
			UserRequest::$oAlertBoxMgr->success = $this->oLang->getMsg('contact', self::SUCCESS_ARCHIVE_MSG);
			return true;
		} catch(Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_ARCHIVE_MSG);
			return false;
		}
	}
	
	public function restoreMsg($iMsgId) {
		try {
			$this->restore($iMsgId);
			UserRequest::$oAlertBoxMgr->success = $this->oLang->getMsg('contact', self::SUCCESS_RESTORE_MSG);
			return true;
		} catch(Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = $this->oLang->getMsg('contact', self::ERROR_RESTORE_MSG);
			return false;
		}
	}
}