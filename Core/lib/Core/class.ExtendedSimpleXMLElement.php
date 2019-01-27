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
final class ExtendedSimpleXMLElement extends SimpleXMLElement {
	
	/**
	* Add CDATA text in a node
	* @param string $sCdataText The CDATA value to add
	*/
	private function addCData($sCdataText) {
		$oNode = dom_import_simplexml($this);
		$oNewNode = $oNode->ownerDocument;
		$oNode->appendChild($oNewNode->createCDATASection($sCdataText));
		return true;
	}

	/**
	* Create a child with CDATA value
	* @param string $sName The name of the child element to add.
	* @param string $sCdataText The CDATA value of the child element.
	*/
	public function addChildCData($sName, $sCdataText) {
		$oChild = $this->addChild($sName);
		$oChild->addCData($sCdataText);
		return true;
	}

	/**
	* Add SimpleXMLElement code into a SimpleXMLElement
	* @param SimpleXMLElement $oNode
	*/
	public function appendXML($oNode) {
		if ($oNode) { // oO à corriger !!!
			if (strlen(trim((string)$oNode))===0) {
				$oXml = $this->addChild($oNode->getName());
				foreach($oNode->children() as $oChild) {
					$oXml->appendXML($oChild);
				}
			} else {
				$oXml = $this->addChild($oNode->getName(), (string)$oNode);
			}
			foreach($oNode->attributes() as $sName => $sValue) {
				$oXml->addAttribute($sName, $sValue);
			}
			return true;
		}
		return false;
	}

	public function removeNode($sPath, $sMulti='all') {
		$aResult = $this->xpath($sPath);
		// for wrong $path
		if (!isset($aResult[0])) {
			return false;
		}
		switch ($sMulti) {
			case 'all':
				foreach ($aResult as $r) unset ($r[0]);
				return true;
				break;
			case 'child':
				unset($aResult[0][0]);
				return true;
				break;
			case 'one':
				if (count($aResult[0]->children())==0 && count($aResult)==1) {
					unset($aResult[0][0]);
					return true;
				}
				break;
			default:
				return false;
		}
		return true;
	}
}