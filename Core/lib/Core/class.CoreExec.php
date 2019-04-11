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
final class CoreExec extends Router {
	
	public function __construct() {
		return parent::__construct();
	}
	
	public function execRequest($sAction) {
		if (self::checkStaticRouting($sAction)) {
			list($sClassName, $sMethodName) = explode('::', $sAction);
			$aActionParams = UserRequest::getParams();
			unset($aActionParams['exw_action']);
			try {
				$oClass = new $sClassName();
				if (UserRequest::getParams('app_token') !== SessionCore::getSessionHash()) {
					throw new Exception('INTERNAL ERROR', CoreException::INTERNAL_ERROR);
				}
				$mResult = $oClass->$sMethodName($aActionParams);
				if(!$mResult) {
					throw new CoreException('FATAL CORE ERROR', CoreException::INTERNAL_ERROR);
				}
			} catch(CoreException $e) {
				$sMsg = DEV ? $e->getMessage() : ''; 
				$sMsg .= DEV ? print_r($e->getTrace(), true) : ''; 
				$e->log();
				$mResult = array(
								'content' => 'INTERNAL ERROR : '.$sMsg,
								'sPage' => UserRequest::getRequest('sPage')
							);
				if($e->getCode() === CoreException::INTERNAL_ERROR) {
					UserRequest::$oAlertBoxMgr->danger = $e->getMessage();
				}
			} catch(Exception $e) {
				$sMsg = DEV ? $e->getMessage() : ''; 
				$sMsg .= DEV ? print_r($e->getTrace(), true) : ''; 
				$mResult = array(
								'content' => 'INTERNAL ERROR : '.$sMsg,
								'sPage' => UserRequest::getRequest('sPage')
							);
			}
			if (is_array($mResult) && !empty($mResult['content'])) {
				$sContent = $mResult['content'];
				if(!empty($mResult['sPage'])) {
					$sPageName = $mResult['sPage'];
				}
				if(!empty($mResult['menu'])) {
					$this->oView->setContent('menu', $mResult['menu']);
				}
			} elseif(is_string($mResult)) {
				$sContent = $mResult;
			} else {
				$sContent = '';
			}
			if(empty($sPageName)) {
				$sPageName = UserRequest::getRequest('sPage');
			}
			if (!empty($sContent)) {
				$this->oView->setContent($sPageName, $sContent);
			}
			return $this->getView();
		} else {
			throw new CoreException('INVALID REQUEST '.UserRequest::getParams('exw_action'));
		}
	}
	
	private function getView() {
		if (UserRequest::getParams('content') !== false) {
			return $this->oView->getContent();
		} else {
			return $this->oView->getPage();
		}
	}
	
	public static function checkStaticRouting($sRoute) {
		try {
			return Toolz_Checker::isValidMethod($sRoute);
		} catch (CoreException $e) {
			return false;
		}
	}
}