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
final class PagesListMgr extends SimpleXmlMgr {
	
	private $sPagesListStorage = 'pagesList.xml';
	private $sXmlRoot = '<?xml version="1.0" encoding="UTF-8"?><pages></pages>';

	public function __construct() {
		$this->sPagesListStorage = DATA_PATH.$this->sPagesListStorage;
		try {
			return parent::__construct($this->sPagesListStorage);
		} catch(exception $e) {
			return $this->init();
		}
	}
	
	public function init() {
		file_put_contents($this->sPagesListStorage, $this->sXmlRoot);
		parent::__construct($this->sPagesListStorage);
		foreach(SitemapMgr::getStaticPagesList() as $sPageName => $sFormatedPageName) {
			if(!is_null($sPageName) && !is_null($sFormatedPageName)) {
				$this->addPage($sPageName, $sFormatedPageName);
			}
		}
		$oRoutingMgr = new RoutingMgr();
		foreach($oRoutingMgr->getAllRoutes('front') as $sPageName=>$sRoute) {
			$this->addPage($sPageName, $sPageName);
		}
		return true;
	}
	
	public function getPagesList() {
		return $this->getIemsList();
	}
	
	public function addPage($sPageName, $sFormatedPageName='') {
		$aTmp = $this->getIemsList();
		if(isset($aTmp[$sPageName])) {
			return true;
		}
		if(empty($sFormatedPageName)) {
			$sFormatedPageName = str_replace('_', '/', $sPageName);
		}
		return $this->addItem($sPageName, $sFormatedPageName);
	}
	
	public function removePage($sPage) {
		return $this->removeItem($sPage);
	}
	
	public function createDedicatedPage($sPageName, $sPageContents, $sLang=DEFAULT_LANG, $sExt=CacheMgr::DEFAULT_CACHE_EXT) {
		$sCachePath = CACHE_PATH.$sPageName.'_'.$sLang.$sExt;
		if(file_put_contents($sCachePath, $sPageContents)) {
			$this->addPage($sPageName);
			$sMsg = SessionCore::getLangObject()->getMsg('pages_configuration', PageConfig::SUCCESS_CREATE_PAGE);
			$sMsg .= ' ('.$sLang.')';
			UserRequest::$oAlertBoxMgr->success = $sMsg;
			return true;
		}
		$sMsg = SessionCore::getLangObject()->getMsg('pages_configuration', PageConfig::ERROR_CAN_NOT_CREATE_PAGE);
		$sMsg .= ' ('.$sLang.')';
		UserRequest::$oAlertBoxMgr->danger = $sMsg;
		return false;
	}
}