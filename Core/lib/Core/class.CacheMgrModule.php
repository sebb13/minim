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
final class CacheMgrModule extends CacheMgr {
	 
	public static function resetSimpleModuleCache($sModuleName, $sPageName) {
		$sTplPath = MODULES_PATH.$sModuleName.'/'.GEN_TPL_CONTENTS_PATH;
		$sLocalesPath = MODULES_PATH.$sModuleName.'/'.GEN_LOC_PATH;
		$oLang = SessionCore::getLangObject();
		$oTplMgr = new TplMgr($oLang, $sLocalesPath, $sTplPath);
		foreach (new DirectoryIterator($sLocalesPath) as $oFileInfos) {
			if ($oFileInfos->isDir() && !$oFileInfos->isDot() && in_array($oFileInfos->getFilename(), LangSwitcher::$aFlagsName)) {
				$sLang = $oFileInfos->getFilename();
				$oLang->LOCALE = $sLang;
				$sCache = $oTplMgr->buildMultilingualTpl($sTplPath.$sPageName.'.tpl', $sLang);
				file_put_contents(
                                CACHE_PATH.$sPageName.'_'.$sLang.'.html', 
                                Minifier::minifyHtml($sCache)
                        );
			}
		}
		$oLang->LOCALE = UserRequest::getLang();
		$oConf = new Config($sModuleName);
		$oConf->setGlobalConf('SYS_LAST_CACHE', date('Ymd - H:i:s')); //20170313 - 11:04:28
		unset($oConf, $oTplMgr);
		return true;
	}
}