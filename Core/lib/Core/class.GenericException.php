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
class GenericException extends Exception {
	
	private $oErrorLogs = null;
	
	public function __construct($sMsg='', $iCode=0, Exception $oPrevious=null) {
		 parent::__construct($sMsg, $iCode, $oPrevious);
		 $this->oErrorLogs = new ErrorLogs();
	}
	
	public function log() {
		$this->oErrorLogs->addLog($this->oErrorLogs->getDebugEnv(true).parent::__toString());
		if(!dexad('DEV',false)) {
			mail(
				str_replace('{__DOMAIN_NAME__}', DOMAIN_NAME, ERROR_MAIL),
				'Une erreur est survenue sur '.DOMAIN_NAME,
				$this->oErrorLogs->getDebugEnv(true).parent::__toString()
			);
			return '';
		} elseif(UserRequest::getRequest('debug') !== false) {
			return	$this->oErrorLogs->getDebugEnv(true).
					parent::getMessage().' <pre>'.
					parent::__toString().'</pre>';
		} else {
			return '';
		}
	}

	public function __toString() {
		return $this->log();
	}
}

class CoreException	extends GenericException {
	
	const INTERNAL_ERROR = 10;
}
class Toolz_Exception					extends GenericException {}
final class Toolz_Transform_Exception	extends Toolz_Exception {}
final class Toolz_Form_Exception		extends Toolz_Exception {}
final class Toolz_Format_Exception		extends Toolz_Exception {}
final class Toolz_Mailer_Exception		extends Toolz_Exception {}
final class Toolz_Main_Exception		extends Toolz_Exception {}
final class Toolz_Checker_Exception		extends Toolz_Exception {}
final class Toolz_Img_Exception			extends Toolz_Exception {}