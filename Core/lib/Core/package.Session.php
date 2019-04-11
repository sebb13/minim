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
abstract class Session {

    public static function set($sKey, $mValue='') {
		$_SESSION[$sKey] = $mValue;
		return true;
    }

    public static function get($sKey) {
		return self::keyExists($sKey) ? $_SESSION[$sKey] : false;
    }
	
	public static function setSubSession($sSubKey, $sKey, $mValue) {
		if (!array_key_exists($sSubKey, $_SESSION)) {
			$_SESSION[$sSubKey] = array();
		}
		$_SESSION[$sSubKey][$sKey] = $mValue;
		return true;
	}

    public static function destroy() {
		if(func_num_args() > 0) {
			foreach(func_get_args() as $sKey) {
				$_SESSION[$sKey] = null;
			}
		} else {
			session_unset();
			session_destroy();
		}
    }
	
	public static function getSubSession($sSubKey, $sKey) {
		if (array_key_exists($sSubKey, $_SESSION) && is_array($_SESSION[$sSubKey]) && array_key_exists($sKey, $_SESSION[$sSubKey])) {
			return $_SESSION[$sSubKey][$sKey];
		}
		return false;
	}

    public static function keyExists($sKey) {
        return !empty($_SESSION[$sKey]);
    }
	
	public static function destroySubSessionKey($sSubKey, $sKey) {
		if (array_key_exists($sSubKey, $_SESSION) && array_key_exists($sKey, $_SESSION[$sSubKey])) {
			unset($_SESSION[$sSubKey][$sKey]);
		}
		return true;
    }
}

final class SessionUser extends Session {
	
	private static $sSubKey = 'user';

    public static function get($sKey) {
		return parent::getSubSession(self::$sSubKey, $sKey);
    }

    public static function set($sKey, $mValue='') {
		return parent::setSubSession(self::$sSubKey,$sKey, $mValue);
    }
	
	public static function delete($sKey) {
		return parent::destroySubSessionKey(self::$sSubKey,$sKey);
	}

    public static function login($sUser) {
        return self::set('user', $sUser);
    }

	public static function logout() {
        return parent::destroy();
    }

    public static function isLogged() {
        return self::get('user') !== false;
    }

    public static function setEmail($sEmail) {
		return parent::setSubSession(self::$sSubKey, 'user_email', $sEmail);
    }
	
	public static function setRole($sRole) {
		return parent::setSubSession(self::$sSubKey,'role', $sRole);
	}
	
	public static function getRole() {
		return parent::getSubSession(self::$sSubKey,'role');
	}
	
	public static function checkRole($sRole) {
		if(self::getRole() !== $sRole) {
			throw new CoreException('system page unavailable', CoreException::INTERNAL_ERROR);
		}
		return true;
	}
}

final class SessionNav extends Session {
	
	private static $sSubKey = 'nav';

    public static function getCurrentSection() {
		if (($mSesction = parent::get('sectionsList')) === false) {
			$mSesction = self::setSectionsList();
		} else {
			// -- if one page is added in runing time
			if (!isset($mSesction[basename($_SERVER['PHP_SELF'])])) {
				$mSesction = self::setSectionsList();
			}
		}
		return $mSesction[basename($_SERVER['PHP_SELF'])];
    }

    private static function setSectionsList() {
		$aSections = array();
		$oXml = new SimpleXMLElement(file_get_contents(XML_PATH.'sections.xml'));
		foreach ($oXml->section as $oSection) {
			$aSections[(string)$oSection['filename']] = (string)$oSection['sectionName'];
		}
		parent::set('sectionsList', $aSections);
		return $aSections;
    }

    public static function setCurrentPage($sPageName) {
		return parent::setSubSession(self::$sSubKey, 'currentPage', $sPageName);
    }

    public static function getCurrentPage() {
		return parent::getSubSession(self::$sSubKey, 'currentPage');
    }

    public static function setPreviousCurrentPage($sPageName) {
		return parent::setSubSession(self::$sSubKey, 'previousCurrentPage', $sPageName);
    }

    public static function getPreviousCurrentPage() {
		return parent::getSubSession(self::$sSubKey, 'previousCurrentPage');
    }
	
	public static function unsetPreviousCurrentPage() {
		return parent::destroySubSessionKey(self::$sSubKey, 'previousCurrentPage');
	}
	
	public static function setPreviousParams($aParams) {
		return parent::setSubSession(self::$sSubKey, 'previousParams', $aParams);
	}
	
	public static function getPreviousParams() {
		return parent::getSubSession(self::$sSubKey, 'previousParams');
	}
}

final class SessionLang extends Session {

    public static function langIsSet() {
		return parent::get('lang') !== false;
    }

    public static function getLang() {
		if (!self::langIsSet()) {
			self::setLang(DEFAULT_LANG);
		}
		return parent::get('lang');
    }

    public static function setLang($sLang) {
		if(SessionCore::setLang($sLang)) {
			return parent::set('lang', $sLang);
		}
		return false;
    }
}

final class SessionDial extends Session {

    public static function setMsg($sMsg, $sTitle='') {
		return parent::set(
						'dial',
						str_replace(
								array('{__MSG__}', '{__TITLE__}'),
								array($sMsg,$sTitle),
								file_get_contents(PUBLIC_TPL.'_dial.container.tpl')
						)
					);
    }

    public static function getMsg() {
		$sDial = parent::get('dial');
		parent::destroy('dial');
		return $sDial;
    }
}

final class SessionCore extends Session {

    public static $oLang		= NULL;
    public static $sToken		= '';
    public static $bMaintenance = false;
    private static $sTokenKey	= '';
	private static $sSubKey		= 'system';

    public static function set($sKey, $mValue='') {
		return parent::setSubSession(self::$sSubKey, $sKey, $mValue);
    }

    public static function get($sKey) {
		return (getenv($sKey) !== false) 
			? getenv($sKey) 
			: parent::getSubSession(self::$sSubKey, $sKey);
    }
	
	public static function destroy() {
		if(func_num_args() > 0) {
			foreach(func_get_args() as $sKey) {
				parent::destroySubSessionKey(self::$sSubKey, $sKey);
			}
		}
		return true;
	}

    public static function getUniqId() {
		$aSystem = parent::get('system');
		if (empty($aSystem['uniqId'])) {
			$oConfig = new Config();
			$aSystem['uniqId'] = $oConfig->getGlobalConf('SYS_UNIQ_ID');
			unset($oConfig);
			parent::set('system', $aSystem);
		}
		return $aSystem['uniqId'];
    }

    public static function setLang($sLang) {
		if (!empty(self::$oLang)) {
			if(in_array($sLang, self::$oLang->getAvailable())) {
				self::$oLang->LOCALE = $sLang;
				return true;
			}
		}
		return false;
    }

    public static function setLangObject(Lang $oLang) {
		self::$oLang = $oLang;
		return true;
    }

    public static function getLangObject() {
		return self::$oLang;
    }

    public static function sessionStart($mToken) {
		try {
			// -- INIT TOKEN
			$sClearTokenKey = DOMAIN_NAME.date('d');
			$sSalt = getenv('REMOTE_ADDR');
			self::$sTokenKey = str_replace('.', '', crypt($sClearTokenKey, $sSalt));
			// -- INIT SESSION
			$iTTL = strtotime(date('Y-m-d', strtotime('tomorrow'))) - time();
			ini_set('session.gc_maxlifetime', $iTTL);
			ini_set('session.cookie_lifetime', $iTTL);
			ini_set('session.force_path', 0);
			ini_set('session.cookie_secure', 1);
			session_save_path(SESSION_PATH);
			// -- CHECK IF SESSION EXIST
			if (self::checkToken($mToken)) {
				session_id(self::getSessionId($mToken));
			} elseif(UserRequest::getCookie('minimCookie') !== false 
			&& self::checkToken(UserRequest::getCookie('minimCookie'))) {
				session_id(self::getSessionId(UserRequest::getCookie('minimCookie')));
			} else {
				$sSessId = uniqid();
				$sToken = self::$sTokenKey.str_rot13($sSessId);
				UserRequest::setCookie('minimCookie', $sToken, time()+$iTTL);
				session_id($sSessId);
			}
			// -- START
			return session_start();
		} catch (Exception $e) {
			if(DEV) {
				debug($e);
			}
			return false;
		}
    }

    public static function getSessionHash() {
        return self::$sTokenKey.str_rot13(session_id());
    }

    private static function checkToken($mToken) {
		try {
			return is_file(SESSION_PATH.'sess_'.self::getSessionId($mToken));
		} catch (Exception $e) {
			return false;
		}
    }

    private static function getSessionId($mToken) {
        return str_rot13(str_replace(self::$sTokenKey, '', $mToken));
    }

    public static function isSecureArea() {
        return self::get('isSecureArea');
    }
	
	public static function writeClose() {
		return session_write_close();
	}
}