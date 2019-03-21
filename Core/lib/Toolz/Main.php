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
final class Toolz_Main {

	public static $aScandirIgnore = array('.', '..', 'index.php');
	
	public static function checkVersion() {
		if(!defined('WEB_PATH')){
			throw new Toolz_Main_Exception('Constante WEB_PATH must be defined');
		}
		if(!defined('DEV')){
			throw new Toolz_Main_Exception('Constante DEV must be defined');
		} elseif (DEV === true) {
			ini_set('display_errors', 1);
		} elseif (DEV === false) {
			ini_set('display_errors', 0);
		}
	}
	
	/**
	 * display the banner development
	 */
	public static function getDevBanner() {
		if(dexad('DEV', false)) {
			if(UserRequest::getRequest('noDevBanner') || SessionCore::get('noDevBanner')) {
				SessionCore::set('noDevBanner', true);
				return '';
			} else {
				return str_replace(
							'{__SITE_URL__}', 
							SITE_URL, 
							file_get_contents(CORE_TPL_PATH.'inDev.tpl')
						);
			}
		}
	}
	
	/**
	 * display the banner draft
	 */
	public static function getDraftBanner() {
		if (dexad('DEV', false)) {
			return str_replace(
						'{__SITE_URL__}', 
						SITE_URL, 
						file_get_contents(CORE_TPL_PATH.'isDraft.tpl')
					);
		}
	}
}