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
final class Starter {
	
	public static function start(array $aServer, array $aRequest, $mParams=array(), $mFiles=array(), $aCookies=array()) {
		Toolz_Checker::checkParams(array(
									'required'	=> array('page', 'lang'),
									'data'	=> $aRequest
								));
		$aRequest['sPage'] = str_replace(
									array('.php', '.html', '/'), 
									array('', '', '_'), 
									trim($aRequest['page'])
								);
		$aRequest['sLang'] = $aRequest['lang'];
        //maintenance mode
        if(SessionCore::$bMaintenance && !ADMIN) {
            $aRequest['sPage'] = 'maintenance';
        }
		// Draft Management - Forced mode
		if(isset($aRequest[DraftMgr::$sParamUrl]) && $aRequest[DraftMgr::$sParamUrl] === '1') {
			$aRequest['bInDraftMode'] = true;
			unset($aRequest[DraftMgr::$sParamUrl]);
		}
		// Version Management - Forced mode
		if(!empty($aRequest[VersionsContentsMgr::$sParamUrl])) {
			$aRequest['version'] = $aRequest[VersionsContentsMgr::$sParamUrl];
			$aRequest['sPage'] = $aRequest['version'].'_'.$aRequest['sPage'];
			unset($aRequest[VersionsContentsMgr::$sParamUrl]);
		}
		UserRequest::init($aRequest, $mParams, $mFiles, $aCookies, $aServer);
		self::initSystemSession();
		SessionNav::setCurrentPage($aRequest['sPage']);
		self::InitLang($aRequest['sLang']);
		return true;
	}
	
	private static function initSystemSession() {
		
		SessionCore::sessionStart(UserRequest::getParams('app_token'));
		if(UserRequest::getParams('app_token') !== false) {
			SessionCore::set('app_token', UserRequest::getParams('app_token'));
		} else {
			if(SessionCore::get('app_token') !== false) {
				UserRequest::setParams('app_token', SessionCore::get('app_token'));
			}
		}
		if(SessionNav::getPreviousCurrentPage() !== false) {
			UserRequest::setRequest(
								array(
									'sLang'=>  UserRequest::getLang(),
									'sPage' => SessionNav::getPreviousCurrentPage()
								)
							);
			SessionNav::unsetPreviousCurrentPage();
		}
	}
	
	private static function InitLang($sLangCalled='') {
		if (!empty($sLangCalled)) {
			$sLang = $sLangCalled;
		} elseif(!SessionLang::langIsSet()) {
			if (($sHTTP_ACCEPT_LANGUAGE = UserRequest::getEnv('HTTP_ACCEPT_LANGUAGE')) !== false) {
				$aBrowserLang = explode(',',$sHTTP_ACCEPT_LANGUAGE);
				$sLang = strtoupper(substr($aBrowserLang[0],0,2));
				/* 
				 * TODO 
				 Remove hack
				 update FlagsMgr
				 */
				if ($sLang === 'EN') $sLang = 'GB';
				unset($aBrowserLang);
			} else {
				$sLang = DEFAULT_LANG;
			}
		} else {
			$sLang = SessionLang::getLang();
		}
		$oLang = new Lang($sLang, DEFAULT_LANG, ADMIN ? ADMIN_LOC_PATH : LOC_PATH);
		SessionCore::setLangObject($oLang);
		if(!in_array($sLang, $oLang->getAvailable())) {
			$sLang = DEFAULT_LANG;
			UserRequest::setRequest(
								array(
									'sLang'	=> $sLang,
									'sPage'	=> UserRequest::getRequest('sPage')
								)
							);
		}
		SessionLang::setLang($sLang);
		$oLang->LOCALE = $sLang;
		return true;
	}
}