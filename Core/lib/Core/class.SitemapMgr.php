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
final class SitemapMgr {

	const SUCCESS_REGENERATE_SITEMAP = 'SUCCESS_REGENERATE_SITEMAP';
	const ERROR_REGENERATE_SITEMAP = 'ERROR_REGENERATE_SITEMAP';
	const DELETE_PAGE_TO_IGNORE_SUCCESS = 'DELETE_PAGE_TO_IGNORE_SUCCESS';
	const DELETE_PAGE_TO_IGNORE_ERROR = 'DELETE_PAGE_TO_IGNORE_ERROR';
	const ADD_PAGE_TO_IGNORE_SUCCESS = 'ADD_PAGE_TO_IGNORE_SUCCESS';
	const ADD_PAGE_TO_IGNORE_ERROR = 'ADD_PAGE_TO_IGNORE_ERROR';
	private static $sTpl = '<?xml version="1.0" encoding="UTF-8"?>
							<urlset
								xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
								xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
								 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
							</urlset>';
	private static $sRobotsTxtPatern		= 'Sitemap: {__WEB_PATH__}sitemap.xml';
	private static $sRobotsTxtFileName		= 'robots.txt';
	private static $sSitemapFileName		= 'sitemap.xml';
	private static $aAllowedNodes			= array('loc','lastmod','changefreq','priority');
	private static $aExcludedFiles			= array(
												'404'=>'404',
												'menu'=>'menu',
												'thankyou'=>'thankyou',
												'downloadFile'=>'downloadFile',
												'maintenance'=>'maintenance'
											);
	private static $sExcludedFilesFilename	= 'sitemapExcludedFiles.json';
	
	private static function init() {
		clearstatcache();
		if(file_exists(DATA_PATH.self::$sExcludedFilesFilename)) {
			self::$aExcludedFiles = (array) json_decode(file_get_contents(DATA_PATH.self::$sExcludedFilesFilename));
		} else {
			file_put_contents(DATA_PATH.self::$sExcludedFilesFilename, json_encode(self::$aExcludedFiles));
			return self::init();
		}
	}
	
	public static function deletePageToIgnore($sFilename) {
		self::init();
		if(isset(self::$aExcludedFiles[$sFilename])) {
			unset(self::$aExcludedFiles[$sFilename]);
		}
		if(self::savePagesToIgnore()) {
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_sitemap', self::DELETE_PAGE_TO_IGNORE_SUCCESS);
			return true;
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_sitemap', self::DELETE_PAGE_TO_IGNORE_ERROR);
			return false;
		}
	}
	
	public static function addPageToIgnore($sFilename) {
		self::init();
		self::$aExcludedFiles[$sFilename] = $sFilename;
		if(self::savePagesToIgnore()) {
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_sitemap', self::ADD_PAGE_TO_IGNORE_SUCCESS);
			return true;
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_sitemap', self::ADD_PAGE_TO_IGNORE_ERROR);
			return false;
		}
	}
	
	private static function savePagesToIgnore() {
		return file_put_contents(DATA_PATH.self::$sExcludedFilesFilename, json_encode(self::$aExcludedFiles));
	}
	/**
	* require an array with one key bey url. Each key are an array with one key per node for currently value
	* example for one url : array(0=>array('loc'=>'http://mySite.com', 'lastmod'=>'20120612'))
	* @return string
	*/
	public static function build() {
		try {
			self::init();
			$oXml = simplexml_load_string(self::$sTpl);
			foreach(self::getArrayMap() as $aUrl) {
				if(empty($aUrl['loc'])) {
					throw new GenericException('"loc" node is mandatory');
				}
				if(is_array($aUrl)) {
					$oEntry = $oXml->addChild('url');
					foreach($aUrl as $sNodeName => $sNodeValue) {
						if (in_array($sNodeName, self::$aAllowedNodes)) {
							$oEntry->addChild($sNodeName, self::escape($sNodeValue));
						}
					}
				}
			}
			self::writeRobotsTxt();
			file_put_contents(ROOT_PATH.self::$sSitemapFileName, trim($oXml->asXML()));
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_sitemap', self::SUCCESS_REGENERATE_SITEMAP);
			return true;
		} catch (Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_sitemap', self::ERROR_REGENERATE_SITEMAP);
			return false;
		}
	}
	
	private static function writeRobotsTxt() {
		return file_put_contents(
							ROOT_PATH.self::$sRobotsTxtFileName, 
							str_replace(
								'{__WEB_PATH__}', 
								SITE_URL_PROD, 
								self::$sRobotsTxtPatern
							)
						);
	}
	
	private static function escape($sInput) {
		return str_replace(
						array('&','\'','"','>','<'),
						array('&amp;','&apos;','&quot;','&gt;','&lt;'),
						$sInput
					);
	}
	
	private static function getArrayMap() {
		$aUrls = array();
		foreach(SessionCore::$oLang->getFrontAvailable() as $sLang) {
			foreach (self::getStaticPagesList() as $sPage) {
				$sPage = str_replace('_', '/', $sPage);
				$aUrls[] = array(
								'loc' 		=> SITE_URL_PROD.$sLang.'/'.$sPage.'.html',
								'lastmod' 	=> date('Y-m-d')
							);
			}
		}
		$oRoutingMgr = new RoutingMgr();
		foreach($oRoutingMgr->getAllRoutes('front') as $sPage=>$sServiceMethod) {
			$sPage = str_replace('_', '/', $sPage);
			if(!in_array($sPage, self::$aExcludedFiles)) {
				foreach(SessionCore::$oLang->getFrontAvailable() as $sLang) {
					$aPage = array(
									'loc' 		=> SITE_URL_PROD.$sLang.'/'.$sPage.'.html',
									'lastmod' 	=> date('Y-m-d')
								);

					if(!in_array($aPage, $aUrls)) {
						$aUrls[] = $aPage;
					}
				}
			}
		}
		unset($oRoutingMgr);
		return $aUrls;
	}
	
	public static function getStaticPagesList() {
		$aPages = array();
		foreach (new DirectoryIterator(CONTENT_TPL_PATH) as $oFileInfos) {
			$sTplName = $oFileInfos->getBasename('.tpl');
			if ($oFileInfos->isFile() && !in_array($sTplName, self::$aExcludedFiles)) {
				$aPages[$sTplName] = str_replace('_', '/', $sTplName);
			}
		}
		foreach(ModulesMgr::getModulesAvailable() as $sModuleName) {
			$sModuleTplPath = MODULES_PATH.$sModuleName.'/'.GEN_TPL_CONTENTS_PATH;
			if(!file_exists($sModuleTplPath)) {
				continue;
			}
			foreach (new DirectoryIterator($sModuleTplPath) as $oFileInfos) {
				$sTplName = $oFileInfos->getBasename('.tpl');
				if ($oFileInfos->isFile() && !in_array($sTplName, self::$aExcludedFiles)) {
					$aPages[$sTplName] = str_replace('_', '/', $sTplName);
				}
			}
		}
		sort($aPages);
		return $aPages;
	}
	
	public static function getPagesListFromConf() {
		$oPagesListMgr = new PagesListMgr();
		$aPagesList = array();
		foreach(self::getStaticPagesList() as $sPage) {
			if(!in_array($sPage, self::$aExcludedFiles)) {
				$aPagesList[$sPage] = str_replace('_', '/', $sPage);
			}
		}
		unset($oPagesListMgr);
		return $aPagesList;
	}
	
	public static function getCurrentSitemapInArray() {
		/*
		 * !!! A MIGRER !!!
		 * avec un extends SimpleXmlMgr
		 */
		if(!file_exists(ROOT_PATH.'sitemap.xml')) {
			return array();
		}
		$oXml = new ExtendedSimpleXMLElement(file_get_contents(ROOT_PATH.'sitemap.xml'), LIBXML_NOCDATA);
		$sJson = json_encode($oXml);
		$aUrls = json_decode($sJson,true);
		if(empty($aUrls['url'])) {
			return array();
		}
		return $aUrls['url'];
	}
	
	private static function getPagesToIgnoreBloc() {
		$sTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.'pages.sitemap.fileToIgnoreForm.tpl');
		$sBloc = '';
		foreach(self::$aExcludedFiles as $sPage) {
			$sBloc .= str_replace('{__PAGE__}', $sPage, $sTpl);
		}
		return $sBloc;
	}
	
	public static function getSitemapPage() {
		self::init();
		$sSitemapXml = '';
		$aSitemap = array();
		foreach(self::getPagesListFromConf() as $sPageName) {
			$aSitemap[] = Toolz_Tpl::getLi(str_replace('_', '/', $sPageName));
		}
		$oRoutingMgr = new RoutingMgr();
		foreach($oRoutingMgr->getAllRoutes('front') as $sPage=>$sServiceMethod) {
			if(!in_array($sPage, self::$aExcludedFiles)) {
				$sRoutedPage = Toolz_Tpl::getLi(str_replace('_', '/', $sPage));
				if(!in_array($sRoutedPage, $aSitemap)) {
					$aSitemap[] = $sRoutedPage;
				}
			}
		}
		unset($oRoutingMgr);
		foreach(self::getCurrentSitemapInArray() as $aPage) {
			$sSitemapXml .= Toolz_Tpl::getLi($aPage['loc']);
			$sLastMod = $aPage['lastmod'];
		}
		return str_replace(
					array(
						'{__SITEMAP__}', 
						'{__SITEMAP_XML__}', 
						'{__LAST_MOD__}', 
						'{__ROBOT_TXT__}',
						'{__PAGES_TO_IGNORE__}'
					), 
					array(
						implode('', $aSitemap), 
						$sSitemapXml, 
						$sLastMod, 
						file_get_contents(ROOT_PATH.self::$sRobotsTxtFileName),
						self::getPagesToIgnoreBloc()
					), 
					file_get_contents(ADMIN_CONTENT_TPL_PATH.'pages_sitemap.tpl')
				);
	}
}