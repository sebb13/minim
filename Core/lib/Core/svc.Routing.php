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
final class Routing extends CoreCommon {
	
	private $oRoutingMgr = null;
	
	public function __construct() {
		SessionUser::checkRole(UserMgr::$SysAdmin);
		parent::__construct();
		$this->oRoutingMgr = new RoutingMgr();
	}
	
	public function getRoutingInterface() {
		if(UserRequest::getParams('sModuleToConfigure') !== false) {
			$sModule = UserRequest::getParams('sModuleToConfigure');
		} else {
			$sModule = 'minim';
		}
		if(UserRequest::getParams('sSideToConfigure') !== false) {
			$sSide = UserRequest::getParams('sSideToConfigure');
		} else {
			$sSide = 'back';
		}
		$sContents = $this->oRoutingMgr->getRoutingInterface($sModule, $sSide);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$sContents, 
															ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_routing.xml'
														),
				'sPage'	=> 'system_routing'
			);
	}
	
	public function getNewRouteEntries() {
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$this->oRoutingMgr->getNewEntries(
																					UserRequest::getParams('sModuleToConfigure'), 
																					UserRequest::getParams('sSideToConfigure'), 
																					UserRequest::getParams('nbRoutesToAdd')
																				), 
															ADMIN_LOC_PATH.$this->oLang->LOCALE.'/system_routing.xml'
														),
				'sPage'	=> 'system_routing'
			);
	}
	
	public function updateRoutes() {
		$aTmp = explode('&',htmlspecialchars_decode(urldecode(UserRequest::getParams('routes'))));
		$aRoutes = array();
		foreach($aTmp as $sInput) {
			$aInput = explode('=', $sInput);
			if(count($aInput) === 2) {
				$aRoutes[$aInput[0]] = $aInput[1];
			}
		}
		$this->oRoutingMgr->updateRoutes(
							UserRequest::getParams('sModuleToConfigure'), 
							UserRequest::getParams('sSideToConfigure'), 
							$aRoutes
					);
		return $this->getRoutingInterface();
	}
	
	public function removeRoute() {
		$this->oRoutingMgr->removeRoute(
							UserRequest::getParams('sModuleToConfigure'), 
							UserRequest::getParams('sSideToConfigure'), 
							UserRequest::getParams('sPageToRemove')
					);
		return $this->getRoutingInterface();
	}
	
	public function __destruct() {
		$this->oRoutingMgr = null;
	}
}