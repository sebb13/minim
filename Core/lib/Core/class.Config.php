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
final class Config extends SimpleXmlMgr {
	
	private $sConfFilePath		= '';
	private $sModuleName		= '';
	private $sTplPath			= 'system_conf.moduleContainer.tpl';
	private $sCssConfPath		= 'css.xml';
	private $sJsConfPath		= 'js.xml';
	private $sMainModule		= 'minim';
	private $aMinimConfToKeep	= array('DOMAIN_NAME',
									'EMAIL_CONTACT','ERROR_MAIL',
									'SITE_URL_PROD','SITE_URL_DEV','ADMIN_URL_PROD','ADMIN_URL_DEV',
									'SYS_DB_NEED','SYS_DB_HOST','SYS_DB_TYPE','SYS_DB_USER','SYS_DB_PWD',
									'SYS_DB_PROD_DATABASE','SYS_DB_DEV_DATABASE',
									'SYS_LAST_CACHE','SYS_LANG_FRONT','SYS_LANG_BACK');
	
	public function __construct($sModuleName='minim') {
		$this->sCssConfPath = DATA_PATH.$this->sCssConfPath;
		$this->sJsConfPath = DATA_PATH.$this->sJsConfPath;
		
		parent::__construct();
		return $this->loadConf($sModuleName);
	}
	
	private function loadConf($sModuleName) {
		if($sModuleName === $this->sModuleName) {
			return true;
		}
		if($sModuleName === $this->sMainModule) {
			// minim
			if(!file_exists(DATA_PATH.$sModuleName.'.conf.xml')) {
				throw new CoreException('Unknow module "'.$sModuleName.'"');
			} else {
				$this->sConfFilePath = DATA_PATH.$this->sMainModule.'.conf.xml';
			}
		} else {
			// module
			$sModuleConfPath = MODULES_PATH.$sModuleName.'/'.GEN_DATA_PATH.$sModuleName.'.conf.xml';
			if(!file_exists($sModuleConfPath)) {
				throw new CoreException('Unknow module "'.$sModuleName.'"');
			} else {
				$this->sConfFilePath = $sModuleConfPath;
			}
		}
		$this->sModuleName = $sModuleName;
		return $this->loadFile($this->sConfFilePath);
	}
	
	public function getConfInterface($sModuleName, $sCallback='') {
		$sItemTpl = file_get_contents(TPL_PATH.'/admin/parts/system_conf.item.tpl');
		$this->loadConf($sModuleName);
		$aConf = array();
		$aSysConf = array();
		foreach($this->getIemsList() as $sConfName=>$sConfValue) {
			if(empty($sConfValue)) {
				$sConfValue = '';
			}
			$sDisplayValue = '';
			$sDisplayConfName = Toolz_Format::cutWithEndDots(strtolower($sConfName), 30);
			$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag($sConfName));
			$sLabel = Toolz_Form::label($sDisplayConfName.$sTooltip, $sConfName, 'form-control');
			//s'il y a des options
			if(($aOptionsTmp = $this->getAttributes($sConfName)) !== false) {
				$aOptions = array();
				if(isset($aOptionsTmp['options']) && strpos($aOptionsTmp['options'], '|') !== false) {
					// si c'est un select
					$aOptionsTmp = explode('|', $aOptionsTmp['options']);
					foreach($aOptionsTmp as $sKey=>$sValue) {
						$aOptions[$sValue] = $sValue;
					}
					$sDisplayValue .= str_replace(
										'{__OPTIONS__}',
										Toolz_Form::optionsList($sConfValue, $aOptions),
										Toolz_Form::getSelect($sConfName, $sConfName, 'form-control')
								);
				} elseif(isset($aOptionsTmp['options']) && strpos($aOptionsTmp['options'], ',') !== false) {
					// si c'est des checkbox
					$aOptionsTmp = explode(',', $aOptionsTmp['options']);
					$aConfValues = explode(',', $sConfValue);
					foreach($aOptionsTmp as $sKey=>$sValue) {
						$aOptions[$sValue] = $sValue;
					}
					foreach($aOptions as $sOption) {
						$sInputName = $sConfName.'_'.$sOption;
						$sDisplayValue .= ' '.$sOption.':';
						$sConfValue = in_array($sOption, $aConfValues) ? $sOption : '';
						$sDisplayValue .= Toolz_Form::checkbox($sInputName, $sInputName, $sConfValue, $sOption, $aOptions);
					}
				} elseif(isset($aOptionsTmp['placeholder'])) {
					// si c'est un placeholder
					if(is_array($sConfValue)) {
						$sConfValue = '';
					}
					$sDisplayValue .= Toolz_Form::input(
													'text', 
													$sConfName, 
													$sConfName, 
													$sConfValue, 
													'form-control input-md', 
													false, 
													(string)$aOptionsTmp['placeholder']
												);
				} else {
					//type non reconnu
					throw new CoreException('invalid options "'.$aOptions['options'].'"');
				}
			} else {
				$bReadonly = (strpos($sConfName, 'SYS_') === 0 && strpos($sConfName, '_DB_')=== false) ? true : false;
				$sDisplayValue .= Toolz_Form::input('text', $sConfName, $sConfName, $sConfValue, 'form-control input-md', $bReadonly);
			}
			if(strpos($sConfName, 'SYS_') === 0) {
				$aSysConf[] = str_replace(
										array('{__LABEL__}','{__VALUE__}'), 
										array($sLabel, $sDisplayValue), 
										$sItemTpl
									);
			} else {
				$aConf[] = str_replace(
										array('{__LABEL__}','{__VALUE__}'), 
										array($sLabel, $sDisplayValue), 
										$sItemTpl
									);
			}
		}
		$sContent = str_replace(
						array(
							'{__MODULE_NAME__}',
							'{__CONF__}', 
							'{__SYS_CONF__}',
							'{__CALLBACK__}'
						),
						array(
							$this->sModuleName,
							!empty($aConf) ? implode($aConf) : '{__NO_CONFIGURATION__}',
							!empty($aSysConf) ? implode($aSysConf) : '{__NO_CONFIGURATION__}',
							$sCallback
						),
						file_get_contents(ADMIN_PARTS_TPL_PATH.$this->sTplPath)
					);
		$oTplMgr = new TplMgr(SessionCore::getLangObject());
		$sTransPath = ModulesMgr::getFilePath($sModuleName, 'backLocales', SessionCore::getLangObject()->LOCALE).strtolower($sModuleName).'.xml';
		return $oTplMgr->buildSimpleCacheTpl($sContent, $sTransPath);
	}
	
	public function getGlobalConf($sKey='') {
		return empty($sKey) ? $this->getIemsList() : $this->getIemsList($sKey);
	}
	
	public function setGlobalConf($sKey, $sValue) {
		return $this->setItem($sKey, $sValue);
	}
	
	/*
	 * pour les mises à jour, si une entrée doit être modifiée ou ajoutée.
	 */
	public function updateMinimConfFromXml($sXmlPath) {
		$aCurrentConf = $this->getGlobalConf();
		$aNewConf = array();
		$oXml = new ExtendedSimpleXMLElement(file_get_contents($sXmlPath));
		$aItemsList = $this->xml2array($oXml);
		foreach ($aItemsList as $sConfName=>$sConfValue) {
			if(!in_array($sConfName, $this->aMinimConfToKeep)) {
				$aNewConf[$sConfName] = $sConfValue;
			} else {
				$aNewConf[$sConfName] = $aCurrentConf[$sConfName];
			}
		}
		unset($aItemsList, $oXml);
		return $this->saveGlobalConf($aNewConf);
	}
	
	public function saveGlobalConf(array $aConf, $bDeleteConf=false) {
		// on ne modifie pas ici les configurations système.
		$aCurrentConf = $this->getIemsList();
		if(!$bDeleteConf) {
			//récupération des éventuelles entrées manquantes
			foreach($aCurrentConf as $sKey=>$sValue) {
				if(empty($aConf[$sKey])) {
					$aConf[$sKey] = $sValue;
				}
			}
		}
		foreach($aConf as $sConfName=>$sConfValue) {
			if(strpos($sConfName, 'SYS_') === 0) {
				$aConf[$sConfName] = $aCurrentConf[$sConfName];
			}
			// Gestion des checkbox pour les choix multiples
			if(($aOptionsTmp = $this->getAttributes($sConfName)) !== false) {
				if(isset($aOptionsTmp['options']) && strpos($aOptionsTmp['options'], ',') !== false) {
					$aConfValues = array();
					$aOptions = explode(',', $aOptionsTmp['options']);
					foreach($aOptions as $sOptionValue) {
						if(isset($aConf[$sConfName.'_'.$sOptionValue])) {
							unset($aConf[$sConfName.'_'.$sOptionValue]);
							$aConfValues[] = $sOptionValue;
						}
					}
					$aConf[$sConfName] = implode(',', $aConfValues);
				}
			}
		}
		//On remonte le XML
		$oNewXml = $this->array2xml($this->getEmptyXmlObject('conf'), $aConf);
		// récupérations des différents attributs en place
		foreach($aCurrentConf as $sKey=>$sValue) {
			if(($aAttributes = $this->getAttributes($sKey)) !== false) {
				foreach($aAttributes as $sAttName=>$sAttValue) {
					// on replace les attributs
					$oNewXml->$sKey->addAttribute($sAttName, $sAttValue);
				}
			}
		}
		ModulesMgr::setModulesAvailable();
		return $this->save2path($oNewXml, $this->sConfFilePath);
	}
}