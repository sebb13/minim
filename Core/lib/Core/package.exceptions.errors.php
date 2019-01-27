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
abstract class exceptionErrorGeneric extends GenericException {
	/**
	 * @desc Error type
	 *
	 * @var string
	 */
	protected $sType;
	
	/**
	 * @desc Error file
	 *
	 * @var string
	 */
	protected $sErrFile;
	
	/**
	 * @desc Error line
	 *
	 * @var int
	 */
	protected $iErrLine;
	
	/**
	 * @desc Error context
	 *
	 * @var mixed
	 */
	protected $mVars;
	
	/**
	 * @desc is context enabled or not
	 *
	 * @var boolean
	 */
	protected $bContext;
	
	/**
	 * @desc constructor
	 *
	 * @param constant $cErrno
	 * @param string $sErrStr
	 * @param string $sErrFile
	 * @param int $iErrLine
	 * @param mixed $mVars
	 * @param boolean $bContext
	 */
	public function __construct($cErrno, $sErrStr, $sErrFile, $iErrLine, $mVars, $bContext = false) {
		parent::__construct($sErrStr, $cErrno);
		$this->sErrFile = $sErrFile;
		$this->iErrLine = $iErrLine;
		$this->mVars = $mVars;
		$this->bContext = $bContext;
	} 
}

/**
 * @desc exceptionErrors for Fatal errors
 *
 */
class exceptionErrorError extends exceptionErrorGeneric {
	protected $sType = 'Fatal error';
}

/**
 * @desc exceptionErrors for Warnings
 *
 */
class exceptionErrorWarning extends exceptionErrorGeneric {
	protected $sType = 'Warning';
}

/**
 * @desc exceptionErrors for Parse errors
 *
 */
class exceptionErrorParseError extends exceptionErrorGeneric {
	protected $sType = 'Parse error';
}

/**
 * @desc exceptionErrors for Notice
 *
 */
class exceptionErrorNotice extends exceptionErrorGeneric {
	protected $sType = 'Notice';
}

/**
 * @desc exceptionErrors for Core errors
 *
 */
class exceptionErrorCoreError extends exceptionErrorGeneric {
	protected $sType = 'Core error';
}

/**
 * @desc exceptionErrors for Core warnings
 *
 */
class exceptionErrorCoreWarning extends exceptionErrorGeneric {
	protected $sType = 'Core warning';
}

/**
 * @desc exceptionErrors for Compile errors
 *
 */
class exceptionErrorCompileError extends exceptionErrorGeneric {
	protected $sType = 'Compile error';
}

/**
 * @desc exceptionErrors for Compile warnings
 *
 */
class exceptionErrorCompileWarning extends exceptionErrorGeneric {
	protected $sType = 'Compile warning';
}

/**
 * @desc exceptionErrors for User errors
 *
 */
class exceptionErrorUserError extends exceptionErrorGeneric {
	protected $sType = 'User error';
}

/**
 * @desc exceptionErrors for User warnings
 *
 */
class exceptionErrorUserWarning extends exceptionErrorGeneric {
	protected $sType = 'User warning';
}

/**
 * @desc exceptionErrors for User notices
 *
 */
class exceptionErrorUserNotice extends exceptionErrorGeneric {
	protected $sType = 'User notice';
}

/**
 * @desc exceptionErrors for Strict errors
 *
 */
class exceptionErrorStrictError extends exceptionErrorGeneric {
	protected $sType = 'Strict error';
}

/**
 * @desc exceptionErrors for recoverable errors
 *
 */
class exceptionErrorRecoverableError extends exceptionErrorGeneric {
	protected $sType = 'Recoverable error';
}

/**
 * @desc exceptionErrors for not handled yet errors
 *
 */
class exceptionErrorNotHandledYet extends exceptionErrorGeneric {
	protected $sType = 'Not handled yet';
}

/**
 * @desc error handler, calling correct exceptionError type
 *
 */
class exceptionErrorHandler {
	/**
	 * @desc translation between context error and exceptionError type of class
	 *
	 * @var array
	 */
	public static $aTrans = array (
		E_ERROR				=> 'exceptionErrorError',
		E_WARNING			=> 'exceptionErrorWarning',
		E_PARSE				=> 'exceptionErrorParseError',
		E_NOTICE			=> 'exceptionErrorNotice',
		E_CORE_ERROR		=> 'exceptionErrorCoreError',
		E_CORE_WARNING		=> 'exceptionErrorCoreWarning',
		E_COMPILE_ERROR		=> 'exceptionErrorCompileError',
		E_COMPILE_WARNING	=> 'exceptionErrorCompileWarning',
		E_USER_ERROR		=> 'exceptionErrorUserError',
		E_USER_WARNING		=> 'exceptionErrorUserWarning',
		E_USER_NOTICE		=> 'exceptionErrorUserNotice',
		E_STRICT			=> 'exceptionErrorStrictError',
		E_RECOVERABLE_ERROR	=> 'exceptionErrorRecoverableError'
	);
	
	/**
	 * @desc is context enabled or not
	 *
	 * @var boolean
	 */
	public static $bContext = false;
	
	/**
	 * @desc constructor, optional bContext boolean can be given if you want context to be displayed or not
	 *
	 * @param boolean $bContext (optional, default = false)
	 */
	public function __construct($bContext = false) {
		self::$bContext = $bContext;
		set_error_handler(array($this, 'errorHandler'));
	}

	/**
	 * @desc error handler
	 *
	 * @param constant $cErrno
	 * @param string $sErrStr
	 * @param string $sErrFile
	 * @param int $iErrLine
	 * @param mixed $mVars
	 */
	public static function errorHandler ($cErrno, $sErrStr, $sErrFile, $iErrLine, $mVars) {
		if(!isset(self::$aTrans[$cErrno])) {
			throw new exceptionErrorNotHandledYet($cErrno, $sErrStr, $sErrFile, $iErrLine, $mVars, self::$bContext);
		} else {
			//echo '#', $sErrStr, '#';
			throw new self::$aTrans[$cErrno]($cErrno, $sErrStr, $sErrFile, $iErrLine, $mVars, self::$bContext);
		}
	}
}
?>