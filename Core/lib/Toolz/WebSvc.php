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
final class Toolz_WebSvc {

	public static function curlMinim($sUrl, $aPost, $sAuth) {
		$aDefaults = array( 
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $sUrl.'/index.php?page=home&lang='.DEFAULT_LANG,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS => http_build_query($aPost),
			CURLOPT_USERPWD => $sAuth
		); 
		$rCh = curl_init(); 
		curl_setopt_array($rCh, $aDefaults);
		$mResult = curl_exec($rCh);
		$http_status = curl_getinfo($rCh, CURLINFO_HTTP_CODE);
		if(empty($mResult)) {
			return $http_status.' '.curl_error($rCh);
		}
		curl_close($rCh);
		return $mResult;
	}
} 