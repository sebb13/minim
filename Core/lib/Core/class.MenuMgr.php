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
final class MenuMgr {
	
	private $oDom = NULL;
	private $oLang = NULL;
	private $sCurrentSectionClass = '';
	private $slangsImgUrl = '';
	private $sLangsClassName = '';
	private $sMaintenanceURL = '';
	private $sMaintenanceClassName = '';
	private $sLangsFilename = 'menu';
	private $aLangs = array();

	public function __construct(Lang $oLang) {
		$this->oDom	= new DOMDocument('1.0', 'UTF-8');
		$this->oLang	= $oLang;
	}

	public function getHTML($sXml, $_sLangsFilename = 'menu') {
		$this->sLangsFilename = $_sLangsFilename;
		return $this->menuBuilder($sXml);
	}

	private function menuBuilder($sXml) {
		$oXml = new SimpleXMLElement($sXml);
		$this->sCurrentSectionClass		= (string)$oXml->config->currentSectionClass;
		$this->sMaintenanceURL			= (string)$oXml->config->maintenanceURL;
		$this->sMaintenanceClassName	= (string)$oXml->config->maintenanceClassName;
		$oRoot = $this->oDom->createElement((string)$oXml->config->containerTag);
		foreach ($oXml->config->containerParam as $oParam) {
			foreach ($oParam->attributes() as $sAttName=>$sAttValue) {
				$oRoot->setAttribute($sAttName, $sAttValue);
			}
		}
		$this->oDom->appendChild($oRoot);
		$this->recursiveBuilder($oXml->entries->children(), $oRoot);
		if ((string)$oXml->config->langSwitcher === 'true') {
			$this->aLangs = explode(',', (string)$oXml->config->langsAvailables);
			$this->slangsImgUrl = (string)$oXml->config->langsImgUrl;
			$this->sLangsClassName = (string)$oXml->config->langsClassName;
			$this->getLangSwitcher($oRoot);
		}
		return $this->oDom->saveHTML();
	}

	private function recursiveBuilder(SimpleXMLElement $oXml, DOMElement $oParentNode) {
		$oSectionNode = $this->oDom->createElement('ul');
		foreach ($oXml as $oEntry) {
			if (isset($oEntry['require'])) {
				switch((string)$oEntry['require']) {
					// -- préférfer un CDATA avec la methode à utiliser...
					case 'connected' :
						if (!SessionUser::isLogged()) {
							continue(2);
						}
						break;
					case 'offline' :
						if (SessionUser::isLogged()) {
							continue(2);
						}
						break;
				}
			}
			$oSubSectionNode = $this->entryBuilder($oEntry);
			$oSectionNode->appendChild($oSubSectionNode);
			$oChilds = $oEntry->children();
			if (count($oChilds) > 0) {
				$this->recursiveBuilder($oChilds, $oSubSectionNode);
			}
		}
		$oParentNode->appendChild($oSectionNode);
	}

	private function entryBuilder(SimpleXMLElement $oXml) {
		$oLiNode = $this->oDom->createElement('li');
		$oLinkNode = $this->oDom->createElement('a');
		if (isset($oXml['in_maintenance']) && (string)$oXml['in_maintenance'] === '1') {
			$oLinkNode->setAttribute('href', $this->sMaintenanceURL);
			$oLinkNode->setAttribute('class', $this->sMaintenanceClassName);
		} else {
			$sLink = str_replace('LANG', SessionLang::getLang(), (string)$oXml['link']);
			$oLinkNode->setAttribute('href', $sLink);
			if (!empty($oXml['class'])) {
				$sClass = (string)$oXml['class'];
				if (SessionNav::getCurrentPage() === (string)$oXml['id']) {
					$sClass .= ' '.$this->sCurrentSectionClass;
				}
				$oLinkNode->setAttribute('class', $sClass);
			}
			if (!empty($oXml['rel'])) {
				$oLinkNode->setAttribute('rel', (string)$oXml['rel']);
			}
			if (!empty($oXml['alt'])) {
				$oLinkNode->setAttribute('alt', (string)$oXml['alt']);
			}
		}
		$oLinkLabel = $this->oDom->createTextNode(
										$this->oLang->getMsg(
														$this->sLangsFilename,
														(string)$oXml['tag']
												)
									);
		$oLinkNode->appendChild($oLinkLabel);
		$oLiNode->appendChild($oLinkNode);
		return $oLiNode;
	}

	private function getLangSwitcher(DOMElement $oRootNode) {
		$oDiv = $this->oDom->createElement('div');
		$oDiv->setAttribute('id', 'langSwitcher');
		$oRootNode->appendChild($oDiv);
		$sCurrentLang = strtolower($this->oLang->LOCALE);
		foreach ($this->aLangs as $sLang) {
			$oLinkNode = $this->oDom->createElement('a');
			if (!empty($_GET['Lang'])) {
				unset($_GET['Lang']);
			}
			$sGet = !empty($_GET) ? '?Lang='.$sLang.'&'.http_build_query($_GET) : '?Lang='.$sLang;
			$oLinkNode->setAttribute('href', SessionNav::getCurrentPage().$sGet);
			$sClassName = $this->sLangsClassName;
			$sClassName .= $sCurrentLang === $sLang ? ' on' : ' off';
			$oLinkNode->setAttribute('class', $sClassName);
			$oLinkLabel = $this->oDom->createElement('img');
			$oLinkLabel->setAttribute('src', $this->slangsImgUrl.$sLang.'.gif');
			$oLinkLabel->setAttribute('alt', $sLang);
			$oLinkNode->appendChild($oLinkLabel);
			$oDiv->appendChild($oLinkNode);
		}
	}
	
	public function isDownForMaintenance($sPageName) {
		
	}
	
	private function getPagesInMaintenance(SimpleXMLElement $oXml) {
		if (isset($oXml['in_maintenance']) && (string)$oXml['in_maintenance'] === '1') {
			$oLinkNode->setAttribute('href', $this->sMaintenanceURL);
			$oLinkNode->setAttribute('class', $this->sMaintenanceClassName);
		} else {
			$oLinkNode->setAttribute('href', (string)$oXml['link']);
		}
	}

	public function __destruct() {
		$this->oDom = NULL;
		$this->oLang = NULL;
	}
}