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
final class RoutingMgr extends SimpleXmlMgr {
	
	const SUCCESS_REMOVE_ROUTE			= 'SUCCESS_REMOVE_ROUTE';
	const ERROR_CAN_NOT_REMOVE_ROUTE	= 'ERROR_CAN_NOT_REMOVE_ROUTE';
	const SUCCESS_ROUTING_SAVE			= 'SUCCESS_ROUTING_SAVE';
	const ERROR_CAN_NOT_SAVE			= 'ERROR_CAN_NOT_SAVE';
	const INVALID_ROUTE					= 'INVALID_ROUTE';
	private $aRoutes			= array();
	private $sSuffixXmlTpl		= '.routes.{__SIDE__}.xml';
	private $sSuffixXmlName		= '';
	private $sModuleName		= '';
	private $sRoutesXmlPath		= '';
	private $sTplFilename		= 'class.RoutesConf.tpl';
	private $sClassFilename		= 'class.RoutesConf.php';
	private $aSideAllowed		= array('front'=>'front','back'=>'back');
	private $sItemTplName		= 'routing.item.tpl';
	private $sContainerTplName	= 'system_routing.tpl';
	private $sItemTpl			= '';
	private $sContainerTpl		= '';

	public function __construct($sModule='minim', $sSide='') {
		$this->sItemTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sItemTplName);
		$this->sContainerTpl = file_get_contents(ADMIN_CONTENT_TPL_PATH.$this->sContainerTplName);
		parent::__construct();
		$this->init($sModule, $sSide);
	}
	
	private function init($sModule, $sSide='') {
		$this->aRoutes = array();
		if(empty($sSide)) {
			$sSide = ADMIN ? 'back' : 'front';
		}
		if(!in_array($sSide, $this->aSideAllowed)) {
			throw new CoreException($sSide.' is not a valid side');
		}
		$this->sModuleName = $sModule;
		$this->sSuffixXmlName = str_replace('{__SIDE__}', $sSide, $this->sSuffixXmlTpl);
		if($this->sModuleName === 'minim') {
			$sRootPath = DATA_PATH;
		} else {
			$sRootPath = MODULES_PATH.ucfirst($this->sModuleName).'/'.GEN_DATA_PATH;
		}
		$this->sRoutesXmlPath = $sRootPath.$this->sModuleName.$this->sSuffixXmlName;
		if(!file_exists($this->sRoutesXmlPath)) {
			$this->aRoutes = array();
		} else {
			$this->loadFile($this->sRoutesXmlPath);
			$this->aRoutes = $this->getIemsList();
		}
		return $this->aRoutes;
	}
	
	public function getRoute($sModule, $sRouteName) {
		$this->aRoutes = $this->getRoutes($sModule);
		return !empty($this->aRoutes[$sRouteName]) ? $this->aRoutes[$sRouteName] : false;
	}
	
	public function getRoutes($sModule, $sSide) {
		return $this->init($sModule, $sSide);
	}
	
	public function getAllRoutes($sSide) {
		$aModules = ModulesMgr::getModulesAvailable();
		array_unshift($aModules, 'minim');
		$aRoutes = array();
		foreach($aModules as $sModule) {
			$this->init($sModule, $sSide);
			$aTmp = $this->getIemsList();
			$aRoutes = array_merge($aRoutes, $aTmp);
		}
		unset($aTmp);
		$this->sModuleName = '';		
		return $aRoutes;
	}
	
	public function removeRoute($sModule, $sSide, $sRoute) {
		$this->init($sModule, $sSide);
		$this->removeItem($sRoute);
		if($this->save2Class()) {
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_routing', self::SUCCESS_REMOVE_ROUTE);
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_routing', self::ERROR_CAN_NOT_REMOVE_ROUTE);
		}
		return true;
	}
	
	public function updateRoutes($sModule, $sSide, $aRoutes) {
		$this->init($sModule, $sSide);
		$bIsValidEntry = true;
		$bError = false;
		$iK = 1;
		$aNewRoutes = array();
		while($bIsValidEntry) {
			if(isset($aRoutes['page_'.(string)$iK]) && isset($aRoutes['route_'.(string)$iK])) {
				$aNewRoutes[$aRoutes['page_'.(string)$iK]] = $aRoutes['route_'.(string)$iK];
				if(!Toolz_Checker::isValidMethod($aRoutes['route_'.(string)$iK])) {
					$bError = true;
					$sMsg = SessionCore::getLangObject()->getMsg('system_routing', self::INVALID_ROUTE);
					$sMsg .= ' : '.$aRoutes['route_'.(string)$iK];
					UserRequest::$oAlertBoxMgr->danger = $sMsg;
				}
			} else {
				$bIsValidEntry = false;
			}
			$iK++;
		}
		if(!$bError) {
			if($this->save2pathFromArray('routes', $this->sRoutesXmlPath, $aNewRoutes) 
			&& $this->save2Class()) {
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('system_routing', self::SUCCESS_ROUTING_SAVE);
			} else {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('system_routing', self::ERROR_CAN_NOT_SAVE);
			}
		}
		return true;
	}
	
	private function save2Class() {
		$sClass = file_get_contents(CORE_TPL_PATH.$this->sTplFilename);
		foreach($this->aSideAllowed as $sSide) {
			$aRoutes = $this->getAllRoutes($sSide);
			$sClass = str_replace(
								'{__ARRAY_'.  strtoupper($sSide).'__}',
								var_export($aRoutes, true),
								$sClass
				);
		}
		return file_put_contents(DATA_PATH.$this->sClassFilename, $sClass);
	}
	
	public function getRoutingInterface($sModule, $sSide) {
		UserRequest::startBenchmark('getRoutingInterface');
		$aModules = array('minim' => 'minim');
		foreach(ModulesMgr::getModulesAvailable() as $sModuleName) {
			$aModules[$sModuleName] = $sModuleName;
		}
		$sFormContent = '';
		$aRoutes = $this->getRoutes($sModule, $sSide);
		$iKey=1;
		foreach($aRoutes as $sPage=>$sRoute) {
			$sFormContent .= str_replace(
									array(
										'{__PAGE_NAME__}',
										'{__PAGE_VALUE__}',
										'{__ROUTE_NAME__}',
										'{__ROUTE_VALUE__}'
									), 
									array(
										'page_'.(string)$iKey,
										$sPage,
										'route_'.(string)$iKey,
										$sRoute
									), 
									$this->sItemTpl
							);
			$iKey++;
		}
		return str_replace(
						array(
							'{__PAGE_TITLE__}',
							'{__MODULES_LIST__}',
							'{__SIDES_LIST__}',
							'{__CONTENT__}',
							'{__BENCHMARK__}'
						), 
						array(
							$this->sModuleName,
							Toolz_Form::optionsList($sModule, $aModules),
							Toolz_Form::optionsList($sSide, $this->aSideAllowed),
							$sFormContent,
							UserRequest::stopBenchmark('getRoutingInterface', true)
						), 
						$this->sContainerTpl
					);
	}
	
	public function getNewEntries($sModule='minim', $sSide='back', $mNbRoutesToAdd=1) {
		$sFormContent = '';
		$aRoutes = $this->getRoutes($sModule, $sSide);
		$iCountRoutes = count($aRoutes);
		for($iLoop=1 ; $iLoop<=(int)$mNbRoutesToAdd ; $iLoop++) {
			$sFormContent .= str_replace(
									array(
										'{__PAGE_NAME__}',
										'{__PAGE_VALUE__}',
										'{__ROUTE_NAME__}',
										'{__ROUTE_VALUE__}'
									), 
									array(
										'page_'.(string)($iCountRoutes+$iLoop),
										'',
										'route_'.(string)($iCountRoutes+$iLoop),
										''
									), 
									$this->sItemTpl
							);
		}
		return $sFormContent;
	}
}