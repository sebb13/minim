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
final class DownloadMgr {
	
	public static function counterUpdate(array $aFiles, $sFileId) {
		// et les ips ??
		$aCounterFiles = array();
		if(!file_exists(DATA_PATH.'download.json')) {
			foreach($aFiles as $sId=>$sPath) {
				if($sId === $sFileId) {
					$aCounterFiles[$sId] = '1';
				} else {
					$aCounterFiles[$sId] = '0';
				}
			}
		} else {
			$aCounterFilesTmp = json_decode(
									file_get_contents(DATA_PATH.'download.json')
			);
			foreach($aFiles as $sId=>$sPath) {
				if(!isset($aCounterFilesTmp->$sId)) {
					$aCounterFiles[$sId] = '1';
				} else {
					foreach($aCounterFilesTmp as $sId=>$sCount) {
						if($sId === $sFileId) {
							$aCounterFiles[$sId] = (int)$sCount+1;
						} else {
							$aCounterFiles[$sId] = $sCount;
						}
					}
				}
			}
		}
		return file_put_contents(
							DATA_PATH.'download.json', 
							json_encode($aCounterFiles)
						);
	}
}