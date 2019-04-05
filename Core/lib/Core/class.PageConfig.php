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
final class PageConfig extends PagesConf {
	
	const DEF = 'default';
	const SUCCESS_ADD_PAGE				= 'SUCCESS_ADD_PAGE';
	const SUCCESS_DELETE_PAGE			= 'SUCCESS_DELETE_PAGE';
	const SUCCESS_CONFIGURATION_SAVE	= 'SUCCESS_CONFIGURATION_SAVE';
	const ERROR_DELETE_STATIC			= 'ERROR_DELETE_STATIC';
	const ERROR_DELETE_DEFAULT			= 'ERROR_DELETE_DEFAULT';
	const SUCCESS_RENAME_PAGE			= 'SUCCESS_RENAME_PAGE';
	const ERROR_RENAME_STATIC			= 'ERROR_RENAME_STATIC';
	const ERROR_RENAME_DEFAULT			= 'ERROR_RENAME_DEFAULT';
	const ERROR_DYNAMIC_ROUTING			= 'ERROR_DYNAMIC_ROUTING';
	const ERROR_INVALID_VIEW			= 'ERROR_INVALID_VIEW';
	const ERROR_CAN_NOT_SAVE			= 'ERROR_CAN_NOT_SAVE';
	const PAGE_ALREADY_EXISTS			= 'PAGE_ALREADY_EXISTS';
	const SUCCESS_CREATE_PAGE			= 'SUCCESS_CREATE_PAGE';
	const ERROR_CAN_NOT_CREATE_PAGE		= 'ERROR_CAN_NOT_CREATE_PAGE';
	const ERROR_PAGE_NAME_EMPTY			= 'ERROR_PAGE_NAME_EMPTY';
	private $sPageName				= self::DEF;
	private $sFormatedPageName		= self::DEF;
	private $aPageConf				= array();
	private $aPagesList				= array();
	private $aStaticPagesList		= array();
	private $sInterfaceTpl			= 'pages_configuration.tpl';
	private $sClassPageConf			= 'class.PagesConf.php';
	private $sClassPageConfTpl		= 'class.PagesConf.tpl';
	private $sInputTplName			= 'pages_configuration.input.tpl';
	private $sInputTpl				= '';
	private $bForce					= false;
	private $oPagesListMgr			= NULL;
	
	public function __construct($sPageName='default', $bForce=false) {
		$this->aStaticPagesList = SitemapMgr::getStaticPagesList();
		$this->oPagesListMgr = new PagesListMgr();
		$aPagesListTmp = $this->oPagesListMgr->getPagesList();
		ksort($aPagesListTmp);
		$this->aPagesList = array_merge(
							array('default' => 'default'), 
							$aPagesListTmp
						);
		unset($aPagesListTmp);
		$this->sPageName = $sPageName;
		$this->bForce = $bForce;
		$this->sInputTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sInputTplName);
		return $this->setPage();
	}
	
	public function setPage() {
		$this->sFormatedPageName = str_replace('_', '/', $this->sPageName);
		if(ADMIN && !$this->bForce) {
			if (empty(parent::$aAdminPagesConf[$this->sPageName])) {
				parent::$aAdminPagesConf[$this->sPageName] = array();
			}
			$this->aPageConf = parent::$aAdminPagesConf[$this->sPageName];
		} else {
			if (empty(parent::$aPagesConf[$this->sPageName])) {
				parent::$aPagesConf[$this->sPageName] = array();
			}
			$this->aPageConf = parent::$aPagesConf[$this->sPageName];
		}
		return true;
	}
	
	public function getConfInterface() {
		$sConfigPageTpl = file_get_contents(ADMIN_CONTENT_TPL_PATH.$this->sInterfaceTpl);
		if (isset($this->aPageConf['robots'])) {
			$aRobots = explode(',', $this->aPageConf['robots']);
		} elseif($this->getDefaultConf('robots') !== false) {
			$aRobots = explode(',', $this->getDefaultConf('robots'));
		} else {
			$aRobots = array();
		}
		$sViewName = !empty($this->aPageConf['view']) ? $this->aPageConf['view'] : $this->getDefaultConf('view');
		$aViewsAvailable = View::getViewsListAvailable();
		unset($aViewsAvailable['admin'], $aViewsAvailable['draft']);
		return str_replace(
						array(
							'{__PAGE_LIST__}',
							'{__INDEX_CHECKED__}',
							'{__NOINDEX_CHECKED__}',
							'{__FOLLOW_CHECKED__}',
							'{__NOFOLLOW_CHECKED__}',
							'{__NOARCHIVE_CHECKED__}',
							'{__INDEX_ACTIVE__}',
							'{__NOINDEX_ACTIVE__}',
							'{__FOLLOW_ACTIVE__}',
							'{__NOFOLLOW_ACTIVE__}',
							'{__NOARCHIVE_ACTIVE__}',
							'{__ROBOTS_TOOLTIP__}',
							'{__META_TAGS_CONTENT__}',
							'{__TWITTER_CONTENT__}',
							'{__OPEN_GRAPH__}',
							'{__GOOGLE_CONTENT__}',
							'{__VIEW_LIST__}',
							'{__VIEW_NAME_TOOLTIP__}'
						), 
						array(
							Toolz_Form::optionsList($this->sFormatedPageName, $this->aPagesList),
							in_array('index', $aRobots) ? Toolz_Form::$sCheckedPattern : '',
							in_array('noindex', $aRobots) ? Toolz_Form::$sCheckedPattern : '',
							in_array('follow', $aRobots) ? Toolz_Form::$sCheckedPattern : '',
							in_array('nofollow', $aRobots) ? Toolz_Form::$sCheckedPattern : '',
							in_array('noarchive', $aRobots) ? Toolz_Form::$sCheckedPattern : '',
							in_array('index', $aRobots) ? Toolz_Form::$sActivePattern : '',
							in_array('noindex', $aRobots) ? Toolz_Form::$sActivePattern : '',
							in_array('follow', $aRobots) ? Toolz_Form::$sActivePattern : '',
							in_array('nofollow', $aRobots) ? Toolz_Form::$sActivePattern : '',
							in_array('noarchive', $aRobots) ? Toolz_Form::$sActivePattern : '',
							Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag('ROBOTS')),
							$this->getMetaTagsInterface(),
							$this->getTwitterInterface(),
							$this->getOgInterface(),
							$this->getGoogleInterface(),
							Toolz_Form::optionsList($sViewName, $aViewsAvailable),
							Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag('VIEW'))
						), 
						$sConfigPageTpl
					);
	}
	
	private function getOgInterface() {
		$aOgSection = array();
		if(!isset($this->aPageConf['og'])) {
			if(($this->aPageConf['og'] = $this->getDefaultConf('og')) === false) {
				$this->aPageConf['og'] = array();
			}
		}
		foreach(MetasTags::$aOgList as $sOgType) {
			$sNameAndId = 'og:'.$sOgType;
			$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag(Toolz_Format::formatTanslateNodeName($sNameAndId)));
			$sValue = isset($this->aPageConf['og'][$sOgType]) ? $this->aPageConf['og'][$sOgType] : '';
			$sItem = $this->getInputTpl(
							Toolz_Form::label($sNameAndId.$sTooltip, $sNameAndId, 'form-control text-left'),
							Toolz_Form::input('text', $sNameAndId, $sNameAndId, $sValue, 'form-control confInput')
					);
			$aOgSection[] = Toolz_Tpl::getLi($sItem);
		}
		return Toolz_Tpl::getUl(implode($aOgSection));
	}
	
	private function getTwitterInterface() {
		$aTwitterSection = array();
		if(!isset($this->aPageConf['twitter'])) {
			if(($this->aPageConf['twitter'] = $this->getDefaultConf('twitter')) === false) {
				$this->aPageConf['twitter'] = array();
			}
		}
		foreach(MetasTags::$aTwitterList as $sTwitterType=>$sValue) {
			$sNameAndId = $sTwitterType;
			if(strlen($sTwitterType) > 17) {
				$sDisplay = substr($sTwitterType, 0, 15).'...';
			} else {
				$sDisplay = $sTwitterType;
			}
			$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag(Toolz_Format::formatTanslateNodeName($sNameAndId)));
			$sValue = isset($this->aPageConf['twitter'][$sTwitterType]) ? $this->aPageConf['twitter'][$sTwitterType] : '';
			$sItem = $this->getInputTpl(
							Toolz_Form::label($sDisplay.$sTooltip, $sNameAndId, 'form-control text-left'),
							Toolz_Form::input('text', $sNameAndId, $sNameAndId, $sValue, 'form-control confInput')
					);
			$aTwitterSection[] = Toolz_Tpl::getLi($sItem);
		}
		return Toolz_Tpl::getUl(implode($aTwitterSection));
	}
	
	private function getGoogleInterface() {
		$aGoogleSection = array();
		if(!isset($this->aPageConf['google'])) {
			if(($this->aPageConf['google'] = $this->getDefaultConf('google')) === false) {
				$this->aPageConf['google'] = array();
			}
		}
		foreach(MetasTags::$aGoogleList as $sGoogleType) {
			$sNameAndId = $sGoogleType;
			$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag(Toolz_Format::formatTanslateNodeName($sNameAndId)));
			$sValue = isset($this->aPageConf['google'][$sGoogleType]) ? $this->aPageConf['google'][$sGoogleType] : '';
			$sItem = $this->getInputTpl(
							Toolz_Form::label($sNameAndId.$sTooltip, $sNameAndId, 'form-control text-left'),
							Toolz_Form::input('text', $sNameAndId, $sNameAndId, $sValue, 'form-control confInput')
					);
			$aGoogleSection[] = Toolz_Tpl::getLi($sItem);
		}
		return Toolz_Tpl::getUl(implode($aGoogleSection));
	}
	
	private function getMetaTagsInterface() {
		$aMetaTags = array();
		if(!isset($this->aPageConf['meta'])) {
			if(($this->aPageConf['meta'] = $this->getDefaultConf('meta')) === false) {
				$this->aPageConf['meta'] = array();
			}
		}
		foreach(MetasTags::$aAllowedNames as $sMetaName) {
			$sValue = isset($this->aPageConf['meta'][$sMetaName]) ? $this->aPageConf['meta'][$sMetaName] : '';
			if(empty($sValue)) {
				$oConfig = new Config();
				$mValue = $oConfig->getGlobalConf($sMetaName);
				unset($oConfig);
				if(is_array($mValue) || !$mValue) {
					$sValue = false;
				} else {
					$sValue = $mValue;
				}
			}
			$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag(Toolz_Format::formatTanslateNodeName($sMetaName)));
			$sItem = $this->getInputTpl(
							Toolz_Form::label($sMetaName.$sTooltip, $sMetaName, 'form-control text-left'),
							Toolz_Form::input('text', $sMetaName, $sMetaName, $sValue, 'form-control confInput')
					);
			$aMetaTags[] = Toolz_Tpl::getLi($sItem);
		}
		return Toolz_Tpl::getUl(implode($aMetaTags));
	}
	
	private function getInputTpl($sLabel, $sInput) {
		return str_replace(
						array(
							'{__LABEL__}',
							'{__INPUT__}'
						), 
						array(
							$sLabel,
							$sInput
						), 
						$this->sInputTpl
					);
	}

	public function save() {
		parent::$aPagesConf[$this->sPageName] = $this->formatToSave();
		if(!empty(parent::$aPagesConf[$this->sPageName]['routing'])) {
			
		}
		unset(parent::$aPagesConf[$this->sPageName]['routing']);
		$sFileContents = str_replace(
								'{__ARRAY_CONF__}', 
								var_export(parent::$aPagesConf, true), 
								file_get_contents(CORE_TPL_PATH.$this->sClassPageConfTpl)
						);
		$mFilePutContents = file_put_contents(DATA_PATH.$this->sClassPageConf, $sFileContents);
		if ($mFilePutContents !== false) {
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_configuration', self::SUCCESS_CONFIGURATION_SAVE);
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', self::ERROR_CAN_NOT_SAVE);
		}
		return $this->setPage();
	}
	
	private function formatToSave() {
		$aConf = $aRobots = $aOg = $aMeta = array();
		$aTwitter = $aGoogle = array();
		// Robots
		$aRobots[] = UserRequest::getParams('index');
		$aRobots[] = UserRequest::getParams('follow');
		if(UserRequest::getParams('noarchive') !== false) {
			$aRobots[] = UserRequest::getParams('noarchive');
		}
		$aConf['robots'] = implode(',', $aRobots);
		// META TAGS
		foreach(MetasTags::$aAllowedNames as $sMetaKey=>$sMetaName) {
			if(UserRequest::getParams($sMetaKey) !== '') {
				$aMeta[$sMetaName] = UserRequest::getParams($sMetaKey);
			}
		}
		$aConf['meta'] = $aMeta;
		// Open Graph
		foreach(MetasTags::$aOgList as $sOgType) {
			if(UserRequest::getParams('og:'.$sOgType) !== '') {
				$aOg[$sOgType] = UserRequest::getParams('og:'.$sOgType);
			}
		}
		$aConf['og'] = $aOg;
		// twitter
		foreach(MetasTags::$aTwitterList as $sTwitterType=>$sValue) {
			if(UserRequest::getParams($sTwitterType) !== '') {
				$aTwitter[$sTwitterType] = UserRequest::getParams($sTwitterType);
			} elseif(!empty($sValue)) {
				$aTwitter[$sTwitterType] = $sValue;
			}
		}
		$aConf['twitter'] = $aTwitter;
		// google
		foreach(MetasTags::$aGoogleList as $sGoogleType) {
			if(UserRequest::getParams($sGoogleType) !== '') {
				$aGoogle[$sGoogleType] = UserRequest::getParams($sGoogleType);
			}
		}
		$aConf['google'] = $aGoogle;
		//view
		if(UserRequest::getParams('view') !== false && UserRequest::getParams('view') !== '') {
			if(View::checkIfViewExists(UserRequest::getParams('view'))) {
				$aConf['view'] = UserRequest::getParams('view');
			} else {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', self::ERROR_INVALID_VIEW);
			}
		}
		return $aConf;
	}
	
	public function deletePage($sPageName)  {
		if ($sPageName === self::DEF) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', self::ERROR_DELETE_DEFAULT);
		} elseif (isset($this->aStaticPagesList[$sPageName])) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', self::ERROR_DELETE_STATIC);
		} else {
			unset(parent::$aPagesConf[$sPageName]);
			$sFileContents = str_replace(
									'{__ARRAY_CONF__}', 
									var_export(parent::$aPagesConf, true), 
									file_get_contents(CORE_TPL_PATH.'class.PagesConf.tpl')
							);
			file_put_contents(CORE_CLASS_PATH.'class.PagesConf.php', $sFileContents);
			$this->sPageName = self::DEF;
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_configuration', self::SUCCESS_DELETE_PAGE);
			return $this->setPage();
		}
		return false;
	}
	
	public function updatePage($sPageName, $sNewPageName)  {
		if ($sNewPageName === self::DEF || $sPageName === self::DEF) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', self::ERROR_RENAME_DEFAULT);
		} elseif (isset($this->aStaticPagesList[$sNewPageName]) || isset($this->aStaticPagesList[$sPageName])) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', self::ERROR_RENAME_STATIC);
		} else {
			if(isset(parent::$aPagesConf[$sPageName])) {
				parent::$aPagesConf[$sNewPageName] = parent::$aPagesConf[$sPageName];
				unset(parent::$aPagesConf[$sPageName]);
				$sFileContents = str_replace(
										'{__ARRAY_CONF__}', 
										var_export(parent::$aPagesConf, true), 
										file_get_contents(CORE_TPL_PATH.'class.PagesConf.tpl')
								);
				file_put_contents(DATA_PATH.'class.PagesConf.php', $sFileContents);
			}
			$this->sPageName = self::DEF;
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('pages_configuration', self::SUCCESS_RENAME_PAGE);
			return $this->setPage();
		}
		return false;
	}
	
	private function getDefaultConf($sIndex='') {
		if(ADMIN && !$this->bForce) {
			if(empty($sIndex)) {
				return parent::$aAdminPagesConf['default'];
			}
			return akead($sIndex, parent::$aAdminPagesConf['default'], false);
		} else {
			if(empty($sIndex)) {
				return parent::$aPagesConf['default'];
			}
			return akead($sIndex, parent::$aPagesConf['default'], false);
		}
	}
	
	public function getConf() {
		if(ADMIN && !$this->bForce) {
			if(!empty(parent::$aAdminPagesConf[$this->sPageName])) {
				return parent::$aAdminPagesConf[$this->sPageName];
			} else {
				return $this->getDefaultConf();
			}
		} else {
			if(!empty(parent::$aPagesConf[$this->sPageName])) {
				return parent::$aPagesConf[$this->sPageName];
			} else {
				return $this->getDefaultConf();
			}
		}
		return false;
	}
	
	public function getAllConfs() {
		return array(
				'front' => parent::$aPagesConf,
				'back' => parent::$aAdminPagesConf
			);
	}
}