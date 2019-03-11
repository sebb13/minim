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
final class TplMgr {

    private $oLang = NULL;
    private $sLocPath = '';
    private $sTplPath = '';

    public function __construct(Lang $oLang, $sLocPath='', $sTplPath='') {
        $this->oLang = $oLang;
        $this->sLocPath = $sLocPath;
        $this->sTplPath = $sTplPath;
    }

    public function buildMultilingualTpl($sTplPath, $sLang) {
        $this->oLang->LOCALE = $sLang;
        $sTplName = basename($sTplPath, '.tpl');
        $sCache = file_get_contents($sTplPath);
        // -- si pas de traduction pour ce template
        if(!file_exists($this->sLocPath.$sLang.'/'.$sTplName.'.xml')) {
            return $this->addCommonElements($sCache);
        }
        return $this->buildTpl($sCache, $this->sLocPath.$sLang.'/'.$sTplName.'.xml');
    }

    public function buildSimpleFileTpl($sTplPath, $sXmlPath) {
        $sCache = file_get_contents($sTplPath);
        return $this->buildTpl($sCache, $sXmlPath);
    }
	
    public function buildSimpleCacheTpl($sCache, $sXmlPath) {
        return $this->buildTpl($sCache, $sXmlPath);
    }
	
	private function buildTpl($sCache, $sXmlPath) {
		//COMMON TRANSLATIONS
		$oXml = simplexml_load_file(COMMON_LOC_PATH.$this->oLang->LOCALE.'/common.xml');
		foreach($oXml as $sNodeName=>$sNodeValue) {
			$sCache = str_replace(
								$this->oLang->formatToTranslate($sNodeName), 
								$sNodeValue, 
								$sCache
							);
		}
		//TRANSLATIONS
		if(file_exists($sXmlPath)) {
			$oXml = simplexml_load_file($sXmlPath);
			foreach($oXml as $sNodeName=>$sNodeValue) {
				$sCache = str_replace(
									$this->oLang->formatToTranslate($sNodeName), 
									$sNodeValue, 
									$sCache
								);
			}
		}
        return $this->addCommonElements($sCache);
	}
	
	public static function addSmiley($sCache) {
		
	}

    private function addCommonElements($sCache) {
		$aSearch = $aReplace = array();
		foreach($this->oLang->aCommonMessages as $sKey=>$sValue) {
			$aSearch[] = $this->oLang->formatToTranslate($sKey);
			$aReplace[] = $sValue;
		}
		if(DEV) {
			$sWebPath = ADMIN ?  ADMIN_URL_DEV : SITE_URL_DEV;
		} else {
			$sWebPath = ADMIN ?  ADMIN_URL_PROD : SITE_URL_PROD;
		}
		$aSearchAdd = array(
						'{##WEB_PATH##}',
						'{##SITE_URL##}',
						'{##FRONT_URL##}',
						'{##STATIC_SERVER_URL##}',
						'{##EMAIL_CONTACT##}',
						'{##LANG##}',
						'{##PAGE##}',
						'{##APP_TOKEN##}',
						'{##DOMAIN_NAME##}',
						'{##YEAR##}'
					);
		$aReplaceAdd = array(
						$sWebPath,
						SITE_URL,
						DEV ? SITE_URL_DEV : SITE_URL_PROD,
						DEV ? STATIC_DEV_SERVER_URL : STATIC_SERVER_URL,
						is_array(EMAIL_CONTACT) ? '' : EMAIL_CONTACT,
						$this->oLang === NULL ? DEFAULT_LANG : $this->oLang->LOCALE,
						UserRequest::getPage(),
						SessionCore::getSessionHash(),
						DOMAIN_NAME,
						date('Y')
					);
        return str_replace(
					array_merge($aSearch,$aSearchAdd), 
					array_merge($aReplace, $aReplaceAdd),
					$sCache
				);
    }
}