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
final class Drafts extends CoreCommon {
	
	private $oDraftMgr = NULL;
	
	public function __construct() {
		parent::__construct();
		$this->oDraftMgr = new DraftMgr();
	}
	
	public function getDraft($sPagename) {
		$sContent = $this->oTplMgr->buildSimpleCacheTpl(
													file_get_contents(DRAFTS_TPL_PATH.$sPagename.'.tpl'), 
													DRAFTS_LOC_PATH.UserRequest::getRequest('sLang').'/'.$sPagename.'.xml'
												);
		return $this->getCoreResult($sContent);
	}
	
	public function getDraftUrl() {
		return $this->oDraftMgr->getDraftUrl(
									UserRequest::getParams('sPage'), 
									UserRequest::getParams('sLang'),
									UserRequest::getParams('sSide')
								);
	}
	
	public function __destruct() {
		$this->oDraftMgr = null;
	}
}	