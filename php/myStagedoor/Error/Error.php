<?php

namespace myStagedoor\Error;

Class Error {
	private $httpCode;
	private $title;
	public $msg;
	public $internal = false;
	private $critical = 0;
	private $stackTrace;
	Const DEFAULT_INTERNAL = false;
	Const DEFAULT_CRITICAL = 0;
	Const DEFAULT_CODE = 500;



	public function __construct($e, $info=null) {
		if(is_integer($e)) {
			$err = ErrorHandler::$Codes[$e];
			// var_dump($err);
			$this->title = $err['title'];
			$this->msg = $err['msg'];
			$this->httpCode = $err['httpCode'];
			$this->critical = $err['critical'];
			$this->internal = isset($err['internal']) ? $err['internal'] : false;
			$this->stackTrace = $info;
		} elseif(is_array($e)) {
			// if I get an Array, it's a custom error in format ['title', 'msg', [int] code, [int] critical, [bool] internal, [object] Exception]
			$this->title = $e[0];
			$this->msg = $e[1];
			$this->httpCode = isset($e[2]) ? $e[2] : self::DEFAULT_CODE;
			$this->critical = isset($e[3]) ? $e[3] : self::DEFAULT_CRITICAL;
			$this->internal = isset($e[4]) ? $e[4] : self::DEFAULT_INTERNAL;
			if(isset($e[5]) && is_a($e[5], "Exception")) {
				$this->stackTrace = $this->_ExceptionToLogString($e[5]);
			}
		} elseif (is_string($e)) {
			// if I get only ONE String, try to make the best out of it
			$this->httpCode = 500;
			$this->title = "Custom Error";
			$this->msg = $e;
			$this->critical = self::DEFAULT_CRITICAL;
			$this->internal = true;
			$this->stackTrace = $info;
		}
	}

	public function toArray($internal=false) {
		$a = Array(
			"title"=> $this->title,
			"msg"=> $this->msg,
			"httpCode"=> $this->httpCode,

			);
		if($internal) {
			$a["internal"]= $this->internal;
			$a["critical"]= $this->critical;
			$a["stackTrace"]= $this->stackTrace;
			
		}

		return $a;
	}

	public function isPublic() {
		if($this->internal) {
			return false;
		} else {
			return true;
		}
	}

	private function _ExceptionToLogString($e) {
		$string =  "  ".$e->getMessage()."\r\n";
		$string .= "   in File: ".$e->getFile()." - Line ".$e->getLine()."\r\n";
		$string .= "   StackTrace: \r\n";
		$trace = $e->getTrace();
		foreach ($trace as $key => $value) {
			// print_r($value);
			$string .= "     {$value['file']}:{$value['line']}, class: {$value['class']}, function: {$value['function']}\r\n";
		}
		return $string;
	}
}