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
final class Toolz_Checker {
	
	// -- Expressions régulières permettant la vérification des données utilisateur
	public static $sRegExpVille = '/^[a-z0-9\-\'\s]*[a-z]{2}[a-z0-9\-\'\s]*$/i';
	public static $sRegExpPrenom = '/^[a-z]{2}[a-z0-9\-\'\s]*$/i';
	public static $sRegExpNaN = '/^[a-z\"\-\'\s]+$/i';
	public static $sRegExpAlphaNum = '/^[\w\"\-\.,\'\s]+$/i';
	public static $sRegExpAdresse = '/^[\w\"\-\.,\'\s]*[a-z0-9]{2}[\w\"\-\.,\'\s]*$/i';
	public static $sRegExpInt = '/^\d+$/i';
	public static $sRegExpMail = '/^\w([-.]?\w)*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$/i';
	public static $sRegExpMoney = '/^(\d)+((,|\.){1}(\d){0,2})?$/i';
	public static $sRegExpPhone = '/^0[1-9][\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}$/i';
	public static $sRegExpPhoneFixe = '/^(01|02|03|04|05|08|09)[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}$/i';
	public static $sRegExpPhoneMobile = '/^(06|07)[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}[\.\-\s]{0,1}[0-9]{2}$/i';
	public static $sRegExpUrl = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
	
	// -- liste des noms de domaines d'adresses e-mail jetables
	public static $aBlackListDomain = array(
		'yopmail',
		'jetable',
		'mail-temporaire',
		'ephemail',
		'trashmail',
		'kasmail',
		'spamgourmet',
		'tempomail',
		'guerrillamail',
		'mytempemail',
		'saynotospams',
		'tempemail',
		'mailinator',
		'mytrashmail',
		'mailexpire',
		'maileater',
		'spambox',
		'guerrillamail',
		'10minutemail',
		'dontreg',
		'filzmail',
		'spamfree24',
		'brefmail',
		'0-mail',
		'link2mail',
		'DodgeIt',
		'dontreg',
		'e4ward',
		'gishpuppy',
		'guerrillamail',
		'haltospam',
		'kasmail',
		'mailexpire',
		'mailEater',
		'mailinator',
		'mailNull',
		'mytrashMail',
		'nobulk',
		'nospamfor',
		'PookMail',
		'shortmail',
		'sneakemail',
		'spam',
		'spambob',
		'spambox',
		'spamDay',
		'spamh0le',
		'spaml',
		'tempInbox',
		'temporaryinbox',
		'willhackforfood',
		'willSelfdestruct',
		'wuzupmail',
		'10minutemail',
	);
	
	public static function checkIp() {
		$sIp = gethostbyname($_SERVER['REMOTE_ADDR']);
		$rCh = curl_init();
		curl_setopt($rCh, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($rCh, CURLOPT_URL, 'http://proxy.mind-media.com/block/proxycheck.php?ip='.$sIp);
		$mResult = curl_exec($rCh);
		curl_close($rCh);
		return $mResult === 'N';
	}
	
	/**
	 * Vérification des clés requises dans un tableau
	 * @param Array $aParams
	 * @return Array
	 */
	public static function checkParams(array $aParams) {
		$bNullAllowed	= akead('nullAllowed',$aParams,true);
		$bWithException = akead('withException',$aParams,true);
		$aRequired = array(
			'required'	=> 'required',
			'data'		=> 'data'
		);
		$aCheck = array_diff_key($aRequired, $aParams);
		$aDb = debug_backtrace();
		array_shift($aDb);
		if (count($aCheck) > 0) {
			throw new InvalidArgumentException(
				'Missing fields ' . implode (', ', $aCheck) . ' ' . $aDb[0]['function']. ' '. $aDb[0]['file']. ' ('.$aDb[0]['line'] .')'
			);
		}
		$aErrors = array();
		$aData = $aParams['data'];
		if ($aData == null or !is_array($aData) or count($aData) == 0) {
			throw new InvalidArgumentException('$aData is not a valid array '. $aDb[0]['function']. ' '. $aDb[0]['file']. ' ('.$aDb[0]['line'] .')');
		}

		if(!$bNullAllowed){
			foreach ($aData as $mKey => $mValue) {
				$aData[$mKey] = trim($mValue);
				if ((is_null($aData[$mKey]) || $aData[$mKey] == '') && in_array($mKey, $aParams['required'])) {
					$aErrors[] = 'missing field '.$mKey . ' in ' . $aDb[0]['function']. ' '. $aDb[0]['file']. ' ('.$aDb[0]['line'] .')';
				}
			}
		}
		if (array_key_exists('default', $aParams)) {
			$aReturn = array_merge($aParams['default'], $aData);
		} else {
			$aReturn = $aData;
		}
		if (count($aErrors) && $bWithException){
			throw new CoreException(implode("\n",$aErrors));
		}
		return (count($aErrors) > 0) ? array('error' => $aErrors) : $aReturn;
	}
	
	public static function isValidMethod($sClassMethod) {
		list($sClassName, $sMethodName) = explode('::', $sClassMethod);
		return method_exists($sClassName , $sMethodName);
	}
	
	/**
	 * Vérification de l'intégrité d'un IBAN en fonction d'un pays
	 * @param String $sIban
	 * @param String $sCodePays : code ISO 3166-1-alpha-2 du pays
	 * @return Boolean
	 */
	public static function checkIban($sIban, $sCodePays = null){
		// -- formatage de l'IBAN
		$sIban = Toolz_Format::formatIban($sIban);
		// -- base de données contenant les règles pour vérifier les iban des pays
		$aParamsConnexion = array(
				'HOST' 		=> 'custom-db2.sdv.fr',
				'LOGIN' 	=> 'iban',
				'PWD' 		=> 'NogbecGac8;',
				'DB' 		=> 'iban',
				'DB_TYPE' 	=> 'mssql'
		);
		// -- connexion à la DB des iban avec un espace de nom pour ne pas interférer avec la connexion principale 
		$aDBIban = aDBFactory::getInstance ('mssql', $aParamsConnexion, 'DB_IBAN');
		// -- vérification de l'IBAN en utilisant une classe externe (InfosBancaires)
		$oCheckInfoBancaire = new InfosBancaires('iban');
		$oCheckInfoBancaire->aDB = $aDBIban;
		$bCheck = $oCheckInfoBancaire->verifierDonnees($sIban, $sCodePays);
		return $bCheck;
	}
	
	/**
	 * Vérification de l'intégrité d'un BIC en fonction d'un IBAN
	 * @param String $sBic
	 * @param String $sIban
	 * @param String $sCodePays : code ISO 3166-1-alpha-2 du pays
	 * @return Boolean
	 */
	public static function checkBic($sBic, $sIban, $sCodePays = null){
		// -- récupération du pays de l'IBAN
		$sPaysIban = substr($sIban, 0, 2);
		// -- si pas de pays spécifique on affecte celui de l'IBAN
		$sCodePays = empty($sCodePays) ? $sPaysIban : $sCodePays;
		// -- vérification du BIC
		$oCheckInfoBancaire = new InfosBancaires('bic');
		$bCheck = $oCheckInfoBancaire->verifierDonnees($sBic, $sCodePays);
		return $bCheck;
	}
	
	/**
	 * check si un chaine est composée uniquement des caractères alpha (voir RegExp)
	 * @param String $sStr
	 * @return Boolean
	 */
	public static function checkNaN($sStr) {
		$sStr = Toolz_Format::removeAccent($sStr);
		return self::checkRegExp($sStr, self::$sRegExpNaN);
	}

	/**
	 * 
	 * @param $sStr
	 * @return boolean
	 */
	public static function checkAlphaNum($sStr) {
		$sStr = Toolz_Format::removeAccent($sStr);
		return self::checkRegExp($sStr, self::$sRegExpAlphaNum);
	}

	public static function checkFirstName($sStr) {
		$sStr = Toolz_Format::removeAccent($sStr);
		return self::checkRegExp($sStr, self::$sRegExpPrenom);
	}
	
	public static function checkCity($sStr) {
		$sStr = Toolz_Format::removeAccent($sStr);
		return self::checkRegExp($sStr, self::$sRegExpVille);
	}

	public static function checkAddress($sStr) {
		$sStr = Toolz_Format::removeAccent($sStr);
		return self::checkRegExp($sStr, self::$sRegExpAdresse);
	}

	/**
	 * @param string $sStr
	 * @param int $iLength
	 * @return boolean
	 */
	public static function checkInt($sStr, $iLength = null) {
		$bCheck = self::checkRegExp($sStr, self::$sRegExpInt);
		if($bCheck === true && !empty($iLength)){
			$bCheck = strlen($sStr) === $iLength ? true : false;
		}
		return $bCheck;
	}

	/**
	 * @param string $sStr
	 * @return boolean
	 */
	public static function checkFloat($sStr) {
		if(preg_match('/^(\d)+((,|\.){1}(\d){0,2})?$/', $sStr) === 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * @param $sEmail
	 * @return boolean
	 */
	public static function checkMail($sStr) {
		$sStr = Toolz_Format::removeAccent($sStr);
		return self::checkRegExp($sStr, self::$sRegExpMail);
	}

	public static function checkMoney($sStr) {
		$sStr = Toolz_Format::removeAccent($sStr);
		return self::checkRegExp($sStr, self::$sRegExpMoney);
	}

	public static function checkPhone($sStr) {
		if(preg_match('/^0[0-9]0{8}$/', Toolz_Format::sanitizeInt($sStr)) === 1) {
			return false;
		}else {
			return self::checkRegExp($sStr, self::$sRegExpPhone);
		}
	}

	public static function checkPhoneFixe($sStr) {
		if(preg_match('/^0[0-9]0{8}$/', Toolz_Format::sanitizeInt($sStr)) === 1) {
			return false;
		}else {
			return self::checkRegExp($sStr, self::$sRegExpPhoneFixe);
		}
	}

	public static function checkPhoneMobile($sStr) {
		if(preg_match('/^0[0-9]0{8}$/', Toolz_Format::sanitizeInt($sStr)) === 1) {
			return false;
		}else {
			return self::checkRegExp($sStr, self::$sRegExpPhoneMobile);
		}
	}

	public static function checkUrl($sStr) {
		return self::checkRegExp($sStr, self::$sRegExpUrl);
	}
	
	/**
	 * Vérifie si un tableau est associatif ou non
	 * @param Array $mVar
	 * @param Boolean $bIndexNumericForced : permet de forcer si les index sont numériques
	 * @return Boolean
	 */
	public static function checkAssoc($aVar, $bIndexNumericForced = false) {
		if($bIndexNumericForced) {
			return true;
		}
		foreach($aVar as $sK=>$sV) {
			if(is_string($sK)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $multiarray
	 */
	public static function checkMultiArray($multiarray) {
		if(is_array($multiarray)) {
			foreach($multiarray as $array) {
				if(is_array($array)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * check une date au format fr jj/mm/aaaa
	 * @param $sDate
	 * @return boolean
	 */
	public static function checkDate($sDate) {
		$sDate = Toolz_Format::formatDateFr2Eng($sDate);
		return self::checkDateFormated($sDate);
	}

	/**
	 * check une date conso au format fr jj/mm/aaaa
	 * la date ne peut pas être dans le futur !
	 * @param $sDate
	 * @return boolean
	 */
	public static function checkDateConso($sDate) {
		$sDate = Toolz_Format::formatDateFr2Eng($sDate);
		if(self::checkDateFormated($sDate) && $sDate <= intval(date('Ymd', time()))) {
			return true;
		}
		return false;
	}

	/**
	 * check une date de naissance
	 * @param $sDate
	 * @param $sLimite
	 * @return boolean
	 */
	public static function checkBirthDate($sDate, $sLimite = 1900) {
		$sDate = Toolz_Format::formatDateFr2Eng($sDate);
		if(strlen($sDate) !== 8 || false == preg_match('`^([0-9]{8})$`', $sDate)) {
			return false;
		}
		// -- si l'année est inférieure à l'année limite ou supérieur à la date actuelle, return false
		$iYear = intval(substr($sDate, 0, 4));
		if($iYear < $sLimite || $sDate >= intval(date('Ymd', time()))) {
			return false;
		}
		return checkdate(substr($sDate, 4, 2), substr($sDate, 6, 2), substr($sDate, 0, 4));
	}

	/**
	 * check une date au format anglais aaaammjj
	 * @param $sDate
	 * @return Boolean
	 */
	public static function checkDateFormated($sDate) {
		if(strlen($sDate) !== 8 || false == preg_match('`^([0-9]{8})$`', $sDate)) {
			return false;
		}
		return checkdate(substr($sDate, 4, 2), substr($sDate, 6, 2), substr($sDate, 0, 4));
	}

	/**
	 * Check un RIB
	 * @param $sRib
	 * @return Boolean
	 */
	public static function checkRib($sRib) {
		// Variables locales
		$bCorrect = false;
		$sCleRib = substr($sRib, 21, 2);
		$sNumeroCompte = substr($sRib, 10, 11);
		$sCodeGuichet = substr($sRib, 5, 5);
		$sCodeBanque = substr($sRib, 0, 5);
		// La clé RIB est-elle syntaxiquement juste ?
		if(self::_verifierCleRib($sCleRib)) {
			// La clé RIB correspond-elle avec les informations bancaires ?
			if($sCleRib === self::_calculerCleRib($sCodeBanque, $sCodeGuichet, $sNumeroCompte)) {
				$bCorrect = true;
			}
		}
		// Valeur de retour
		return $bCorrect;
	}

	protected static function _calculerCleRib($sCodeBanque, $sCodeGuichet, $sNumeroCompte) {
		// Variables locales
		$iCleRib = 0;
		$sCleRib = '';
		// Calcul de la clé RIB à  partir des informations bancaires
		$sNumeroCompte = strtr(strtoupper($sNumeroCompte), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '12345678912345678923456789');
		$iCleRib = 97 - (int)fmod(89 * $sCodeBanque + 15 * $sCodeGuichet + 3 * $sNumeroCompte, 97);
		// Valeur de retour
		if($iCleRib < 10) {
			$sCleRib = '0' . (string)$iCleRib;
		} else {
			$sCleRib = (string)$iCleRib;
		}
		return $sCleRib;
	}

	protected static function _verifierCleRib($sCleRib) {
		return preg_match('/^(0[1-9]|[1-8]\d|9[0-7])$/', $sCleRib);
	}

	/**
	 * Check une expression régulière
	 * @param string $sRegExp
	 * @param string $sStr
	 * @return Boolean
	 */
	public static function checkRegExp($sStr, $sRegExp) {
		return (bool)preg_match($sRegExp, $sStr);
	}

	/**
	 * Check un code postal suivant un code pays
	 * @param $sCode
	 * @param $sPays : code ISO 3166-1-alpha-2 du pays
	 * @return Boolean
	 */
	public static function checkCodePostal($sCode, $sPays = 'FR') {
		if(!empty($sPays)) {
			// -- C = Chiffre, L = Lettre
			switch($sPays) {
				/**
				* Format CCCC
				* Autriche, Australie, Belgique, Suisse, Danemark, Hongrie, Liechtenstein, Luxembourg, Moldavie, Macédoine, Norvège, Philippines, Tunisie, Afrique du Sud
				*/
				case 'AT':
				case 'AU':
				case 'BE':
				case 'CH':
				case 'DK':
				case 'HU':
				case 'LI':
				case 'LU':
				case 'MD':
				case 'MK':
				case 'NO':
				case 'PH':
				case 'TN':
				case 'ZA':
					return self::checkInt($sCode, 4);
					break;
				/**
				* Format CCCC-CCC
				* Portugal
				*/
				case 'PT' :
					return self::checkRegExp($sCode, '/^(\d){4}-(\d){3}$/');
					break;
				/**
				* Format CCCC LL (Le premier chiffre ne peut être égal à 0)
				* Pays-bas
				*/
				case 'NL' :
					return self::checkRegExp($sCode, '/^[1-9](\d){3}\s([a-z]){2}$/i');
					break;
				/**
				* Format CCCCC
				* Bosnie Herzégovine, Allemagne, Algérie, Espagne, Finlande, France, Guatemala, Croatie, Italie, Sri Lanka, Lituanie, Maroc, Mexique, Malaisie, Ukraine
				*/
				case 'BA':
				case 'DZ':
				case 'ES':
				case 'FI':
				case 'FR':
				case 'GT':
				case 'HR':
				case 'IT':
				case 'LK':
				case 'LT':
				case 'MA':
				case 'MX':
				case 'MY':
				case 'UA':
					return self::checkInt($sCode, 5);
					break;
				/**
				* Format CCCCCC
				* Biélorussie, Chine, Colombie, Equateur, Inde, Kirghizistan, Kazakhstan, Roumanie, Singapour, Ouzbékistan
				*/
				case 'BY':
				case 'CN':
				case 'CO':
				case 'EC':
				case 'IN':
				case 'KG':
				case 'KZ':
				case 'RO':
				case 'SG':
				case 'UZ':
					return self::checkInt($sCode, 5);
					break;
				/**
				* Format CC-CCC
				* Pologne
				*/
				case 'PL':
					return self::checkRegExp($sCode, '/^(\d){2}-(\d){3}$/');
					break;
				/**
				* Format LCCCCLLL ou CCCC
				* Argentine
				*/
				case 'AR':
					return self::checkRegExp($sCode, '/^([a-z]\d{4}[a-z]{3})|(\d{4})$/i');
					break;
				/**
				* Format CCCCC-CCC
				* Brésil, Cambodge
				*/
				case 'BR':
				case 'KH':
					return self::checkRegExp($sCode, '/^\d{5}-\d{3}$/');
					break;
				/**
				* Format LCL CLC
				* Canada
				*/
				case 'CA':
					return self::checkRegExp($sCode, '/^[a-z]\d[a-z]\s\d[a-z]\d$/i');
					break;
				/**
				* Format CCC (Le 0 ne peut être utilisé en première position)
				* Taïwan
				*/
				case 'TW':
					return self::checkRegExp($sCode, '/^[1-9]\d{2}$/');
					break;
				/**
				* Format CCC
				* Madagascar
				*/
				case 'MG':
					return self::checkRegExp($sCode, '/^\d{3}$/');
					break;
				/**
				* Format CCC-CCCC
				* Japon
				*/
				case 'JP':
					return self::checkRegExp($sCode, '/^\d{3}-\d{4}$/');
					break;
				/**
				* Format CCC CC
				* République tchèque, Slovaquie
				*/
				case 'CZ':
				case 'SK':
					return self::checkRegExp($sCode, '/^\d{3}\s\d{2}$/');
					break;
				/**
				* Format CCCCC ou CCCCCC
				* Serbie
				*/
				case 'RS':
					return self::checkRegExp($sCode, '/^\d{5,6}$/');
					break;
				/**
				* Format CCCC ou CCCCC (Le code postal compte 5 chiffres depuis 1993. Avant il n'y en avait que 4.)
				* Allemagne
				*/
				case 'DE':
					return self::checkRegExp($sCode, '/^\d{4,5}$/');
					break;
				// -- Pays inconnu => on renvoie vrai
				default :
					return true;
					break;
			}
		}
		return true;
	}

	/**
	 * Check le captcha en POST avec celui en SESSION
	 * @return Boolean
	 */
	public static function checkCaptcha($sStr, $captcha){
		if (empty( $sStr ) || !isset ( $_SESSION [$captcha] ) || strcasecmp ( $sStr, $_SESSION [$captcha] ) != 0) {
			$_SESSION [$captcha] = null;
			return false;
		}
		return true;
	}

	/**
	 * public static function checkMinLength
	 * checks if the input length matches the minimum required length
	 *
	 * @param string $mString : the input
	 * @param string $sName : user defined name for the data
	 * @param int $iMinLength : minimum length
	 * @return boolean true if the input length is greater or equal to the minimum length, false if not
	 */
	public static function checkMinLength($mString, $iMinLength) {
		if(self::checkInt($iMinLength) === true && strlen($mString) >= (int)$iMinLength) {
			return true;
		}
		return false;
	}

	/**
	 * public static function checkMaxLength
	 * checks if the input length matches the maximum required length
	 *
	 * @param string $mString : the input
	 * @param int $iMaxLength : maximum length
	 * @return boolean true if the input length is lesser or equal to the maximum length, false if not
	 */
	public static function checkMaxLength($mString, $iMaxLength) {
		if(self::checkInt($iMaxLength) === true && strlen($mString) <= (int)$iMaxLength) {
			return true;
		}
		return false;
	}

	/**
	 * public static function checkStrictLength
	 * checks if the input has the mandatory strict length
	 *
	 * @param string $mString : the input
	 * @param int $iStrictLength : strict length
	 * @return boolean true if the input length is equal to the mandaotry strict length, false if not
	 */
	public static function checkStrictLength($mString, $iStrictLength) {
		if(self::checkInt($iStrictLength) === true && strlen($mString) === (int)$iStrictLength) {
			return true;
		}
		return false;
	}

	/**
	 * public static function checkStartingWith
	 * checks if the input starts with each values in a given array
	 * @param string $mString : the input
	 * @param array $aStarts : array of values
	 * @return boolean true if the input starts with at least one of the values, false if not.
	 */
	public static function checkStartingWith($mString, $aStarts) {
		$bRes = false;
		if(!is_array($aStarts)) {
			throw new Toolz_Checker_Exception('Filter must be an array');
		}
		foreach($aStarts as $mVal) {
			if(substr($mString, 0, strlen($mVal)) === $mVal) {
				$bRes = true;
			}
		}
		return $bRes;
	}

	/**
	 * public static function checkNotStartingWith
	 * checks if the input does not start with each values in a given array
	 *
	 * @param string $mString : the input
	 * @param array $aStarts : array of values
	 * @return boolean false if the input starts with at least one of the values, true if not.
	 */
	public static function checkNotStartingWith($mString, $aStarts) {
		$bRes = true;
		if(!is_array($aStarts)) {
			throw new Toolz_Checker_Exception('Filter must be an array');
		}
		foreach($aStarts as $mVal) {
			if(substr($mString, 0, strlen($mVal)) === $mVal) {
				$bRes = false;
				break;
			}
		}
		return $bRes;
	}

	/**
	 * public static function checkForbidden
	 * checks if the input is equal to one of the forbidden values
	 *
	 * @param string $mString : the input
	 * @param string or array $mForbidden : string or array of forbidden values
	 * @return boolean false if the input is equal to one of the forbidden values, true if not
	 */
	public static function checkForbidden($mString, $mForbidden) {
		if(is_array($mForbidden)) {
			if(in_array($mString, $mForbidden)) {
				return false;
			}
		} else {
			if($mForbidden === $mString) {
				return false;
			}
		}
		return true;
	}

	/**
	 * public static function checkAllowed
	 * checks if the input is equal to one of the allowed (and mandatory) values
	 *
	 * @param string $mString : the input
	 * @param string or array $mForbidden : string or array of forbidden values
	 * @return boolean false if the input is equal to one of the forbidden values, true if not
	 */
	public static function checkAllowed($mString, $mAllowed) {
		if(is_array($mAllowed)) {
			if(!in_array($mString, $mAllowed)) {
				return false;
			}
		} else {
			if($mAllowed !== $mString) {
				return false;
			}
		}
		return true;
	}

	/**
	 * public function checkMinRange
	 * checks if the numeric input is greater or equal than the minimum range
	 *
	 * @param string $mString : the input
	 * @param int $iMinRange : minimum range
	 * @return boolean if the input is greater or equal than the minimum range, false if not
	 */
	public static function checkMinRange($mString, $iMinRange) {
		if(true === self::checkInt($iMinRange) && $mString >= $iMinRange) {
			return true;
		}
		return false;
	}

	/**
	 * public function checkMaxRange
	 * checks if the numeric input is lesser or equal than the maximum range
	 * @param string $mString : the input
	 * @param int $iMaxRange : maximum range
	 * @return boolean if the input is lesser or equal than the maximum range, false if not
	 */
	public static function checkMaxRange($mString, $iMaxRange) {
		if(true === self::checkInt($iMaxRange) && $mString <= $iMaxRange) {
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * Check si l'adresse e-mail n'est pas une adresse e-mail jetable
	 * @param string $sStr : L'adresse e-mail à tester
	 * @return boolean : retourne false si l'adresse est jetable sinon retourne true
	 */
	public static function checkNotDisposableEmail($sStr){
		foreach (self::$aBlackListDomain as $sDomain){
			if(self::checkRegExp($sStr, '/@' . $sDomain . '\./i')){
				return false;
			}
		}
		return true;
	}
}