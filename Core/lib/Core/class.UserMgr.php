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
final class UserMgr extends SimpleXmlMgr {
	
	const ERROR_LOGIN_ALREADY_USED				= 'ERROR_LOGIN_ALREADY_USED';
	const ERROR_WRONG_PWD						= 'ERROR_WRONG_PWD';
	const SUCCESS_CREATED_USER_ACCOUNT			= 'SUCCESS_CREATED_USER_ACCOUNT';
	const ERROR_UNABLE_TO_CREATE_USER_ACCOUNT	= 'ERROR_UNABLE_TO_CREATE_USER_ACCOUNT';
	const SUCCESS_USER_ACCOUNT_EDIT_SUCCESSFULLY= 'SUCCESS_USER_ACCOUNT_EDIT_SUCCESSFULLY';
	const ERROR_UNABLE_TO_MODIFY_USER_ACCOUNT	= 'ERROR_UNABLE_TO_MODIFY_USER_ACCOUNT';
	const SUCCESS_DELETED_USER_ACCOUNT			= 'SUCCESS_DELETED_USER_ACCOUNT';
	const ERROR_UNABLE_TO_DELETE_USER_ACCOUNT	= 'ERROR_UNABLE_TO_DELETE_USER_ACCOUNT';
	const SUCCESS_GENERATED_HTPASSWD			= 'SUCCESS_GENERATED_HTPASSWD';
	const ERROR_GENERATED_HTPASSWD				= 'ERROR_GENERATED_HTPASSWD';
	const ERROR_LOGIN_ONLY_ALPHANUMERIC_CHAR	= 'ERROR_LOGIN_ONLY_ALPHANUMERIC_CHAR';
	const ERROR_TRY_DELETE_LAST_USER_ACCOUNT	= 'ERROR_TRY_DELETE_LAST_USER_ACCOUNT';
	
	public static $SysAdmin			= 'sysAdmin';
	public static $aUserTypes		= array('user'=>'user', 'sysAdmin'=>'sysAdmin');
	private $sUsersFilePath			= 'rights.xml';
	private $sUsersManagerTplName	= 'system_user.tpl';
	private $sUserItemTplName		= 'user.item.tpl';
	private $sUserPageTplName		= 'user.tpl';
	private $aUsers					= array();
	private $aLogins				= array();
	
	public function __construct() {
		$this->sUsersFilePath = DATA_PATH.$this->sUsersFilePath;
		parent::__construct();
		$this->formatUsersArray();
	}
	
	private function formatUsersArray() {
		$this->aUsers = array();
		$this->aLogins = array();
		$this->loadFile($this->sUsersFilePath);
		foreach($this->getIemsList() as $aUserByRole) {
			if(isset($aUserByRole['login'])) {
				$this->aUsers[] = $aUserByRole;
			} else {
				foreach($aUserByRole as $aUser) {
					if(isset($aUser['login'])) {
						$this->aUsers[] = $aUser;
					}
				}
			}
		}
		foreach($this->aUsers as $sKey=>$aUser) {
			$this->aLogins[$sKey] = $aUser['login'];
		}
	}
	
	public function getUserProps($sUserLogin) {
		$sKey = array_search($sUserLogin, $this->aLogins);
		if(isset($this->aUsers[$sKey]) && is_array($this->aUsers[$sKey])) {
			return $this->aUsers[$sKey];
		}
		return array();
	}
	
	public function setUsersProps($aUsersProps) {
		$aUsersTmp = $aUsers = array();
		foreach($aUsersProps as $aProps) {
			if(isset($aUsers[$aProps['name']]) && empty($aProps['value'])) {
				continue;
			} else {
				$aUsersTmp[$aProps['name']] = $aProps['value'];
			}
		}
		foreach($aUsersTmp as $sKey=>$sValue) {
			$aKeys = explode('-', $sKey);
			if(!isset($aUsers[$aKeys[1]])) {
				$aUsers[$aKeys[1]] = array();
			}
			$aUsers[$aKeys[1]][$aKeys[0]] = trim($sValue);
		}
		foreach($aUsers as $aUser) {
			$this->setUserProps($aUser['user'], $aUser, 'systemForce');
		}
	}
	
	public function setUserProps($sUserLogin, array $aUserProps, $sPwd) {
		try{
			Toolz_Checker::checkParams(array(
											'required'	=> array('login'),
											'data'	=> $aUserProps,
											'nullAllowed'	=> false
									));
			$aUser = $this->getUserProps($sUserLogin);
			if($sPwd !== 'systemForce' && !AdminAuthMgr::checkPwd($aUser['pwd'], trim($sPwd))) {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_WRONG_PWD);
				return false;
			}
			if(in_array($aUserProps['login'], $this->aLogins) && $aUserProps['login'] !== $sUserLogin) {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_LOGIN_ALREADY_USED);
				return false;
			}
			if(!Toolz_Checker::checkAlphaNum($aUserProps['login'])) {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_LOGIN_ONLY_ALPHANUMERIC_CHAR);
				return false;
			}
			$aUpdateUser = array(
							'login' => $aUserProps['login'], 
							'pwd' => !empty($aUserProps['pwd']) ? Toolz_Crypt::getSha1ForHtpasswd(trim($aUserProps['pwd'])) : $aUser['pwd'], 
							'role' => isset($aUserProps['role']) ? $aUserProps['role'] : $aUser['role']
						);
			$sKey = array_search($sUserLogin, $this->aLogins);
			$this->aUsers[$sKey] = $aUpdateUser;
		} catch(Exception $e) {
			if(DEV) {
				debug($e->getTrace());
			}
		}
		if($this->saveUsers()) {
			if(SessionUser::get('user') === $sUserLogin) {
				SessionUser::login($aUserProps['login']);
			}
			//rebuild htpasswd
			$this->updateHtpasswd();
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_user', self::SUCCESS_USER_ACCOUNT_EDIT_SUCCESSFULLY).' ('.$sUserLogin.')';
			return true;
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_UNABLE_TO_MODIFY_USER_ACCOUNT).' ('.$sUserLogin.')';
			return false;
		}
	}
	
	private function updateHtpasswd() {
		if(DEV) {
			$sUserPatern = '{__user__}:{__pwd__}';
			$aHtpasswd = array();
			foreach($this->aUsers as $aUser) {
				$aHtpasswd[] = str_replace(
									array('{__user__}','{__pwd__}'), 
									array($aUser['login'], $aUser['pwd']), 
									$sUserPatern
								);
			}
			if(file_put_contents(ROOT_PATH.'.htpasswd', implode("\n", $aHtpasswd))) {
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_user', self::SUCCESS_GENERATED_HTPASSWD);
				return true;
			} else {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_GENERATED_HTPASSWD);
				return false;
			}
		}
		return true;
	}
	
	public function createUser(array $aUserProps) {
		try {
		Toolz_Checker::checkParams(array(
                                        'required'		=> array('login','pwd','role'),
                                        'data'			=> $aUserProps,
										'nullAllowed'	=> false
                                ));
		} catch(GenericException $e) {
			UserRequest::$oAlertBoxMgr->danger = $e->getMessage();
			return false;
		}
		if(in_array($aUserProps['login'], $this->aLogins)) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_LOGIN_ALREADY_USED);
			return false;
		}
		$aNewUser = array(
						'login' => $aUserProps['login'], 
						'pwd' => Toolz_Crypt::getSha1ForHtpasswd(trim($aUserProps['pwd'])), 
						'role' => $aUserProps['role']
					);
		$this->aUsers[] = $aNewUser;
		if($this->saveUsers()) {
			//rebuild htpasswd
			$this->updateHtpasswd();
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_user', self::SUCCESS_CREATED_USER_ACCOUNT).' ('.$aUserProps['login'].')';
			return true;
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_UNABLE_TO_CREATE_USER_ACCOUNT).' ('.$aUserProps['login'].')';
			return false;
		}
	}
	
	public function deleteUser($sUserLogin) {
		if(count($this->aUsers) === 1) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_TRY_DELETE_LAST_USER_ACCOUNT);
			return false;
		}
		$sKey = array_search($sUserLogin, $this->aLogins);
		unset($this->aUsers[$sKey]);
		if($this->saveUsers()) {
			if(DEV) {
				//rebuild htpasswd
				$this->updateHtpasswd();
			}
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_user', self::SUCCESS_DELETED_USER_ACCOUNT).' ('.$sUserLogin.')';
			return true;
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_user', self::ERROR_UNABLE_TO_DELETE_USER_ACCOUNT).' ('.$sUserLogin.')';
			return false;
		}
	}
	
	private function saveUsers() {
		try {
			$oNewXml = $this->array2xml($this->getEmptyXmlObject('system_user'), $this->aUsers);
			$this->save2path($oNewXml, $this->sUsersFilePath);
			$this->formatUsersArray();
			return true;
		} catch(Exception $e) {
			return false;
		}
	}
	
	public function hasRight() {
		return SessionUser::getRole() === self::$SysAdmin;
	}
	
	public function getUserInterface() {
		$aUser = $this->getUserProps(SessionUser::get('user'));
		return str_replace(
						array(
							'{__USER_ID__}',
							'{__LOGIN_VALUE__}'
						), 
						array(
							array_search($aUser['login'], $this->aLogins),
							$aUser['login']
						), 
						file_get_contents(ADMIN_CONTENT_TPL_PATH.$this->sUserPageTplName)
					);
	}
	
	public function getUserManager() {
		$sUserItemTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sUserItemTplName);
		$sUsersForm = '';
		foreach($this->aUsers as $aUser) {
			$sUsersForm .= str_replace(
						array(
							'{__USER_ID__}',
							'{__LOGIN_VALUE__}',
							'{__USER_ROLE_OPTIONS__}'
						), 
						array(
							array_search($aUser['login'], $this->aLogins),
							$aUser['login'],
							Toolz_Form::optionsList($aUser['role'], self::$aUserTypes)
						), 
						$sUserItemTpl
					);
		}
		return str_replace(
						array(
							'{__USERS__}',
							'{__USER_ROLE_OPTIONS__}'
						), 
						array(
							$sUsersForm,
							Toolz_Form::optionsList('', self::$aUserTypes)
						), 
						file_get_contents(ADMIN_CONTENT_TPL_PATH.$this->sUsersManagerTplName)
					);
	}
}