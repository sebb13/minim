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
class Router {
	 
    private $aAuthRedirect = array();
    private $aHomeRedirect = array();
    private $a404Redirect = array();
	private $oPageConfig = NULL;
	protected $oView = NULL;

    public function __construct() {
		$this->oPageConfig = new PageConfig(UserRequest::getPage());
        $this->oView = new View($this->oPageConfig);
        $this->aAuthRedirect = array(
                                    'sLang'=>  UserRequest::getLang(),
                                    'sPage' => 'login'
                                );
        $this->aHomeRedirect = array(
                                    'sLang'=>  UserRequest::getLang(),
                                    'sPage' => 'home'
                                );
        $this->a404Redirect = array(
                                    'sLang'=>  UserRequest::getLang(),
                                    'sPage' => '404'
                                );
    }

    public function checkRequest() {
		try {
			if($this->checkRight()) {
				if($this->getAdditionalRouting() !== false && UserRequest::getParams('exw_action') === false) {
					UserRequest::setParams('exw_action', $this->getAdditionalRouting());
					return $this->doAction();
				}
				if (UserRequest::getParams('app_token') !== false && UserRequest::getParams('exw_action') !== false) {
					return $this->doAction();
				}
			}
			if (UserRequest::getParams('content') !== false) {
				return $this->oView->getContent();
			} else {
				return $this->oView->getPage();
			}
		} catch (CoreException $e) {
            echo $e;
			$this->redirect($this->a404Redirect);
			return $this->oView->getPage();
        } catch (Exception $e) {
			$sMsg = $e->getMessage()."\n\r".print_r($e->getTrace(), true);
			$oErrorLogs = new ErrorLogs();
			$oErrorLogs->addLog($sMsg);
			unset($oErrorLogs);
			$this->redirect($this->a404Redirect);
			return $this->oView->getPage();
		}
		if (UserRequest::getParams('content') !== false) {
			return $this->oView->getContent('home');
		} else {
			if(!UserRequest::getParams('sPage')) {
				$this->redirect($this->aHomeRedirect);
			}
			return $this->oView->getPage();
		}
		return $this->oView->get404();
     }

    private function checkRight() {
        if(ADMIN) {
            if(SessionUser::isLogged()) {
                return true;
            } elseif(UserRequest::getParams() !== false && AdminAuthMgr::checkLogin(UserRequest::getParams())) {
				SessionNav::setPreviousCurrentPage('home');
				UserRequest::setParams('app_token', SessionCore::getSessionHash());
                return true;
            } else {
				if (UserRequest::getParams('content') === 'menu') {
					return $this->oView->setContent(' ');
				}
				SessionNav::setPreviousCurrentPage(UserRequest::getPage());
                $this->redirect($this->aAuthRedirect);
				$this->oView->setContent('menu', ' ');
				$this->oView->setContent('login', AdminAuthMgr::getLoginForm());
                return false;
            }
            return true;
        } elseif(SessionCore::isSecureArea()) {
            if(SessionUser::isLogged()) {
                return true;
            } else {
                $this->redirect($this->aAuthRedirect);
				$this->oView->setContent('menu', ' ');
				$this->oView->setContent('login', AdminAuthMgr::getLoginForm());
                return false;
            }
        } else {
            return true;
        }
    }
	
	private function getAdditionalRouting() {
		$aRoutesConf = ADMIN ? RoutesConf::$aRoutesBackConf : RoutesConf::$aRoutesFrontConf;
		if(!empty($aRoutesConf[UserRequest::getPage()])) {
			return $aRoutesConf[UserRequest::getPage()];
		} else {
			return false;
		}
	}

    private function doAction() {
		if (($sAction = UserRequest::getParams('exw_action')) !== false) {
			$oCoreRequest = new CoreExec();
			return $oCoreRequest->execRequest($sAction);
		} elseif (UserRequest::getParams('content') !== false) {
			return $this->oView->getContent(UserRequest::getParams('content'));
		} else {
			return $this->oView->getPage();
		}
    }

    public function redirect($aNewRequest) {
        Toolz_Checker::checkParams(array(
                                        'required'	=> array('sPage', 'sLang'),
                                        'data'	=> $aNewRequest,
										'nullAllowed'	=> false
                                ));
        return UserRequest::setRequest($aNewRequest);
    }

    public function __destruct() {
        try {
            SessionCore::writeClose();
        } catch(Exception $e) {
            //echo $e;
        }
    }
}