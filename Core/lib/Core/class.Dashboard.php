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
final class Dashboard {
	
	private static $sDashboardContainerTplName	= 'dashboard.container.tpl';
	private static $sDashboardContainerTpl		= '';
	private static $sMinimDashboardTplName		= 'dashboard.minim.tpl';
	
	public static function getDashboard($sTitle, $sContents, $sSize='6') {
		if(empty(self::$sDashboardContainerTpl)) {
			self::$sDashboardContainerTpl = file_get_contents(ADMIN_PARTS_TPL_PATH.self::$sDashboardContainerTplName);
		}
		return str_replace(
						array(
							'{__TITLE__}',
							'{__CONTENTS__}',
							'{__SIZE__}'
						), 
						array(
							$sTitle, 
							$sContents,
							$sSize
						), 
						self::$sDashboardContainerTpl
					);
	}
	
	public static function getMinimDashboard() {
		
	}
}