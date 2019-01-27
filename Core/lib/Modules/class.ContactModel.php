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
abstract class ContactModel {
	
	private $oModel = NULL;
	
	public function __construct($bDb=true) {
		$this->oModel = $bDb ? new ContactSql() : new ContactXml();
	}
	
	public function add(array $aMsgData) {
		return $this->oModel->add($aMsgData);
	}
	
	public function searchMsg($sKeyword) {
		return $this->oModel->searchMsg($sKeyword);
	}
	
	public function getMsg($iMsgId) {
		return $this->oModel->getMsg($iMsgId);
	}
	
	public function getAll($bActive=true) {
		return $this->oModel->getAll($bActive);
	}
	
	public function saveComment($iMsgId, $sComment) {
		return $this->oModel->saveComment($iMsgId, $sComment);
	}
	
	public function archive($iMsgId) {
		return $this->oModel->archive($iMsgId);
	}
	
	public function delete($iMsgId) {
		return $this->oModel->delete($iMsgId);
	}
	
	public function restore($iMsgId) {
		return $this->oModel->restore($iMsgId);
	}
	
	public function getNbMsgs($bActive=true) {
		return $this->oModel->getNbMsgs($bActive);
	}
	
	public function getLastMessageDate() {
		return $this->oModel->getLastMessageDate();
	}
}

final class ContactSql implements iContactMgr {
	
	private $oPdo = NULL;
	private $sContactTableName = 't_contact';
	
	public function __construct() {
		$this->oPdo = SPDO::getInstance();
	}
	
	public function add(array $aMsgData) {
		try {
			$sQuery = 'INSERT INTO '.$this->sContactTableName.' (
						contact_name,
						contact_email,
						contact_subject,
						contact_msg,
						contact_file,
						contact_ip
					) VALUES (
						:contact_name,
						:contact_email,
						:contact_subject,
						:contact_msg,
						:contact_file,
						:contact_ip
					)';
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':contact_name', $aMsgData['contact_name'], PDO::PARAM_STR);
			$oQuery->bindParam(':contact_email', $aMsgData['contact_email'], PDO::PARAM_STR);
			$oQuery->bindParam(':contact_subject', $aMsgData['contact_subject'], PDO::PARAM_STR);
			$oQuery->bindParam(':contact_msg', $aMsgData['contact_msg'], PDO::PARAM_STR);
			$oQuery->bindParam(':contact_file', $aMsgData['contact_file'], PDO::PARAM_STR);
			$oQuery->bindParam(':contact_ip', $aMsgData['contact_ip'], PDO::PARAM_STR);
			$oQuery->execute();
			return $this->oPdo->lastInsertId();
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function searchMsg($sKeyword) {
		$sQuery = 'SELECT
						contact_id,
						contact_name,
						contact_email,
						contact_subject,
						contact_msg,
						contact_comment,
						contact_comment_user,
						contact_file,
						contact_ip,
						contact_date
					FROM '.$this->sContactTableName.'
					WHERE (contact_msg like CONCAT(\'%\', :keyword, \'%\') 
					OR contact_name  like CONCAT(\'%\', :keyword, \'%\') 
					OR contact_email  like CONCAT(\'%\', :keyword, \'%\') 
					OR contact_subject  like CONCAT(\'%\', :keyword, \'%\')) 
					AND contact_active = 1
					ORDER BY contact_date DESC';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':keyword', $sKeyword, PDO::PARAM_STR);
		$oQuery->execute();
		$aContacts = $oQuery->fetchAll(PDO::FETCH_ASSOC);
		if(count($aContacts) === 0) {
			return array();
		} else {
			return $aContacts;
		}
	}
	
	public function getMsg($iMsgId) {
		$sQuery = 'SELECT
						contact_id,
						contact_name,
						contact_email,
						contact_subject,
						contact_msg,
						contact_comment,
						contact_comment_user,
						contact_file,
						contact_ip,
						contact_date
					FROM '.$this->sContactTableName.'
					WHERE contact_id = :contact_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':contact_id', $iMsgId, PDO::PARAM_INT);
		$oQuery->execute();
		$aContacts = $oQuery->fetchAll(PDO::FETCH_ASSOC);
		if(count($aContacts) === 0) {
			return false;
		} else {
			return $aContacts[0];
		}
	}
	
	public function getAll($bActive=true) {
		$bActive = (int)$bActive;
		$sQuery = 'SELECT
						contact_id,
						contact_name,
						contact_email,
						contact_subject,
						contact_msg,
						contact_comment,
						contact_comment_user,
						contact_file,
						contact_ip,
						contact_date
					FROM '.$this->sContactTableName.'
					WHERE contact_active = :contact_active
					ORDER BY contact_date DESC';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':contact_active', $bActive, PDO::PARAM_INT);
		$oQuery->execute();
		$aContacts = $oQuery->fetchAll(PDO::FETCH_ASSOC);
		if(count($aContacts) === 0) {
			return array();
		} else {
			return $aContacts;
		}
	}
	
	public function saveComment($iMsgId, $sComment) {
		$sUser = SessionUser::get('user');
		$sQuery = 'UPDATE '.$this->sContactTableName.' SET 
				contact_comment = :contact_comment,
				contact_comment_user = :contact_comment_user
				WHERE contact_id = :contact_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':contact_comment', $sComment, PDO::PARAM_STR);
		$oQuery->bindParam(':contact_comment_user', $sUser, PDO::PARAM_STR);
		$oQuery->bindParam(':contact_id', $iMsgId, PDO::PARAM_INT);
		return $oQuery->execute();
	}
	
	public function delete($iMsgId) {
		$sQuery = 'DELETE FROM '.$this->sContactTableName.' 
				WHERE contact_id = :contact_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':contact_id', $iMsgId, PDO::PARAM_INT);
		return $oQuery->execute();
	}
	
	public function archive($iMsgId) {
		$sQuery = 'UPDATE '.$this->sContactTableName.' 
				SET contact_active = 0
				WHERE contact_id = :contact_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':contact_id', $iMsgId, PDO::PARAM_INT);
		return $oQuery->execute();
	}
	
	public function restore($iMsgId) {
		$sQuery = 'UPDATE '.$this->sContactTableName.' 
				SET contact_active = 1
				WHERE contact_id = :contact_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':contact_id', $iMsgId, PDO::PARAM_INT);
		return $oQuery->execute();
	}
	
	public function getNbMsgs($bActive=true) {
		$sQuery = 'SELECT
						count(contact_id)
					FROM '.$this->sContactTableName.'
					WHERE contact_active = :contact_active';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':contact_active', $bActive, PDO::PARAM_INT);
		$oQuery->execute();
		$aNbContacts = $oQuery->fetchAll(PDO::FETCH_COLUMN);
		return $aNbContacts[0];
	}
	
	public function getLastMessageDate() {
		$sQuery = 'SELECT
						contact_date
					FROM '.$this->sContactTableName.'
					ORDER BY contact_date DESC
					LIMIT 1';
		$oQuery = $this->oPdo->query($sQuery);
		$oQuery->execute();
		$aContactDate = $oQuery->fetchAll(PDO::FETCH_COLUMN);
		return $aContactDate[0];
	}
}

final class ContactXml extends SimpleXmlMgr implements iContactMgr  {
	
	private $sContactsFilename = 'contacts.xml';
	private $sContactFilePath = '';
	private $aMsgs = array();
	
	public function __construct() {
		$this->sContactFilePath = ModulesMgr::getFilePath(ContactMgr::$sModuleName, 'data').$this->sContactsFilename;
		parent::__construct($this->sContactFilePath);
		$this->aMsgs = $this->getIemsList();
	}
	
	public function add(array $aMsgData) {
		$aMsgData['contact_id'] = count($this->aMsgs);
		$this->aMsgs['item'.count($this->aMsgs)] = $aMsgData;
		return $this->saveContacts();
	}
	
	public function searchMsg($sKeyword) {
		$aMsgs = array();
		foreach($this->aMsgs as $aMsg) {
			if(isset($aMsg['contact_active']) && $aMsg['contact_active'] === '1') {
				if(strpos($aMsg['contact_msg'], $sKeyword) !== false ||
				strpos($aMsg['contact_name'], $sKeyword) !== false ||
				strpos($aMsg['contact_email'], $sKeyword) !== false ||
				(!is_array($aMsg['contact_subject']) && strpos($aMsg['contact_subject'], $sKeyword) !== false)) {
					$aMsgs[] = $aMsg;
				}
			}
		}
		return $aMsgs;
	}
	
	public function getMsg($iMsgId) {
		return !empty($this->aMsgs[$iMsgId]) ? $this->aMsgs[$iMsgId] : false;
	}
	
	public function getAll($bActive=true) {
		$sActive = $bActive ? '1' : '0';
		$aMsgs = array();
		foreach($this->aMsgs as $aMsg) {
			if(isset($aMsg['contact_active']) && $aMsg['contact_active'] === $sActive) {
				if(is_array($aMsg['contact_subject'])) {
					$aMsg['contact_subject'] = '';
				}
				$aMsg['contact_date'] = date ('Y-m-d H:i:s', $aMsg['contact_date']);
				$aMsgs[] = $aMsg;
			}
		}
		return $aMsgs;
	}
	
	public function saveComment($iMsgId, $sComment) {
		if(isset($this->aMsgs['item'.$iMsgId])) {
			$this->aMsgs['item'.$iMsgId]['contact_comment'] = $sComment;
			$this->aMsgs['item'.$iMsgId]['contact_comment_user'] = SessionUser::get('user');
			return $this->saveContacts();
		} else {
			return false;
		}
	}
	
	public function delete($iMsgId) {
		if(isset($this->aMsgs['item'.$iMsgId]['contact_active'])
			&& $this->aMsgs['item'.$iMsgId]['contact_active'] === '0') {
			unset($this->aMsgs['item'.$iMsgId]);
			return $this->saveContacts();
		} else {
			return false;
		}
	}
	
	public function archive($iMsgId) {
		if(isset($this->aMsgs['item'.$iMsgId])) {
			$this->aMsgs['item'.$iMsgId]['contact_active'] = 0;
			return $this->saveContacts();
		}
		return false;
	}
	
	public function restore($iMsgId) {
		if(isset($this->aMsgs['item'.$iMsgId])) {
			$this->aMsgs['item'.$iMsgId]['contact_active'] = 1;
			return $this->saveContacts();
		}
		return false;
	}
	
	private function saveContacts() {
		$oNewXml = $this->array2xml($this->getEmptyXmlObject('contacts'), $this->aMsgs);
		return $this->save2path($oNewXml, $this->sContactFilePath);
	}
	
	public function getNbMsgs($bActive=true) {
		$sActive = $bActive ? '1' : '0';
		$iNbMsg = 0;
		foreach($this->aMsgs as $aMsg) {
			if(isset($aMsg['contact_active']) && $aMsg['contact_active'] === $sActive) {
				$iNbMsg++;
			}
		}
		return $iNbMsg;
	}
	
	public function getLastMessageDate() {
		foreach($this->aMsgs as $aMsg) {
			if(isset($aMsg['contact_active']) && $aMsg['contact_active'] === '1') {
				$sDate = $aMsg['contact_date'];
			}
		}
		return date ('Y-m-d H:i:s', $sDate);
	}
}

interface iContactMgr {

	public function add(array $aMsgData);
	public function searchMsg($sKeyword);
	public function getAll();
	public function getMsg($iMsgId);
	public function saveComment($iMsgId, $sComment);
	public function delete($iMsgId);
	public function archive($iMsgId);
	public function restore($iMsgId);
}