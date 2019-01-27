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
// -- UNFOMIZE DATES
if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Europe/Paris');
}
// -- SET ENCODING UTF-8 FOR MB_STRING
if(extension_loaded('mbstring')){
    mb_internal_encoding('UTF-8');
}
// -- Set Environment
$sRoot = $_SERVER['DOCUMENT_ROOT'];
$sWebPath = 'https://'.$_SERVER['HTTP_HOST'].'/';
if (strpos($_SERVER['SCRIPT_FILENAME'], '/dev.') !== false) {
    // -- in dev
    define('DEV', true);
    define('TEST', false);
    error_reporting(E_ALL);
} elseif (strpos($_SERVER['SCRIPT_FILENAME'], '/test.') !== false) {
    // -- in test
    define('DEV', true);
    define('TEST', true);
    error_reporting(E_ALL);
} else {
    // -- in prod
    define('DEV', false);
    define('TEST', false);
    error_reporting(0);
}
$aWebPath = explode('.', $sWebPath);
/* global paths */
define('ADMIN', strpos($aWebPath[0], 'admin') !== false);
define('WEB_PATH',$sWebPath);
define('ROOT_PATH', $sRoot.'/');
define('REPOSITORY_PATH', ROOT_PATH.'repository/');
define('REPOSITORY_URL', WEB_PATH.'repository/');
define('CORE_PATH', ROOT_PATH.'Core/');
unset($sWebPath, $sRoot);
/* minim paths */
define('IMG_URL',				WEB_PATH.'img/');
define('CSS_URL',				WEB_PATH.'css/');
define('JS_URL',				WEB_PATH.'js/');
define('SWF_URL',				WEB_PATH.'swf');
define('DOWNLOAD_URL',			WEB_PATH.'download/');
define('DOWNLOAD_PATH',			ROOT_PATH.'download/');
define('LOG_PATH',				CORE_PATH.'logs/');
define('DATA_PATH',				CORE_PATH.'data/');
define('DEV_BANNER_PATH',		CORE_PATH.'devBanner/');
define('LIB_PATH',				CORE_PATH.'lib/');
define('CORE_INTERFACE_PATH',	LIB_PATH.'interfaces/');
define('CORE_CLASS_PATH',		LIB_PATH.'Core/');
define('EXTERNAL_CLASS_PATH',	LIB_PATH.'External/');
define('TOOLZ_CLASS_PATH',		LIB_PATH.'Toolz/');
define('USR_CLASS_PATH',		LIB_PATH.'usr/');
define('INC_PATH',				CORE_PATH.'inc/');
define('SESSION_PATH',			CORE_PATH.'_sessions');
define('LOC_PATH',				CORE_PATH.'locales/');
define('COMMON_LOC_PATH',		LOC_PATH.'common/');
define('TPL_PATH',				CORE_PATH.'tpl/');
define('CORE_TPL_PATH',			TPL_PATH.'core/');
define('INC_TPL_PATH',			TPL_PATH.'inc/');
define('CONTENT_TPL_PATH',		TPL_PATH.'contents/');
define('VIEW_TPL_PATH',			TPL_PATH.'view/');
define('SOC_NET_TPL_PATH',		TPL_PATH.'socialNetwork/');
define('CACHE_PATH',			CORE_PATH.'cache/');
define('CORE_CACHE_PATH',		CACHE_PATH.'core/');
define('CSS_PATH',				ROOT_PATH.'css/');
define('JS_PATH',				ROOT_PATH.'js/');
define('IMG_PATH',				ROOT_PATH.'img/');
define('MDEIAS_PATH',			ROOT_PATH.'medias/');
define('BAN_LOG',				LOG_PATH.'ban/');
define('UPDATES_PATH',			CORE_PATH.'_updates/');
/* Modules paths */
define('MODULES_PATH',			CORE_PATH.'modules/');
define('MODULES_CLASS_PATH',	LIB_PATH.'Modules/');
/* Modules generic paths */
define('GEN_LIB_PATH',					'lib/');
define('GEN_ADMIN_TPL_CONTENTS_PATH',	'tpl/admin/contents/');
define('GEN_ADMIN_TPL_PARTS_PATH',		'tpl/admin/parts/');
define('GEN_TPL_CONTENTS_PATH',			'tpl/contents/');
define('GEN_TPL_PARTS_PATH',			'tpl/parts/');
define('GEN_LOC_PATH',					'locales/');
define('GEN_ADMIN_LOC_PATH',			GEN_LOC_PATH.'admin/');
define('GEN_COMMON_LOC_PATH',			GEN_LOC_PATH.'common/');
define('GEN_DATA_PATH',					'data/');
define('GEN_CACHE_PATH',				'cache/');
define('GEN_JS_PATH',					'js/');
define('GEN_CSS_PATH',					'css/');
/* Drafts paths */
define('DRAFTS_PATH',				CORE_PATH.'drafts/');
define('DRAFTS_TPL_PATH',			DRAFTS_PATH.'tpl/contents/');
define('DRAFTS_LOC_PATH',			DRAFTS_PATH.'locales/');
define('DRAFTS_CACHE_PATH',			DRAFTS_PATH.'cache/');
define('DRAFTS_ADMIN_TPL_PATH',		DRAFTS_PATH.'tpl/admin/contents/');
define('DRAFTS_ADMIN_CACHE_PATH',	DRAFTS_PATH.'cache/admin/');
define('DRAFTS_ADMIN_LOC_PATH',		DRAFTS_LOC_PATH.'admin/');
define('DRAFTS_COMMON_LOC_PATH',	DRAFTS_LOC_PATH.'common/');
/* Versions paths */
define('BACKUP_PATH',				CORE_PATH.'_backups/');
define('BACKUP_TPL_PATH',			BACKUP_PATH.'tpl/contents/');
define('BACKUP_LOC_PATH',			BACKUP_PATH.'locales/');
define('BACKUP_CACHE_PATH',			BACKUP_PATH.'cache/');
define('BACKUP_ADMIN_LOC_PATH',		BACKUP_LOC_PATH.'admin/');
define('BACKUP_COMMON_LOC_PATH',	BACKUP_LOC_PATH.'common/');
/* backoffice defines */
define('ADMIN_LOC_PATH',			LOC_PATH.'admin/');
define('CORE_TRANSLATIONS_PATH',	ADMIN_LOC_PATH.'{__LANG__}/core.xml');
define('ADMIN_TPL_PATH',			TPL_PATH.'admin/');
define('ADMIN_CONTENT_TPL_PATH',	TPL_PATH.'admin/content/');
define('CORE_RESULT_TPL_PATH',		ADMIN_CONTENT_TPL_PATH.'coreResult.tpl');
define('ADMIN_PARTS_TPL_PATH',		TPL_PATH.'admin/parts/');
define('ADMIN_CACHE_PATH',			CACHE_PATH.'admin/');
define('ADMIN_IMG_URL',				IMG_URL.'admin/');
define('ADMIN_IMG_PATH',			IMG_PATH.'admin/');
define('ADMIN_VIEW_TPL_PATH',		ADMIN_TPL_PATH.'view/');

require INC_PATH.'inc.coreFunctions.php';
require CORE_CLASS_PATH.'class.GenericException.php';
require CORE_CLASS_PATH.'package.exceptions.errors.php';
require CORE_CLASS_PATH.'package.Session.php';
//Classe dynamique de configuration des routes, hors de portée de l'autoloader
//Utilisée pour chaque requêtes, donc chargée directement ici.
require DATA_PATH.'class.RoutesConf.php';
require DATA_PATH.'class.PagesConf.php';
// Configuration globale
require CORE_CLASS_PATH.'class.UserRequest.php';
UserRequest::startBenchmark();
require CORE_CLASS_PATH.'class.SimpleXmlMgr.php';
require CORE_CLASS_PATH.'class.Config.php';
$oConfig = new Config('minim');
$aConf = $oConfig->getGlobalConf();
unset($oConfig);
if(isset($aConf['MAINTENANCE']) && $aConf['MAINTENANCE'] === 'YES') {
    SessionCore::$bMaintenance = true;
}
// Data Base
if (($aConf['SYS_DB_NEED'] === 'YES') ||
($aConf['SYS_DB_NEED'] === 'BACK' && ADMIN) ||
($aConf['SYS_DB_NEED'] === 'FRONT' && !ADMIN)) {
	require CORE_CLASS_PATH.'class.SPDO.php';
	$sDbName = DEV ? $aConf['SYS_DB_DEV_DATABASE'] : $aConf['SYS_DB_PROD_DATABASE'];
	$sConnect = 'mysql:dbname='.$sDbName.';host='.$aConf['SYS_DB_HOST'];
	try {
		$oPdo = SPDO::getInstance($sConnect, $aConf['SYS_DB_USER'], $aConf['SYS_DB_PWD']);
	} catch (PDOException $e) {
		//throw new CoreException('unable to connect to DB');
	}
}
define('DOMAIN_NAME',			$aConf['DOMAIN_NAME']);
define('EMAIL_CONTACT',			$aConf['EMAIL_CONTACT']);
define('SITE_URL_PROD',			$aConf['SITE_URL_PROD']);
define('SITE_URL_DEV',			$aConf['SITE_URL_DEV']);
define('ADMIN_URL_PROD',		$aConf['ADMIN_URL_PROD']);
define('ADMIN_URL_DEV',			$aConf['ADMIN_URL_DEV']);
define('STATIC_SERVER_URL',		$aConf['STATIC_SERVER_URL']);
define('STATIC_DEV_SERVER_URL',	$aConf['STATIC_DEV_SERVER_URL']);
define('SITE_URL',				substr(WEB_PATH, 0, -1));
define('DEFAULT_LANG',			$aConf['DEFAULT_LANG']);
define('SYS_UPDATES_URL',		$aConf['SYS_UPDATES_URL']);
define('SYS_DEV_UPDATES_URL',	$aConf['SYS_DEV_UPDATES_URL']);
define('SYS_UPDATES_SVC',		$aConf['SYS_UPDATES_SVC']);
define('SYS_GET_RELEASE_SVC',	$aConf['SYS_GET_RELEASE_SVC']);
define('SYS_SEND_STATE_SVC',	$aConf['SYS_SEND_STATE_SVC']);
// -- Error mails
define('ERROR_MAIL', str_replace(
                                '{__DOMAIN_NAME__}', 
                                DOMAIN_NAME, 
                                $aConf['ERROR_MAIL'])
                            );
unset($aConf);
// -- ERROR HANDLER
set_error_handler(array('exceptionErrorHandler', 'errorHandler'));
// -- interfaces 
include_once CORE_INTERFACE_PATH.'interface.iCacheSystem.php';
// -- function autoloader
function __autoload($sClass) {
    if (file_exists(CORE_CLASS_PATH.'svc.'.$sClass.'.php')) {
        include_once CORE_CLASS_PATH.'svc.'.$sClass.'.php';
    } elseif (file_exists(CORE_CLASS_PATH.'class.'.$sClass.'.php')) {
        include_once CORE_CLASS_PATH.'class.'.$sClass.'.php';
    } elseif (file_exists(LIB_PATH.strtr($sClass,'_','/').'.php')) {
        include_once LIB_PATH.strtr($sClass,'_','/').'.php';
    } elseif (file_exists(MODULES_CLASS_PATH.'svc.'.$sClass.'.php')) {
        include_once MODULES_CLASS_PATH.'svc.'.$sClass.'.php';
    } elseif (file_exists(MODULES_CLASS_PATH.'class.'.$sClass.'.php')) {
        include_once MODULES_CLASS_PATH.'class.'.$sClass.'.php';
    } elseif (file_exists(EXTERNAL_CLASS_PATH.$sClass.'.php')) {
        include_once EXTERNAL_CLASS_PATH.$sClass.'.php';
    } else {
        throw new CoreException('Class '.$sClass.' not found');
    }
}
if (!empty($_POST)) {
    Toolz_Format::XssKiller($_POST);
}
if (empty($_GET['page'])) {
    $_GET['page'] = 'home';
}
if (empty($_GET['lang'])) {
    $_GET['lang'] = DEFAULT_LANG;
}
Toolz_Main::checkVersion();
//fire
Starter::start($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
?>