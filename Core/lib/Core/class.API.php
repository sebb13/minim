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
abstract class API {
	
	/*
	 * https://www.pulsar-informatique.com/actus-blog/entry/mise-en-place-api-rest-en-php
	 */
	public $aAllow = array();
	public $sContentType = 'application/json';
	public $aRequest = array();
	private $iCode = 200;
	private $iStatus = array(
					100 => 'Continue',  
					101 => 'Switching Protocols',  
					200 => 'OK',
					201 => 'Created',  
					202 => 'Accepted',  
					203 => 'Non-Authoritative Information',  
					204 => 'No Content',  
					205 => 'Reset Content',  
					206 => 'Partial Content',  
					300 => 'Multiple Choices',  
					301 => 'Moved Permanently',  
					302 => 'Found',  
					303 => 'See Other',  
					304 => 'Not Modified',  
					305 => 'Use Proxy',  
					306 => '(Unused)',  
					307 => 'Temporary Redirect',  
					400 => 'Bad Request',  
					401 => 'Unauthorized',  
					402 => 'Payment Required',  
					403 => 'Forbidden',  
					404 => 'Not Found',  
					405 => 'Method Not Allowed',  
					406 => 'Not Acceptable',  
					407 => 'Proxy Authentication Required',  
					408 => 'Request Timeout',  
					409 => 'Conflict',  
					410 => 'Gone',  
					411 => 'Length Required',  
					412 => 'Precondition Failed',  
					413 => 'Request Entity Too Large',  
					414 => 'Request-URI Too Long',  
					415 => 'Unsupported Media Type',  
					416 => 'Requested Range Not Satisfiable',  
					417 => 'Expectation Failed',  
					500 => 'Internal Server Error',  
					501 => 'Not Implemented',  
					502 => 'Bad Gateway',  
					503 => 'Service Unavailable',  
					504 => 'Gateway Timeout',  
					505 => 'HTTP Version Not Supported'
		);
	
	public function __construct() {
		$this->inputs();
	}

	private function inputs() {
		switch($this->getRequestMethod()) {
			case 'POST':
				$this->aRequest = $this->cleanInputs(UserRequest::getParams());
				break;
			case 'GET':
			case 'DELETE':
				$this->aRequest = $this->cleanInputs(UserRequest::getRequest());
				break;
			case 'PUT':
				parse_str(file_get_contents("php://input"), $this->aRequest);
				$this->aRequest = $this->cleanInputs($this->aRequest);
				break;
			default:
				$this->response('', 406);
				break;
		}
	}
	
	public function getReferer() {
		return UserRequest::getEnv('HTTP_REFERER');
	}

	public function getRequestMethod() {
		return UserRequest::getEnv('REQUEST_METHOD');
	}

	public function sendResponse($mData, $iStatus, $sContentType='') {
		if(!empty($sContentType)) {
			$this->sContentType = $sContentType;
		}
		$this->iCode = ($iStatus) ? $iStatus : 200;
		$this->setHeaders();
		echo $mData;
		exit;
	}

	private function getStatusMessage() {
		return !empty($this->iStatus[$this->iCode]) 
			? $this->iStatus[$this->iCode] 
			: $this->iStatus[500];
	}	

	private function cleanInputs($mData) {
		if(is_array($mData)) {
			$mCleanInput = array();
			foreach($mData as $k => $v) {
				$mCleanInput[$k] = $this->cleanInputs($v);
			}
		}else{
			if(get_magic_quotes_gpc()) {
				$mData = trim(stripslashes($mData));
			}
			$mData = strip_tags($mData);
			$mCleanInput = trim($mData);
		}
		return $mCleanInput;
	}		

	private function setHeaders() {
		header("HTTP/1.1 ".$this->iCode." ".$this->getStatusMessage());
		header("Content-Type:".$this->sContentType);
	}
}