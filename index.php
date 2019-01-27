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
try {
    require 'Core/inc/inc.coreAutoConfig.php';
    $oRouter = new Router();
    echo $oRouter->checkRequest();
} catch (GenericException $e) {
    echo $e;
} catch (Exception $e) {
    if (dexad('DEV', false)) { 
        echo $e-> getMessage(),'<br />';
		print_r($e->getTrace());
    } else {
		mail(
			ERROR_MAIL,
			'Une erreur est survenue sur '.WEB_PATH,
			$this->getDebugEnv(true).parent::__toString()
		);
	}
}