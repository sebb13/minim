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
final class AdminAuthMgr {
    private static $bIsBan		= false;
	private static $bMailSecure	= false;
	private static $oUsersMgr	= NULL;

    public static function checkLogin($aParams, $bMailSecure=false) {
		self::$bMailSecure = $bMailSecure;
		if (isset($aParams['user']) && isset($aParams['pwd'])) {
			if(SessionCore::get('TRY') === 3) {
				self::logBan();
				self::$bIsBan = true;
			} elseif (SessionCore::get('TRY') === false) {
				SessionCore::set('TRY', 1); 
			} else {
				SessionCore::set('TRY', (int)SessionCore::get('TRY')+1); 
			}
		}
        if (isset($aParams['user']) && isset($aParams['pwd']) && self::login($aParams)) {
			return SessionCore::destroy('TRY');
        }
        return false;
    }
	
	private static function getBanFilePath() {
		return BAN_LOG.date('Ymd').'.log';
	}

    private static function login($aParams) {
		if(file_exists(self::getBanFilePath())) {
			$aBanIps = file(self::getBanFilePath());
		} else {
			$aBanIps = array();
		}
		if(in_array(Session::getSubSession('system', 'HTTP_REMOTE_IP'), $aBanIps)) {
			return self::kick();
		}
		if(count($aBanIps) >= 10) {
			//Multiple brute force attacks
			mail(
				ERROR_MAIL,
				'Multiple brute force attacks on '.WEB_PATH,
				'Multiple brute force attacks on '.WEB_PATH
			);
			return self::kick();
		}
		try {
			Toolz_Checker::checkParams(array(
											'required'		=> array('user', 'pwd', 'role'),
											'data'			=> $aParams,
											'nullAllowed'	=> false
									));
			if(!empty($aParams['captcha'])) {
				return self::kick();
			}
			$sUser = $aParams['user'];
			$sPwd = $aParams['pwd'];
			if(empty(self::$oUsersMgr)) {
				self::$oUsersMgr = new UserMgr();
			}
			$aUser = self::$oUsersMgr->getUserProps($sUser);
			if (isset($aUser['login']) && $aUser['login'] === $sUser) {
				if(!self::checkPwd($aUser['pwd'], $sPwd)) {
					self::setLoginFailAlert();
					return false;
				} else {
					if(self::$bMailSecure) {
						return self::checkMailCode(); 
					} else {
						SessionUser::login($sUser);
						SessionUser::setRole($aUser['role']);
						UserRequest::setParams('app_token', SessionCore::getSessionHash());
						return true;
					}
				}
			}
		} catch(CoreException $e) {
			if (DEV && ADMIN) {
				UserRequest::$oAlertBoxMgr->danger = (string)$e->getMessage();
			}
		}
		self::setLoginFailAlert();
        return false;
    }
	
	public static function checkPwd($sPwd, $sPwdTry) {
		/*
		 votre propre code !!!
		 your own code !!!
		 par défaut :
		 */
		if(strpos($sPwdTry, 'salt') === false) {
			return false;
		}
		return $sPwd === Toolz_Crypt::getSha1ForHtpasswd(substr($sPwdTry, 0, -4));
	}
	
	private static function checkMailCode() {
		/*
		 idem		 
		 */
		return true;
	}
	
	public static function logout() {
		SessionUser::logout();
		return array(
					'content'	=> self::getLoginForm(),
					'menu'		=> ' ',
					'sPage'		=> 'login'
				);
	}

    public static function getLoginForm() {
        if(self::$bIsBan) {
			return self::kick();
        } else {
            $oCacheMgr = new CacheMgrBack(SessionCore::getLangObject());
            if ($oCacheMgr->checkIfCacheExists('login', SessionLang::getLang())) {
				self::setLoginPromptAlert();
                $sContent = str_replace(
							'{__LOGIN_VALUE__}',
							SessionCore::get('REMOTE_USER') !== false ? SessionCore::get('REMOTE_USER') : '',
                            $oCacheMgr->getCache()
						);
				return $sContent;
            } else {
                throw new CoreException('login page unavailable');
            }
        }
    }
	
	private static function setLoginFailAlert() {
		return UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('login', 'AUTH_FAIL');
	}
	
	private static function setLoginPromptAlert() {
		return UserRequest::$oAlertBoxMgr->warning = SessionCore::getLangObject()->getMsg('common', 'LOGIN_PROMPT');
	}
	
	private static function kick() {
		header("HTTP/1.1 418 I'm a teapot");
		die('I\'m a teapot');
	}

    private static function logBan() {
		mail(
			ERROR_MAIL,
			'3 consecutive connection errors on '.WEB_PATH,
			SessionCore::get('REMOTE_USER').' - '.Session::getSubSession('system', 'HTTP_REMOTE_IP')
		);
        file_put_contents(
                    self::getBanFilePath(), 
                    Session::getSubSession('system', 'HTTP_REMOTE_IP').PHP_EOL, 
                    FILE_APPEND
                );
    }
}
