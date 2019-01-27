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
function array_key_exists_assign_default($mSearch,$aArray,$mDefault){
	if (is_array($aArray)){
		if (array_key_exists($mSearch,$aArray)){
			$mReturn=$aArray[$mSearch];
		}else{
			$mReturn = $mDefault;
		}
	}else{
		$mReturn = $mDefault;
	}
	return $mReturn;
}

function akead($mSearch,$aArray,$mDefault){
	return array_key_exists_assign_default($mSearch,$aArray,$mDefault);
}

function define_exists_assign_default($mSearch,$mDefault){
	return defined($mSearch) ? constant($mSearch) : $mDefault;
}

function dexad($mSearch,$mDefault){
	return define_exists_assign_default($mSearch,$mDefault);
}

function debug($mDebug) {
	if(dexad('DEV', false)) {
		echo '<pre>';
		var_dump($mDebug);
		echo '</pre>';
	}
}
?>