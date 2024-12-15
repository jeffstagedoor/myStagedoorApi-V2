<?php
/**
 * a class that defines and shows version, author, etc. of this package
 *
 * @author Jeff Frohner <office@jefffrohner.com>
 * @copyright Jeff Frohner 2017-2021
 * @version 2.0.0
 * @package Jeff\Api
 */
namespace myStagedoor\Api;

class Info
{
	/** @var string vesion of this Api class */
	public static $version = "2.0.0";
	/** @var string Author */
	public static $author = "Jeff Frohner";
	/** @var string when I coded that */
	public static $year = "2024";
	/** @var string the licence */
	public static $licence = "MIT";
	/** @var string what kid of API this is. New Version is JSON */
	public static $type = "JSON";
	/** @var string just a string to say where this API can be used*/
	public static $restriction = "authorized apps and logged in users only";

	/**
	 *	returns a collection of ApiInfos as json (default)
	 *	
	 *	@param string $format ('array', 'json'=default) - NOT IMPLEMENTED
	 *	@return string // json
	 **/
	public static function getApiInfo(string $format = 'json')
	{
		$array = Array(
			"version" => self::$version,
			"author" => self::$author,
			"year" => self::$year,
			"licence" => self::$licence,
			"type" => self::$type,
			"restriction" => self::$restriction
		);
		return json_encode($array);
	}
}