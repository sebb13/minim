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
final class Minifier extends SimpleXmlMgr {
	
	public function __construct() {
		return parent::__construct();
	}
	
	public function concatJs() {
		// préparation des données
		$aBackFunctions		= array();
		$aBackLaunchers		= array();
		$aBackModuleFunctions	= array();
		$aBackModuleLaunchers	= array();
		$aFrontFunctions		= array();
		$aFrontLaunchers		= array();
		$aFrontModuleFunctions	= array();
		$aFrontModuleLaunchers	= array();
		$this->loadFile(DATA_PATH.'concat_js.xml');
		$aConf = $this->getIemsList();
		foreach($aConf as $sModuleName=>$aFiles) {
			foreach($aFiles['file'] as $aFileInfos) {
				if(empty($aFileInfos['url'])) {
					//si un seul noeud "file"
					$aFileInfos = $aFiles['file'];
				}
				if (isset($aFileInfos['type'])) {
					if ($sModuleName === 'minim') {
						$sJsContents = file_get_contents(JS_PATH.$aFileInfos['url']);
						if ($aFileInfos['type'] === 'functions') {
							if ($aFileInfos['role'] === 'required') {
								$aFrontFunctions[$aFileInfos['level']] = $sJsContents;
								$aBackFunctions[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'front') {
								$aFrontFunctions[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'back') {
								$aBackFunctions[$aFileInfos['level']] = $sJsContents;
							} else {
								throw new CoreException('unknown role "'.$aFileInfos['role'].'"');
							}
						
						} elseif($aFileInfos['type'] === 'launchers') {
							if($aFileInfos['role'] === 'required') {
								$aFrontLaunchers[$aFileInfos['level']] = $sJsContents;
								$aBackLaunchers[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'front') {
								$aFrontLaunchers[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'back') {
								$aBackLaunchers[$aFileInfos['level']] = $sJsContents;
							} else {
								throw new CoreException('unknown role "'.$aFileInfos['role'].'"');
							}
						} else {
							throw new CoreException('unknown type "'.$aFileInfos['type'].'"');
						}
					} else {
						$sJsContents = file_get_contents(MODULES_PATH.ucfirst($sModuleName).'/js/'.$aFileInfos['url']);
						if($aFileInfos['type'] === 'functions') {
							if($aFileInfos['role'] === 'required') {
								$aFrontModuleFunctions[$aFileInfos['level']] = $sJsContents;
								$aBackModuleFunctions[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'front') {
								$aFrontModuleFunctions[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'back') {
								$aBackModuleFunctions[$aFileInfos['level']] = $sJsContents;
							} else {
								throw new CoreException('unknown role "'.$aFileInfos['role'].'"');
							}
						} elseif($aFileInfos['type'] === 'launchers') {
							if($aFileInfos['role'] === 'required') {
								$aFrontModuleLaunchers[$aFileInfos['level']] = $sJsContents;
								$aBackModuleLaunchers[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'front') {
								$aFrontModuleLaunchers[$aFileInfos['level']] = $sJsContents;
							} elseif($aFileInfos['role'] === 'back') {
								$aBackModuleLaunchers[$aFileInfos['level']] = $sJsContents;
							} else {
								throw new CoreException('unknown role "'.$aFileInfos['role'].'"');
							}
						} else {
							throw new CoreException('unknown type "'.$aFileInfos['type'].'"');
						}
					}
				}
			}
		}
		// traitement des fonctions FRONT, minim en premier
		$sFrontFunctions = implode('', $aFrontFunctions).implode('', $aFrontModuleFunctions);
		// traitement des exécution FRONT, minim en premier ($(document).ready())
		$sFrontLaunchers = str_replace(
							'{__CONTENTS__}',
							implode('', $aFrontLaunchers).implode('', $aFrontModuleLaunchers),
							file_get_contents(CORE_TPL_PATH.'launchers.js.tpl')
		);
		$sFrontJsScript = self::genericMinify($sFrontFunctions.$sFrontLaunchers);
		self::saveConcat(JS_PATH.'main.front.js', $sFrontJsScript);
		
		// traitement des fonctions BACK, minim en premier
		$sBackFunctions = implode('', $aBackFunctions).implode('', $aBackModuleFunctions);
		// traitement des exécution BACK, minim en premier ($(document).ready())
		$sBackLaunchers = str_replace(
							'{__CONTENTS__}',
							implode('', $aBackLaunchers).implode('', $aBackModuleLaunchers),
							file_get_contents(CORE_TPL_PATH.'launchers.js.tpl')
		);
		$sBackJsScript = self::genericMinify($sBackFunctions.$sBackLaunchers);
		self::saveConcat(JS_PATH.'main.back.js', $sBackJsScript);
		unset($sFrontFunctions,$sFrontLaunchers,$sBackFunctions,$sBackLaunchers,
			$aBackFunctions,$aBackLaunchers,$aBackModuleFunctions,$aBackModuleLaunchers,
			$aFrontFunctions,$aFrontLaunchers,$aFrontModuleFunctions,$aFrontModuleLaunchers);
		return array('concatJs'=>__METHOD__);
	}
	
	public function concatCss() {
		$this->loadFile(DATA_PATH.'concat_css.xml');
		$aConf = $this->getIemsList();
		// préparation des données
		$aFrontCss		= array();
		$aFrontModuleCss= array();
		$aBackCss		= array();
		$aBackModuleCss	= array();
		foreach($aConf as $sModuleName=>$aFiles) {
			foreach($aFiles['file'] as $aFileInfos) {
				if(empty($aFileInfos['url'])) {
					//si un seul noeud "file"
					$aFileInfos = $aFiles['file'];
				}
				if ($sModuleName === 'minim') {
					if(strpos($aFileInfos['url'], 'http') === 0) {
						$sCssPath = $aFileInfos['url'];
					} else {
						$sCssPath = CSS_PATH.$aFileInfos['url'];
					}
					$sCssContents = self::genericMinify(file_get_contents($sCssPath));
					if ($aFileInfos['role'] === 'required') {
						$aFrontCss[$aFileInfos['level']] = $sCssContents;
						$aBackCss[$aFileInfos['level']] = $sCssContents;
					} elseif($aFileInfos['role'] === 'front') {
						$aFrontCss[$aFileInfos['level']] = $sCssContents;
					} elseif($aFileInfos['role'] === 'back') {
						$aBackCss[$aFileInfos['level']] = $sCssContents;
					} else {
						throw new CoreException('unknown role "'.$aFileInfos['role'].'"');
					}
				} else {
					$sCssContents = self::genericMinify(
												file_get_contents(
													MODULES_PATH.ucfirst($sModuleName).'/css/'.$aFileInfos['url']
												)
									);
					if($aFileInfos['role'] === 'required') {
						$aFrontModuleCss[] = $sCssContents;
						$aBackModuleCss[] = $sCssContents;
					} elseif($aFileInfos['role'] === 'front') {
						$aFrontModuleCss[] = $sCssContents;
					} elseif($aFileInfos['role'] === 'back') {
						$aBackModuleCss[] = $sCssContents;
					} else {
						throw new CoreException('unknown role "'.$aFileInfos['role'].'"');
					}
				}
			}
		}
		// traitement des fonctions FRONT, minim en premier
		$sFrontCss = implode('', $aFrontCss).implode('', $aFrontModuleCss);
		self::saveConcat(CSS_PATH.'main.front.css', $sFrontCss);
		// traitement des fonctions BACK, minim en premier
		$sBackCss = implode('', $aBackCss).implode('', $aBackModuleCss);
		self::saveConcat(CSS_PATH.'main.back.css', $sBackCss);
		unset($sFrontCss,$sBackCss,$aFrontCss,$aFrontModuleCss,$aBackCss,$aBackModuleCss);
		return array('concatCss'=>__METHOD__);
	}
	
	private static function saveConcat($sPath, $sContents) {
		if(!file_put_contents($sPath, $sContents)) {
			throw new CoreException('Can not save file "'.$sPath.'"');
		}
	}
	
	public static function concat($sType, $sPath, array $aConf) {
		$sOutput = '';
		ksort($aConf);
		foreach($aConf as $aFile) {
			$sOutput .= file_get_contents($sPath.$aFile['name']);
		}
		file_put_contents($sPath.'main.'.$sType, $sOutput);
		return array('concatJs'=>__METHOD__);
	}
	
	/*
	 * DEPRECATED
	 */
	public static function minify($sPath, $sType) {
		$sOut = '';
		if (file_exists($sPath.'main.min.'.$sType)) {
			unlink($sPath.'main.min.'.$sType);
		}
		foreach (scandir($sPath) as $sFilename) {
			if(pathinfo($sFilename, PATHINFO_EXTENSION) === $sType) {
				$sOut .= self::genericMinify(file_get_contents($sPath.$sFilename));
			}
		}
		file_put_contents($sPath.'main.min.'.$sType, $sOut);
		return array('minify'=>__METHOD__);
	}
	
	public static function minifyHtml($sInput) {
		return str_replace(array("\r\n", "\n", "\r", "\t"), '', $sInput);
	}
	
	public static function genericMinify($sBuffer) 
{		// -- On vire les commentaires
		$sBuffer = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $sBuffer);
		// -- On vire les espaces, tabulations, etc...
		$sBuffer = str_replace(array("\r\n", "\n", "\r", "\t", '  ', '   '), '', $sBuffer);
		$sBuffer = str_replace(array(' { ',' {','{ '), '{', $sBuffer);
		$sBuffer = str_replace(array(' } ',' }','} '), '}', $sBuffer);
		$sBuffer = str_replace(array(' = ',' =','= '), '=', $sBuffer);
		return $sBuffer;
	}
	/*
	 * DEPRECATED
	 */
	public static function minifyJs($sPath) {
		$sInput = file_get_contents(str_replace('.min', '', $sPath));
		$sOutput = self::genericMinify($sInput);
		file_put_contents($sPath, $sOutput);
		return array('minifyJs'=>__METHOD__);
	}
}