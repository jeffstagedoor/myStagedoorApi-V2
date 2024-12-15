<?php
/**
 * File contains the class Log\Api
 */

namespace myStagedoor\Log;
use myStagedoor\Config\Environment;


/**
* Class Api
*
* 
* @author Jeff Frohner
* @copyright Copyright (c) 2024
* @license   private
* @version   1.0.0
*
**/
class Api {

	public static function write(string $line, Level $level = Level::ERROR) :bool {
		if(in_array($level, Environment::$logLevels)) {
			try {
				$logPath = Environment::$dirs->apiLog;
				if (!is_dir($logPath)) {
					mkdir($logPath, 0664, true);
				}
				$logFileName = "ApiLog ".date('Ymd').".txt";
				return file_put_contents($logPath.DIRECTORY_SEPARATOR.$logFileName, date('d.m.Y H:i:s').'     '.$line.PHP_EOL , FILE_APPEND | LOCK_EX);
			} catch (\Throwable $th) {
				throw $th;
			}
		} else {
			return false;
		}
	}

	// public static function writeSimple(string $line, Level $level = Level::ERROR) :bool {
	// 	return self::write(date('d.m.Y H:i:s').'     '.$line, $level);
	// }
}