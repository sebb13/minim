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
final class Toolz_Format {

	protected static $sAlphaUpperCase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	protected static $sAlphaLowerCase = 'abcdefghijklmnopqrstuvwxyz';
	protected static $sAccentsUpperCase = 'ÀÁÂÃÄÅÒÓÔÕÖØÈÉÊËÇÌÍÎÏÙÚÛÜÑ';
	protected static $sAccentsLowerCase = 'àáâãäåòóôõöøèéêëçìíîïùúûüñ';

	/**
	 * Default encoding
	 * @var string
	 */
	const ENCODING = 'UTF-8';

	/**
	 * Extension mbstring enabled ??? 
	 * @var boolean
	 */
	const MBSTRING = true;
	
	/*
	 * Injections XSS
	 * 
	 */
	public static function XssKiller(&$aValue) {
		foreach ($aValue as $sKey => $mValue) {
			if (is_array($mValue)) {
				self::XssKiller($mValue);
			} else {
				$aValue[$sKey] = htmlspecialchars(
										str_replace('"', '', strip_tags(trim($mValue))),
										NULL,
										'UTF-8'
									);
			}
		}
	}
	
	public static function cutWithEndDots($sString, int $iMaxSize) {
		if(strlen($sString) > $iMaxSize) {
			return substr($sString, 0, $iMaxSize-2).'...';
		} else {
			return $sString;
		}
	}
	
	public static function formatFromSerializeArray(array $aForm) {
		$aFormOutput = array();
		foreach($aForm as $aFormInfos) {
			$aFormOutput[$aFormInfos['name']] = $aFormInfos['value'];
		}
		return $aFormOutput;
	}
	
	public static function formatFromJQuerySerialize($sForm) {
		$aTmp = explode('&',htmlspecialchars_decode(urldecode($sForm)));
		$aOutput = array();
		foreach($aTmp as $sInput) {
			$aInput = explode('=', $sInput);
			if(count($aInput) === 2) {
				$aOutput[$aInput[0]] = $aInput[1];
			}
		}
		return $aOutput;
	}
	
	public static function formatTanslateNodeName($sNodeName) {
		return strtoupper(preg_replace("#[^a-zA-Z0-9_]#", "_", $sNodeName));
	}

	/**
	 * public static function sanitizeStr
	 * sanitize a string, given an array of options :
	 * addslashes => adds slashes...(default)
	 * htmlentities => encode html entities (default)
	 * urlencode => encode as en url
	 * trim => apply a trim
	 * mssqlEscape => escape quotes for mssql
	 *
	 * @param string $mString : the input
	 * @param array $aOptions : array of options
	 * @return string
	 */

	public static function sanitizeStr($mString, $aOptions = array ('addslashes', 'htmlentities')) {
		if(in_array('trim', $aOptions)) {
			$mString = trim($mString);
		}
		if(in_array('addslashes', $aOptions)) {
			$mString = addslashes($mString);
		}
		if(in_array('mssqlEscape', $aOptions)) {
			$mString = str_replace("'", "''", $mString);
		}
		if(in_array('htmlentities', $aOptions)) {
			$mString = htmlentities($mString);
		}
		if(in_array('urlencode', $aOptions)) {
			$mString = urlencode($mString);
		}
		return $mString;
	}

	/**
	 * public static function sanitizeInt
	 * sanitize a variable (input, so should be a string) as an integer, given an array of options :
	 * onlyInt => extract onnly the integer values in the variable
	 *
	 * @param string $mString : the input
	 * @param array $aOptions : array of options :
	 * onlyInt => retrieve only integer characters
	 * @return string
	 */
	public static function sanitizeInt($mString, $aOptions = array ('onlyInt')) {
		if(in_array('onlyInt', $aOptions)) {
			preg_match_all('@([\d]+)+@', $mString, $aRes);
			if(!empty($aRes[0])) {
				$mString = implode($aRes[0]);
			} else {
				$mString = '';
			}
		}
		return $mString;
	}

	/**
	 * suprimme tous les accents de la chaine
	 *	@param $sStr (string)
	 *	@return string
	 */
	public static function removeAccent($sStr) {
		return str_replace(
					array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','Ò','Ó','Ô','Õ','Ö','Ø','ò','ó','ô','õ','ö','ø','È','É','Ê','Ë','è','é','ê','ë','Ç','ç','Ì','Í','Î','Ï','ì','í','î','ï','Ù','Ú','Û','Ü','ù','ú','û','ü','ÿ','Ñ','ñ'),
					array('A','A','A','A','A','A','a','a','a','a','a','a','O','O','O','O','O','O','o','o','o','o','o','o','E','E','E','E','e','e','e','e','C','c','I','I','I','I','i','i','i','i','U','U','U','U','u','u','u','u','y','N','n'),
					$sStr
				);
	}

	/**
	 * ucfirst avec prise en compte des accents + met tout le reste en lowercase
	 * @param $sStr
	 * @return String
	 */
	public static function ucfirst($sStr) {
		$sStr = self::strtolower($sStr);
		$sFirst = self::strtoupper(mb_substr($sStr, 0, 1, self::ENCODING));
		$sEnd = mb_substr($sStr, 1, mb_strlen($sStr), self::ENCODING);
		$sStr = $sFirst.$sEnd;
		return $sStr;
	}

	/**
	 * ucwords avec prise en compte des accents + met tout le reste en lowercase
	 * @param $sStr
	 * @return String
	 */
	public static function ucwords($sStr) {
		if(self::MBSTRING){
			return  mb_convert_case($sStr, MB_CASE_TITLE, self::ENCODING);
		}else{
			$aWords = split(' ', $sStr);
			$aWords = array_map(array('self', 'ucfirst'), $aWords);
			return join(' ', $aWords);
		}
	}

	/**
	 * strtoupper avec prise en compte des accents
	 * @param $sStr
	 * @return String
	 */
	public static function strtoupper($sStr) {
		if(self::MBSTRING){
			return mb_strtoupper($sStr,self::ENCODING);
		}else{
			return strtr(strtoupper($sStr), self::$sAccentsLowerCase, self::$sAccentsUpperCase);
		}
	}

	/**
	 * strtolower avec prise en compte des accents
	 * @param $sStr
	 * @return String
	 */
	public static function strtolower($sStr) {
		if(self::MBSTRING){
			return mb_strtolower($sStr,self::ENCODING);
		}else{
			$sStr = strtr($sStr, self::$sAccentsUpperCase, self::$sAccentsLowerCase);
			return strtr($sStr, self::$sAlphaUpperCase, self::$sAlphaLowerCase);
		}
	}

	public static function formatMoney($sMoney) {
		return str_replace(',', '.', $sMoney);
	}

	/**
	 * Renvoi une date au format jj/mm/aaaa
	 * @param $iDay
	 * @param $iMonth
	 * @param $iYear
	 * @return String
	 */
	public static function formatDateFr($iDay, $iMonth, $iYear) {
		if(strlen($iDay) === 1) {
			$iDay = '0' . $iDay;
		}
		if(strlen($iMonth) === 1) {
			$iMonth = '0' . $iMonth;
		}
		return $iDay . '/' . $iMonth . '/' . $iYear;
	}

	/**
	 * Transforme une date jj/mm/aaaa en aaaammjj
	 * @param $sDate
	 * @return String
	 */
	public static function formatDateFr2Eng($sDate) {
		if(strlen($sDate) !== 10) {
			return false;
		}
		$aDate = explode('/', $sDate);
		if(count($aDate) !== 3) {
			return false;
		}
		return $aDate[2] . $aDate[1] . $aDate[0];
	}

	/**
	 * formate une date aaaammmjj en jj/mm/aaaa
	 * @param $sDate
	 * @return String
	 */
	public static function formatDateEng2Fr($sDate) {
		if(strlen($sDate) !== 8) {
			return false;
		}
		return substr($sDate, 6, 2) . '/' . substr($sDate, 4, 2) . '/' . substr($sDate, 0, 4);
	}

	/**
	 * clean un numéro de téléphone 
	 * @param $sPhone
	 * @return string
	 */
	public static function formatPhone($sPhone) {
		return str_replace(array('.', ',', ' ', '-'), '', $sPhone);
	}

	/**
	 * Transforme les br en \n
	 * @param $sStr
	 * @return string
	 */
	public static function br2nl($sStr) {
		return str_replace(array('<br />', '<br/>', '<br>'), "\n", $sStr);
	}

	/**
	 * 
	 * @param $sChaine
	 * @param $iLen
	 * @param $sEndGlue
	 * @return String
	 */
	public static function subWords($sChaine, $iLen, $sEndGlue = '') {
		if($iLen >= strlen($sChaine)) {
			return $sChaine;
		}
		$aSplit = explode(' ', $sChaine);
		$aReturn = array();
		$iUpToNow = 0;
		foreach($aSplit as $sWord) {
			if(self::MBSTRING){
				$iUpToNow += mb_strlen($sWord, self::ENCODING);
			}else{
				$iUpToNow += strlen($sWord);
			}
			if($iUpToNow < $iLen) {
				$aReturn[] = $sWord; 
			} else {
				array_pop($aReturn);
				return implode(' ', $aReturn).$sEndGlue;
			}
		}
		return $sChaine;
	}

	public static function formatMoneyFr($iMoney){
		$iMoney = number_format($iMoney, 2, ',', ' ');
		return $iMoney;
	}

	public static function sanitizeQuery($sQuery) {
		return str_replace("\n", ' ', $sQuery);
	}
}