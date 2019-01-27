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
final class CookiesConsent {
		
	private static $sCookiesConsentBannerTplName	= 'cookiesConsent.banner.tpl';
	private static $sCookiesConsent					= 'cookiesConsent';
	private static $sLearnMorUrl					= 'legalNotices.html#cookies';
	
	public static function getCookiesConsentBanner() {
		if(UserRequest::getCookie(self::$sCookiesConsent) === '1') {
			return '';
		} else {
			return str_replace(
						'{__LEARN_MORE_URL__}', 
						self::$sLearnMorUrl,
						file_get_contents(CORE_TPL_PATH.self::$sCookiesConsentBannerTplName)
					);
		}
	}
	
	public static function setCookiesConsent() {
		return UserRequest::setCookie(
								self::$sCookiesConsent, 
								'1', 
								strtotime('+13 months')
							);
	}
}