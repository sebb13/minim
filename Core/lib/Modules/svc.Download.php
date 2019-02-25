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
final class Download extends CoreCommon {
	
	private $oDownloadMgr = NULL;
	
	public function __construct() {
		parent::__construct();
		$this->oDownloadMgr = new DownloadMgr();
	}

	public function startDownload() {
		$this->oDownloadMgr->startDownload();
	}
	
	public function getDashboard() {
		$sTitle = Toolz_Tpl::getA('/'.UserRequest::getPage().'/download/home.html', '{__DOWNLOAD_MGR_HOME_TITLE__}');
		$sDashboard = Dashboard::getDashboard($sTitle, $this->oDownloadMgr->getDownloadRawStats(6));
		return $this->oTplMgr->buildSimpleCacheTpl(
												$sDashboard, 
												ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'download.xml'
											);
	}
	
	public function getHomePage() {
		return array(
			'content' => $this->oTplMgr->buildSimpleCacheTpl(
												$this->oDownloadMgr->getHomePage(), 
												ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'download.xml'
											),
			'sPage'	=> 'download_home'
		);
	}
	
	public function getManageFilesPage() {
		return array(
			'content' => $this->oTplMgr->buildSimpleCacheTpl(
												$this->oDownloadMgr->getManageFilesPage(), 
												ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'download.xml'
											),
			'sPage'	=> 'download_manageFiles'
		);
	}
	
	public function addFile() {
		$this->oDownloadMgr->addFile(UserRequest::getParams());
		return $this->getManageFilesPage();
	}
	
	public function deleteFile() {
		$this->oDownloadMgr->deleteFile(UserRequest::getParams('fileId'));
		return $this->getManageFilesPage();
	}
}