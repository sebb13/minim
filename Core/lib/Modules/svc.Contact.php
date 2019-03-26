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
final class Contact extends CoreCommon {
	
	private $oContactMgr = NULL;
	
	public function __construct() {
		parent::__construct();
		$this->oContactMgr = new ContactMgr();
	}
	
	public function getDashboard() {
		$sTplPath = ModulesMgr::getFilePath(__CLASS__, 'backPartsTpl').'dashboard.tpl';
		$sContents = str_replace(
							array(
								'{__NB_MSG__}',
								'{__NB_ARCHIVE__}',
								'{__LAST_MSG_DATE__}', 
							),
							array(
								$this->oContactMgr->getNbMsgs(true),
								$this->oContactMgr->getNbMsgs(false),
								$this->oContactMgr->getLastMessageDate(),
							),
							file_get_contents($sTplPath)
						);
		$sDashboard = Dashboard::getDashboard('{__CONTACT_MGR_HOME_TITLE__}', $sContents);
		return $this->oTplMgr->buildSimpleCacheTpl(
												$sDashboard, 
												ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'contact.xml'
											);
	}
	
	public function getContactPage(array $aMsgData=array()) {
		$sContent = $this->oContactMgr->getFrontPage($aMsgData);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$sContent, 
															ModulesMgr::getFilePath(__CLASS__, 'locales', $this->oLang->LOCALE).'contact.xml'
														),
				'sPage'	=> 'contact'
			);
	}
	
	public function addMsg() {
		$sContent = $this->oContactMgr->newMsg(UserRequest::getParams());
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$sContent, 
															ModulesMgr::getFilePath(__CLASS__, 'locales', $this->oLang->LOCALE).'contact.xml'
														),
				'sPage'	=> 'contact'
			);
	}
	
	public function getHomePage() {
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$this->oContactMgr->getHomePage(), 
															ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'contact.xml'
														),
				'sPage'	=> 'contact_home'
			);
	}
	
	public function getMsgsPage() {
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$this->oContactMgr->getMsgsPage(true), 
															ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'contact.xml'
														),
				'sPage'	=> 'contact_messagesReceived'
			);
	}
	
	public function getArchivesPage() {
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$this->oContactMgr->getMsgsPage(false), 
															ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'contact.xml'
														),
				'sPage'	=> 'contact_archivedMessages'
			);
	}
	
	public function archiveMsg() {
		$this->oContactMgr->archiveMsg(UserRequest::getParams('sMsgId'));
		return $this->getMsgsPage();
	}
	
	public function restoreMsg() {
		$this->oContactMgr->restoreMsg(UserRequest::getParams('sMsgId'));
		return $this->getMsgsPage();
	}
	
	public function deleteMsg() {
		$this->oContactMgr->deleteMsg(UserRequest::getParams('sMsgId'));
		return $this->getArchivesPage();
	}
	
	public function getEditComment() {
		$sContents = str_replace(
						array(
							'{__CONTACT_COMMENT__}', 
							'{__MSG_ID__}'
						),
						array(
							!UserRequest::getParams('sComment') ? '' : UserRequest::getParams('sComment'), 
							UserRequest::getParams('iMsgId')
						),
						file_get_contents(ModulesMgr::getFilePath(__CLASS__, 'backPartsTpl').'add.comment.tpl')
				
			);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
											$sContents,
											ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'contact.xml'
										),
				'sPage'	=> UserRequest::getParams('content')
			);
	}
	
	public function saveComment() {
		$sContents = $this->oContactMgr->saveUserComment(
											UserRequest::getParams('sMsgId'), 
											UserRequest::getParams('sComment')
										);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
											$sContents,
											ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'contact.xml'
										),
				'sPage'	=> UserRequest::getParams('content')
			);
	}
	
	public function searchMsg() {
		$sContents = $this->oContactMgr->getMsgsPage(true, UserRequest::getParams('sKeyword'));
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
											$sContents,
											ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'contact.xml'
										),
				'sPage'	=> 'contact_messagesReceived'
			);
	}
	
	public function getFile() {
		return $this->oContactMgr->getFile(UserRequest::getRequest('file_id'));
	}
}