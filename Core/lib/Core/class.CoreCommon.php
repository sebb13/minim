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
abstract class CoreCommon {
	
	protected $sCoreTransPath		= '';
	protected $sCoreResultTpl		= '';
	protected $oLang				= null;
	protected $oTplMgr				= null;
	
	public function __construct() {
		$this->oLang = SessionCore::getLangObject();
		$this->sCoreTransPath = str_replace(
										'{__LANG__}', 
										$this->oLang->LOCALE, 
										CORE_TRANSLATIONS_PATH
									);
		$this->oTplMgr = new TplMgr($this->oLang);
		$this->sCoreResultTpl = file_get_contents(CORE_RESULT_TPL_PATH);
		if(UserRequest::getParams('app_token') === false) {
			UserRequest::setParams('app_token', SessionCore::getSessionHash());
		}
	}
	
	protected function getCoreResult($sContent, array $aOther=array()) {
		if(empty($aOther['sPage'])) {
			$aOther['sPage'] = UserRequest::getRequest('sPage');
		}
		return array_merge($aOther, array(
					'content' => $this->oTplMgr->buildSimpleCacheTpl(
															str_replace(
																'{__CONTENTS__}', 
																$sContent, 
																$this->sCoreResultTpl
															), 
															$this->sCoreTransPath
														)
													)
				);
	}
	
	protected function getVersionSignature() {
		
	}
}