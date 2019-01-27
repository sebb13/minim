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
final class Pages extends CoreCommon {
	
	public function getPageConfig() {
		UserRequest::startBenchmark('getPageConfig');
		if(UserRequest::getParams('pageToConfigure') !== false) {
			$oPageConfig = new PageConfig(UserRequest::getParams('pageToConfigure'), true);
		} else {
			$oPageConfig = new PageConfig('', true);
		}
		$sContent = str_replace(
						'{__BENCHMARK__}', 
						UserRequest::stopBenchmark('getPageConfig', true), 
						$oPageConfig->getConfInterface()
					);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
														$sContent, 
														ADMIN_LOC_PATH.$this->oLang->LOCALE.'/pages_configuration.xml'
							),
				'sPage'	=> 'pages_configuration'
			);
	}
	
	public function savePageConfig() {
		$oPageConfig = new PageConfig(UserRequest::getParams('pageToConfigure'), true);
		$oPageConfig->save();
		return $this->getPageConfig();
	}
	
	public function addPageToConfigure() {
		$oPagesListMgr = new PagesListMgr();
		$oPagesListMgr->addPage(UserRequest::getParams('newPageName'));
		unset($oPagesListMgr);
		UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_configuration', PageConfig::SUCCESS_ADD_PAGE);
		UserRequest::setParams('pageToConfigure', UserRequest::getParams('newPageName'));
		return $this->getPageConfig();
	}
	
	public function deletePageToConfigure() {
		$oPageConfig = new PageConfig(UserRequest::getParams('deletePageName'), true);
		if($oPageConfig->deletePage(UserRequest::getParams('deletePageName'))) {
			$oPagesListMgr = new PagesListMgr();
			$oPagesListMgr->removePage(UserRequest::getParams('deletePageName'));
			unset($oPagesListMgr, $oPageConfig);
			$oPageConfig = new PageConfig(PageConfig::DEF, true);
		}
		return $this->getPageConfig();
	}
	
	public function getSitemapPage() {
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
														SitemapMgr::getSitemapPage(), 
														ADMIN_LOC_PATH.$this->oLang->LOCALE.'/pages_sitemap.xml'
							),
				'sPage'	=> 'pages_sitemap'
			);
	}
	
	public function regenerateSitemap() {
		SitemapMgr::build();
		return $this->getSitemapPage();
	}
}