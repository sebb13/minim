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
	private static $sTpl = '<?xml version="1.0" encoding="UTF-8"?>
							<urlset
								xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
								xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
								 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
							</urlset>';
	private static $sRobotsTxtPatern	= 'Sitemap: {__WEB_PATH__}sitemap.xml';
	private static $sRobotsTxtFileName	= 'robots.txt';
	private static $sSitemapFileName	= 'sitemap.xml';
	private static $aAllowedNodes		= array('loc','lastmod','changefreq','priority');
	private static $aExcludedFiles		= array('404','menu','index.php', 'thankyou');
	
	/**
	* require an array with one key bey url. Each key are an array with one key per node for currently value
	* example for one url : array(0=>array('loc'=>'http://mySite.com', 'lastmod'=>'20120612'))
	* @return string
	*/
	public static function build() {
		try {
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
		$aRoutes = $oRoutingMgr->getAllRoutes('front');
		unset($oRoutingMgr);
		foreach($aRoutes as $sPage=>$sServiceMethod) {
			$sPage = str_replace('_', '/', $sPage);
			$aPage = array(
							'loc' 		=> SITE_URL_PROD.$sLang.'/'.$sPage.'.html',
							'lastmod' 	=> date('Y-m-d')
						);
			if(!in_array($aPage, $aUrls)) {
				$aUrls[] = $aPage;
			}
		}
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
			$aPagesList[$sPage] = str_replace('_', '/', $sPage);
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
	
	public static function getSitemapPage() {
		$sSitemap = $sSitemapXml = '';
		$aSitemap = array();
		foreach(self::getPagesListFromConf() as $sPageName) {
			$aSitemap[] = Toolz_Tpl::getLi(str_replace('_', '/', $sPageName));
		}
		$oRoutingMgr = new RoutingMgr();
		$aRoutes = $oRoutingMgr->getAllRoutes('front');		
		unset($oRoutingMgr);
		foreach($aRoutes as $sPage=>$sServiceMethod) {
			$sRoutedPage = Toolz_Tpl::getLi(str_replace('_', '/', $sPage));
			if(!in_array($sRoutedPage, $aSitemap)) {
				$aSitemap[] = $sRoutedPage;
			}
		}
		foreach(self::getCurrentSitemapInArray() as $aPage) {
			$sSitemapXml .= Toolz_Tpl::getLi($aPage['loc']);
			$sLastMod = $aPage['lastmod'];
		}
		return str_replace(
					array(
						'{__SITEMAP__}', 
						'{__SITEMAP_XML__}', 
						'{__LAST_MOD__}', 
						'{__ROBOT_TXT__}'
					), 
					array(
						implode('', $aSitemap), 
						$sSitemapXml, 
						$sLastMod, 
						file_get_contents(ROOT_PATH.self::$sRobotsTxtFileName)
					), 
					file_get_contents(ADMIN_CONTENT_TPL_PATH.'pages_sitemap.tpl')
				);
	}
}