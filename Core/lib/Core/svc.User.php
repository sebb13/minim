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
final class User extends CoreCommon {
	
	private $oUserMgr = NULL;
	
	public function __construct() {
		parent::__construct();
		$this->oUserMgr = new UserMgr();
	}
	
	public function getUserManager() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		UserRequest::startBenchmark('getUserManager');
		$sContents = $this->oUserMgr->getUserManager();
		$sContents .= UserRequest::stopBenchmark('getUserManager', true);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
														$sContents, 
														ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_user.xml'
							),
				'sPage'	=> 'system_user'
			);
	}
	
	public function getUserPage() {
		$sContents = $this->oUserMgr->getUserInterface(SessionUser::get('user'));
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
														$sContents, 
														ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_user.xml'
							),
				'sPage'	=> 'user'
			);
	}
	
	public function addUser() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		$this->oUserMgr->createUser(UserRequest::getParams());
		return $this->getUserManager();
	}
	
	public function deleteUser() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		$this->oUserMgr->deleteUser(UserRequest::getParams('user'));
		return $this->getUserManager();
	}
	
	public function updateUser() {
		$this->oUserMgr->setUserProps(
								UserRequest::getParams('user'), 
								UserRequest::getParams(),
								UserRequest::getParams('currentPwd')
							);
		return $this->getUserPage();
	}
	
	public function updateUsers() {
		$aUsers = UserRequest::getParams();
		if(!isset($aUsers['users']) || !is_array($aUsers['users'])) {
			
		} else {
			$this->oUserMgr->setUsersProps($aUsers['users']);
		}
		return $this->getUserManager();
	}
}