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
final class SocialNetwork {
	
	private static $aAllowedLanguage = array('html','js');
	
	public static function getAll($sLanguage='') {
		if (!in_array($sLanguage, self::$aAllowedLanguage)) {
			throw new CoreException('language not allowed');
		}
		$sReturn = '';
		//if (!dexad('DEV', false) && !dexad('ADMIN', false)) {
			foreach(scandir(SOC_NET_TPL_PATH.$sLanguage) as $sFilename) {
				if (pathinfo($sFilename,PATHINFO_EXTENSION) === 'tpl') {
					$sReturn .= file_get_contents(SOC_NET_TPL_PATH.$sLanguage.'/'.$sFilename);
				}
			}
		//}
		return $sReturn;
	}
}