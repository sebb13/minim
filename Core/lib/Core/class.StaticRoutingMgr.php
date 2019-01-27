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
final class StaticRoutingMgr {
	
	private $sStaticRoutingConf = 'router.xml';
	private $oXml = NULL;
	public $aStaticRouting = array();

	public function __construct() {
		$this->oXml = simplexml_load_file(CONF_PATH.$this->sStaticRoutingConf);
		foreach ($this->oXml as $sNodeName=>$sNodeValue) {
			$this->aStaticRouting[(string)$sNodeName] = (string)$sNodeValue;
		}
	}
	
	public function getStaticRouting() {
		return $this->aStaticRouting;
	}
	
	public function addRoute($sPage, $sClsMthd) {
		if(strpos($sClsMthd, '::') === false) {
			throw new CoreException('invalid argument');
		}
		$this->oXml->addChild($sPage, $sClsMthd);
		return $this->save();
	}
	
	public function removeRoute($sPage) {
		foreach ($this->oXml as $sNodeName=>$sNodeValue) {
			if((string)$sNodeName === $sPage) {
				unset($this->oXml->$sNodeName);
				break;
			}
		}
		return $this->save();
	}
	
	private function save() {
		return file_put_contents(
						CONF_PATH.$this->sStaticRoutingConf, 
						$this->oXml->asXML()
					);
	}
}