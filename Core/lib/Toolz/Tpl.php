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
final class Toolz_Tpl {
	
	private static $sToolTipTagPatern = '{__{__tag_name__}_TOOLTIP__}';
	
	public static function getClass($sClassName='') {
		return !empty($sClassName) ? ' class="'.$sClassName.'"' : '';
	}
	
	public static function getId($sId='') {
		return !empty($sId) ? ' id="'.$sId.'"' : '';
	}
	
	public static function getUl($sContent='', $sClass='', $sId='') {
		return self::getBasicElmt('ul', $sContent, $sClass, $sId);
	}
	
	public static function getLi($sContent='', $sClass='', $sId='') {
		return self::getBasicElmt('li', $sContent, $sClass, $sId);
	}
	
	public static function getDiv($sContent='', $sClass='', $sId='') {
		return self::getBasicElmt('div', $sContent, $sClass, $sId);
	}
	
	public static function getSpan($sContent='', $sClass='', $sId='') {
		return self::getBasicElmt('span', $sContent, $sClass, $sId);
	}
	
	public static function getHx($sLevel, $sContent='', $sClass='', $sId='') {
		return self::getBasicElmt('h'.$sLevel, $sContent, $sClass, $sId);
	}
	
	public static function getP($sContent='', $sClass='', $sId='') {
		return self::getBasicElmt('p', $sContent, $sClass, $sId);
	}
	
	public static function getA($sUrl, $sContent, $sClass='', $sId='', $bTargetBlank=false) {
		$sTarget = $bTargetBlank ? 'target="_blank" ' : '';
		return '<a href="'.$sUrl.'" '.$sTarget.self::getClass($sClass).self::getId($sId).'>'.$sContent.'</a>';
	}
	
	private static function getBasicElmt($sElmt, $sContent='', $sClass='', $sId='') {
		return '<'.$sElmt.self::getClass($sClass).self::getId($sId).'>'.$sContent.'</'.$sElmt.'>';
	}
	
	public static function replacePlaceholdersFromArray(&$aReplace, $sTpl, $sPlaceholderTpl = '{__KEY__}'){
		if(!empty($sPlaceholderTpl)){
			$aFinalReplace = array();
			foreach ($aReplace as $sKey => $sVal){
				$sNewKey = str_replace('KEY', $sKey, $sPlaceholderTpl);
				$aFinalReplace[$sNewKey] = $sVal;
			}
			return str_replace(array_keys($aFinalReplace), array_values($aFinalReplace), $sTpl);
		}
		return '';
	}
	
	public static function getToolTip($sContents) {
		return ' <a href="#" data-toggle="tooltip" data-placement="right" title="'.$sContents.'" class="text-right" style="display:bloc;width:100%;">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>';
	}
	
	public static function getToolTipTag($sTagName) {
		return str_replace('{__tag_name__}', strtoupper($sTagName), self::$sToolTipTagPatern);
	}
	
	public static function getPaging($iNbPages, $iActivePage, $sQueryString, $bArrow=true) {
		if((int)$iNbPages === 1) {
			return '';
		}
		if((int)$iActivePage > 1 && $bArrow) {
			$iPreviousPage = (int)$iActivePage-1;
			$sPaging = self::getA($sQueryString.$iPreviousPage, '< {__PREVIOUS__}');
		} else {
			$sPaging = '';
		}
		for($iPage=1 ; $iPage<=$iNbPages ; $iPage++) {
			$sPaging .= self::getA(
								$sQueryString.$iPage, 
								$iPage, 
								(int)$iActivePage === $iPage ? 'active' : ''
							);
		}
		if((int)$iActivePage < $iNbPages && $bArrow) {
			$iNextPage = (int)$iActivePage+1;
			$sPaging .= self::getA($sQueryString.$iNextPage, '{__NEXT__} >');
		}
		return self::getDiv($sPaging, 'paging');
	}
}