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
final class MetasTags {
	
	private static $sOgMetaTplName		= 'og.meta.tpl';
	private static $aPageConfig			= array();
	private static $sMetaPattern		= '<meta name="{__NAME__}" content="{__CONTENT__}" />';
	private static $sGooglePattern		= '<meta itemprop="{__NAME__}" content="{__CONTENT__}" />';
	public static $aAllowedNames		= array(
											'description'=>'description',
											'msvalidate_01'=>'msvalidate.01',
											'keywords'=>'keywords',
											'author'=>'author',
											'current-period'=>'current-period',
											'current-thread-id'=>'current-thread-id'
										);
	public static $aOgList = array(
				'type',
				'title',
				'url',
				'image',
				'audio',
				'description',
				'determiner',
				'locale',
				'site_name',
				'video',
		);
	public static $aTwitterList = array(
				'twitter:card' => 'summary_large_image',
				'twitter:site'=>'',
				'twitter:title' => '',
				'twitter:description'=>'',
				'twitter:creator'=>'',
				'twitter:image'=>''
		);
	public static $aGoogleList = array(
				'g+:name',
				'g+:description',
				'g+:image'
		);
	
	public static function getMetaTags(array $aPageConfig) {
		self::$aPageConfig = $aPageConfig;
		if (!DEV && !ADMIN) {
			$sRobots = self::$aPageConfig['robots'];
		} else {
			$sRobots = 'noindex,nofollow';
		}
		$sMetas = Minifier::genericMinify(
									str_replace(
											array(
												'{__ROBOTS_META__}',
												'{__META_FROM_CONF__}',
												'{__TWITTER_SECTION__}',
												'{__GOOGLE_SECTION__}',
												'{__OG_SECTION__}',
												'{__TOKEN__}',
												'{__CURRENT_PAGE__}',
												'{__LANG__}',
												'{__LANG_AVAILABLE__}'
											), 
											array(
												$sRobots,
												self::getMetaTagsFromConf(),
												self::getTwitterSection(),
												self::getGoogleSection(),
												self::getOgSection(),
												SessionCore::getSessionHash(),
												UserRequest::getRequest('sPage'),
												UserRequest::getRequest('sLang'),
												json_encode(SessionCore::$oLang->getAvailable())
											), 
											file_get_contents(INC_TPL_PATH.'metaTags.tpl')
										)
									);
		if (!DEV && !ADMIN) {
			$sMetas .= file_get_contents(DATA_PATH.'google-analytics.tpl');
		}
		return $sMetas;
	}
	
	private static function getMetaTagsFromConf() {
		if(empty(self::$aPageConfig['meta'])) {
			return '';
		}
		$sMetaTags = '';
		foreach(self::$aPageConfig['meta'] as $sName=>$sContent) {
			if(in_array($sName, self::$aAllowedNames)) {
				$sMetaTags .= str_replace(
									array('{__NAME__}', '{__CONTENT__}'), 
									array($sName,$sContent), 
									self::$sMetaPattern
								);
			}
		}
		return $sMetaTags;
	}
	
	private static function getOgSection() {
		$sOgSection = '';
		if(empty(self::$aPageConfig['og'])) {
			return '';
		}
		$sOgMetaTpl = file_get_contents(INC_TPL_PATH.self::$sOgMetaTplName);
		foreach(self::$aPageConfig['og'] as $sName=>$sContent) {
			$sOgSection .= str_replace(
				array('{__NAME__}','{__CONTENT__}'), 
				array($sName,$sContent), 
				$sOgMetaTpl
			);
		}
		return $sOgSection;
	}
	
	private static function getTwitterSection() {
		$sTwitterSection = '';
		if(empty(self::$aPageConfig['twitter'])) {
			return '';
		}
		foreach(self::$aPageConfig['twitter'] as $sName=>$sContent) {
			$sTwitterSection .= str_replace(
				array('{__NAME__}','{__CONTENT__}'), 
				array($sName,$sContent), 
				self::$sMetaPattern
			);
		}
		return $sTwitterSection;
	}
	
	private static function getGoogleSection() {
		$sGoogleSection = '';
		if(empty(self::$aPageConfig['google'])) {
			return '';
		}
		foreach(self::$aPageConfig['google'] as $sName=>$sContent) {
			$sGoogleSection .= str_replace(
				array('{__NAME__}','{__CONTENT__}'), 
				array(str_replace('g+:', '', $sName),$sContent), 
				self::$sGooglePattern
			);
		}
		return $sGoogleSection;
	}
}