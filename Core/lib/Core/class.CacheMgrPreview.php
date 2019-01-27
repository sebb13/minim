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
final class CacheMgrPreview extends CacheMgr {
	
	public function __construct(Lang $oLang, $sTransType) {
		if($sTransType === TranslationsMgr::TRANS_BACK) {
			$this->sLocPath = DRAFTS_ADMIN_LOC_PATH;
			$this->sTplPath = DRAFTS_ADMIN_TPL_PATH;
			$this->sCachePath = DRAFTS_ADMIN_CACHE_PATH;
		} else {
			$this->sLocPath = DRAFTS_LOC_PATH;
			$this->sTplPath = DRAFTS_TPL_PATH;
			$this->sCachePath = DRAFTS_CACHE_PATH;
		}
		$this->oLang = $oLang;
		parent::__construct();
		return true;
	}
	
	public function resetCache() {
		try{
			$this->_resetCache();
		} catch (Exception $ex) {
			echo $ex->getMessage();
		}
	}
}