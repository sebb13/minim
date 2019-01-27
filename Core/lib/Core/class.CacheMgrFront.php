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
final class CacheMgrFront extends CacheMgr {
	
	public function __construct(Lang $oLang) {
		$this->oLang	= $oLang;
		$this->sLocPath = LOC_PATH;
		$this->sTplPath = CONTENT_TPL_PATH;
		$this->sCachePath = CACHE_PATH;
		parent::__construct();
		return true;
	}
	
	public function resetCache() {
		$aData = $this->_resetCache();
		$oMinifier = new Minifier();
		$aData[] = $oMinifier->concatJs();
		$aData[] = $oMinifier->concatCss();
		$oAssetsLinker = new AssetsLinker();
		$oAssetsLinker->buildCSS();
		$oAssetsLinker->buildJS();
		unset($oMinifier, $oAssetsLinker);
		return $aData;
	}
	
	public function deleteCache($sPageName, $sExt=CacheMgr::DEFAULT_CACHE_EXT) {
		return $this->_deleteCache($sPageName, TranslationsMgr::TRANS_FRONT, $sExt);
	}
	
	public function buildSiteMap() {
		return SitemapMgr::build();
	}
}