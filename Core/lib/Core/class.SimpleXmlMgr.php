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
abstract class SimpleXmlMgr {
	
	private $sXmlPath = '';
	private $oXml = NULL;
	private $aItemsList = array();
	private $sEmptyXmlPattern = '<?xml version="1.0"?><{__ROOT__}></{__ROOT__}>';

	public function __construct($sXmlPath='') {
		if(!empty($sXmlPath)) {
			return $this->loadFile($sXmlPath);
		}
		return true;
	}
	
	protected function loadFile($sXmlPath) {
		$this->sXmlPath = $sXmlPath;
		$this->oXml = new ExtendedSimpleXMLElement(file_get_contents($this->sXmlPath), LIBXML_NOCDATA);
		$this->aItemsList = $this->xml2array($this->oXml);
		return true;
	}
	
	protected function getIemsList($sItem='') {
		if(empty($sItem)) {
			return $this->aItemsList;
		}
		return !empty($this->aItemsList[$sItem]) ? $this->aItemsList[$sItem] : false;
	}
	
	protected function getAttributes($sNodeName) {
		try {
			$aReturn = (array)$this->oXml->$sNodeName->attributes();
			return !empty($aReturn["@attributes"]) ? $aReturn["@attributes"] : false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	protected function getFormatedTag($sTag) {
		return 'item-'.preg_replace('#[^a-zA-Z0-9]#', '', $sTag);
	}
	
	protected function addItem($sItemName, $sItemValue) {
		$this->oXml->addChildCData($sItemName, $sItemValue);
		return $this->save();
	}
	
	protected function removeItem($sItem) {
		foreach ($this->oXml as $sNodeName=>$sNodeValue) {
			if((string)$sNodeName === $sItem) {
				unset($this->oXml->$sNodeName);
				break;
			}
		}
		return $this->save();
	}
	
	protected function setItem($sKey, $sValue) {
		if (!empty($this->oXml->$sKey)) {
			$this->oXml->$sKey = $sValue;
			return $this->save();
		} else {
			return $this->addItem($sKey, $sValue);
		}
	}
	
	protected function save() {
		$this->oXml->asXML($this->sXmlPath);
		return $this->loadFile($this->sXmlPath);
	}
	
	protected function xml2array($oXml, $aOut=array()) {
		$sJson = json_encode($oXml);
		$aTmp = json_decode($sJson,true);
		if(empty($aOut)) {
			return $aTmp;
		}
		return array_merge($aOut, $aTmp);
	}
	
	protected function array2xml($oXml, array $aData) {
		foreach($aData as $mKey => $mValue) {
			if(is_array($mValue)) {
				if(is_numeric($mKey)){
					$mKey = 'item'.$mKey; //pour les indexes numériques
				}
				$oSubnode = $oXml->addChild($mKey);
				$this->array2xml($oSubnode, $mValue);
			} else {
				$oXml->addChildCData((string)$mKey, (string)$mValue);
			}
		}
		return $oXml;
	}
	
	protected function getEmptyXmlObject($sRootName) {
		return new ExtendedSimpleXMLElement(
								str_replace(
									'{__ROOT__}', 
									$sRootName, 
									$this->sEmptyXmlPattern
								)
							);
	}
	
	protected function save2path($oXml, $sPath) {
		return $oXml->asXML($sPath);
	}
	
	protected function save2pathFromArray($sRoot, $sPath, array $aData) {
		$oXml = $this->array2xml($this->getEmptyXmlObject($sRoot), $aData);
		return $this->save2path($oXml, $sPath);
	}
}