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
final class Versions extends CoreCommon {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getPagesVersionsInterface() {
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															VersionsContentsMgr::getVersionsInterface(), 
															ADMIN_LOC_PATH.$this->oLang->LOCALE.'/pages_versions.xml'
														),
				'sPage'	=> 'pages_versions'
			); 
	}
	
	public function getVersionUrl() {
		if(!empty(UserRequest::getParams('sVersion'))) {
			UserRequest::setParams(
								'sPage', 
								str_replace(
									UserRequest::getParams('sVersion').'_', 
									'', 
									UserRequest::getParams('sPage')
								)
							);
		}
		return VersionsContentsMgr::getVersionUrl(
										UserRequest::getParams('sPage'), 
										UserRequest::getParams('sLang'), 
										UserRequest::getParams('sVersion')
									);
	}
	
	public function applyVersion() {
		// créer un draft de la version courante avant ?
		try {
			VersionsContentsMgr::applyVersion(
										TranslationsMgr::TRANS_FRONT,
										UserRequest::getParams('sPage'),
										UserRequest::getParams('sVersion')
									);
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_versions', VersionsContentsMgr::SUCCESS_APPLY_VERSION_MSG);
		} catch(CoreException $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_versions', VersionsContentsMgr::ERROR_APPLY_VERSION_MSG);
		}
		return $this->getPagesVersionsInterface();
	}
	
	public function purgeVersions() {
		try {
			VersionsContentsMgr::purgeVersions(UserRequest::getParams('sPage'));
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_versions', VersionsContentsMgr::SUCCESS_PURGE_VERSIONS_MSG);
		} catch(CoreException $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_versions', VersionsContentsMgr::ERROR_PURGE_VERSIONS_MSG);
		}
		return $this->getPagesVersionsInterface();
	}
	
	public function deleteVersion() {
		try {
			VersionsContentsMgr::deleteVersion(UserRequest::getParams('sPage'));
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_versions', VersionsContentsMgr::SUCCESS_PURGE_VERSIONS_MSG);
		} catch(CoreException $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_versions', VersionsContentsMgr::ERROR_PURGE_VERSIONS_MSG);
		}
		return $this->getPagesVersionsInterface();
	}
}