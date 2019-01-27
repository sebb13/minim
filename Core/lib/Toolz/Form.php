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
final class Toolz_Form {
	
	private static $sInputFileTplName	= 'input.file.bootstrap.tpl';
	private static $sInputFileTpl		= '';
	public static $sCheckedPattern		= ' checked="checked"';
	public static $sActivePattern		= 'active';
	public static $sSelectedPattern		= ' selected="selected"';
	public static $sReadonlyPattern		= ' readonly';
	public static $sPlaceholderPattern	= ' placeholder="{__placeholder__}"';

	public static function hasBeenChecked($sChoice, $sValue, array $aData) {
		if(isset($aData[$sChoice])) {
			return $sValue === $aData[$sChoice] ? self::$sCheckedPattern : '';
		}
		return '';
	}

	public static function hasBeenSelected($sChoice, $sValue, array $aData) {
		if(isset($aData[$sValue])) {
			return $sChoice === $aData[$sValue] ? self::$sSelectedPattern : '';
		}
		return '';
	}

	public static function optionsAnnee($sName='', $iAnnee=1900, $aData='', $sChoice='') {
		$sOpt = '';
		if($sChoice !== '') {
			$sOpt .= '<option value="">'.$sChoice.'</option>'."\n";
		}
		for($i = intval(date('Y')); $i >= $iAnnee; $i--) {
			$sOpt .= '<option value="'.$i.'"'.self::hasBeenSelected($sName, $i, $aData).'>'.$i.'</option>';
		}
		return $sOpt;
	}

	public static function getSelect($sName, $sId='', $sClass='') {
		return '<select name="'.$sName.'"'.Toolz_Tpl::getId($sId).Toolz_Tpl::getClass($sClass).'>{__OPTIONS__}</select>';
	}
	
	public static function optionsList($sChoice='', array $aData=array()) {
		$sOpt = '';
		foreach($aData as $sValue=>$sDisplay) {
			$sOpt .= '<option value="'.$sValue.'"'.self::hasBeenSelected($sChoice, $sValue, $aData).'>'.$sDisplay.'</option>'."\n";
		}
		return $sOpt;
	}

	public static function label($sDisplay, $sForID, $sClass='text-left') {
		return '<label for="'.$sForID.'"'.Toolz_Tpl::getClass($sClass).'>'.$sDisplay.'</label>';
	}

	public static function input($sType, $sName, $sId, $sValue=null, $sClass='', $bReadonly=false, $sPlaceholder='') {
		$sReadonly = $bReadonly ? self::$sReadonlyPattern : '';
		if(!empty($sPlaceholder)) {
			$sPlaceholder = str_replace('{__placeholder__}', $sPlaceholder, self::$sPlaceholderPattern);
		}
		return '<input type="'.$sType.'"'.Toolz_Tpl::getId($sId).' name="'.$sName.'" value="'.$sValue.'"'.Toolz_Tpl::getClass($sClass).' '.$sReadonly.$sPlaceholder.' />';
	}
	
	public static function file($sName, $sId, $sClass='') {
		if(empty(self::$sInputFileTpl)) {
			self::$sInputFileTpl = file_get_contents(CORE_TPL_PATH.self::$sInputFileTplName);
		}
		return str_replace(
						array(
							'{__NAME__}',
							'{__ID__}',
							'{__CLASS__}'
						), 
						array(
							$sName,
							Toolz_Tpl::getId($sId),
							Toolz_Tpl::getClass($sClass)
						), 
						self::$sInputFileTpl
					);
	}
	
	public static function checkbox($sName, $sId, $sValue = null, $sChoice='', array $aData=array(), $sClass='') {
		return '<input type="checkbox" name="'.$sName.'"'.Toolz_Tpl::getId($sId).' value="'.$sValue.'"'.Toolz_Tpl::getClass($sClass).' '.self::hasBeenChecked($sChoice, $sValue, $aData).' />';
	}
	
	public static function textarea($sName, $sId, $sValue = null, $sClass='', $sRows='', $sCols='') {
		return '<textarea '.Toolz_Tpl::getId($sId).'" name="'.$sName.'" rows="'.$sRows.'" cols="'.$sCols.'"'.Toolz_Tpl::getClass($sClass).'>'.$sValue.'</textarea>';
	}
	
	public static function genInputsHidden($sName, $sValue, $sId) {
		return $sReturn .= '<input type="hidden" name="'.$sName.'" value="'.$sValue.'"'.Toolz_Tpl::getId($sId).' />';
	}
}