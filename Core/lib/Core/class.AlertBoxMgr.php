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
final class AlertBoxMgr {
	
	private $sAlertTplFilename		= 'alert.tpl';
	private $sModalBoxTplFilename	= 'modal.box.tpl';
	private $sGenericAlertTplPath	= 'generic.alert.tpl';
	private $sAlertTpl				= '';
	private $aAllowedTypes			= array('success','info','warning','danger');
	public $aMessages				= array();
	
	public function __construct() {
		$this->sAlertTpl = file_get_contents(CORE_TPL_PATH.$this->sAlertTplFilename);
		return $this->reset();
	}
	
	private function reset() {
		$this->aMessages = array(
							'success'	=> array(),
							'info'		=> array(),
							'warning'	=> array(),
							'danger'	=> array()
						);
		return true;
	}
	
	public function __set($sType, $sMsg) {
		if (in_array($sType, $this->aAllowedTypes)) {
			$this->aMessages[$sType][] = $sMsg;
			return true;
		} else {
			return false;
		}
	}
	
	public function getAlert($sType, $sMsg) {
		if (!in_array($sType, $this->aAllowedTypes)) {
			throw new CoreException('invalid type : '.$sType);
		}
		if(UserRequest::getRequest('bInDraftMode')) {
			$sAlertTpl = $this->sGenericAlertTplPath;
		} else {
			$sAlertTpl = $this->sAlertTpl;
		}
		return str_replace(
						array('{__TYPE__}','{__CONTENTS__}'), 
						array($sType, $sMsg), 
						$this->sAlertTpl
				);
	}
	
	public function getAllAlerts() {
		$sAlerts = '';
		foreach ($this->aAllowedTypes as $sType) {
			$aMsg = array();
			if (!empty($this->aMessages[$sType])) {
				foreach($this->aMessages[$sType] as $sMsg) {
					$aMsg[] = Toolz_Tpl::getLi($sMsg);
				}
				$sMsgs = Toolz_Tpl::getUl(implode($aMsg));
				$sAlerts .= $this->getAlert($sType, $sMsgs);
			}
		}
		$this->reset();
		return $sAlerts;
	}
	
	public function getModalBox($sId,$sHeader,$sContents) {
		return str_replace(
						array(
							'{__BOX_ID__}',
							'{__BOX_HEADER__}',
							'{__BOX_CONTENTS__}'
						), 
						array(
							$sId,
							$sHeader,
							$sContents
						), 
						file_get_contents(CORE_TPL_PATH.$this->sModalBoxTplFilename)
					);
	}
}