<?php
/**
 * Backed Enum Log\Level
 * 
 * to be used both in APILog as in application (db) Log
 * @author Jeff Frohner
 * @copyright Copyright (c) 2024
 * @license   private
 * @version   1.0.0
 */
namespace myStagedoor\Log;

enum Level :int {
	case CRITICAL = 0;
	case ERROR = 1;
	case WARNING = 2;
	case NOTICE = 3;
	case INFO = 4;
	case DEBUG = 5;
	case TRACE = 6;
}