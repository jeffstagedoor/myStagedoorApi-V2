<?php
/**
 * File contains the class Log
 */

namespace myStagedoor;
// use myStagedoor\Api as Api;
use myStagedoor\Log\Default\Config;
use myStagedoor\Log\Default\Meta;
use myStagedoor\Log\Default\TargetUsers;
use myStagedoor\Error\ErrorHandler;
use myStagedoor\Error\Error;
use myStagedoor\Config\Environment;

/**
* Class Log
*
* 
* @author Jeff Frohner
* @copyright Copyright (c) 2018
* @license   private
* @version   1.2.0
*
**/
Class Log {
	/** @var \MySqliDb Instance of database class */
	protected static \MysqliDb $db;
	/** @var Config Instance of Log\Config class */
	protected static \stdClass $logConfig;
	/** @var bool if the Log is ready to write (after checking Log\Config and DB) */
	protected static bool $readyToWrite = false;
	/** @var string NOT USED? */
	public static string $modelName = "Log";
	/** @var string db-table name */
	protected static string $dbTable = "log";
	protected static $user;
	protected static $type;
	protected static int $loginattempt;
	protected static bool $success;

	/**
	*
	* Database-Table definition to be used by {@see DBHelper} class to create the corresponding table
	* @var array $dbDefinition Database-Table definition
	* @see DBHelper
	* 
	*/
	public array $dbDefinition = Array(
			array ('id', 'int', '11', false, NULL, 'auto_increment'),
			array ('A', 'int', '11', true, NULL),
			array ('ARights', 'int', '11', true, NULL),
			array ('B', 'int', '11', true, NULL),
			array ('BRights', 'int', '11', true, NULL),
			array ('C', 'int', '11', true, NULL),
			array ('CRights', 'int', '11', true, NULL),
			array ('D', 'int', '11', true, NULL),
			array ('DRights', 'int', '11', true, NULL),

			array ('type', 'varchar', '50', false),
			array ('item', 'varchar', '50', true, NULL),

			array ('meta1', 'int', '11', true, NULL),
			array ('meta2', 'int', '11', true, NULL),
			array ('meta3', 'int', '11', true, NULL),
			array ('meta4', 'varchar', '80', true, NULL),
			array ('meta5', 'varchar', '255', true, NULL),

			array ('logDate', 'timestamp', null, false, 'CURRENT_TIMESTAMP'),
			array ('user', 'int', '11', true, NULL),

		);
	/** @var string db primary key = 'id' */
	public string $dbPrimaryKey = 'id';

	/**
	* the database keys/indexes definition which shall look like that:
	*	           
	*	           ```
	*	           array(
	*	               "name" => "firstIndex",
	*	               "collation" => "A",
	*	               "cardinality" => 5,
	*	               "type" => "BTREE",
	*	               "comment" => "This is a database index foo bar, whatsoever",
	*	               "columns" => ["fieldName1", "anotherField"]
	*	           )
	*	           ```
	*	
	* @var array   the database keys/indexes definition, 
	*/	
	public array $dbKeys = [];

	/**
	 * Constructor
	 *
	 * Sets passed in instances to local vars.
	 * 
	 * Checks if a 'LogConfig' is implemented in consuming app and throws error if not.
	 * If implemented it gets the config, sets $readyToWrite to true.
	 * 
	 * Checks for ready database and trwos Error if not existing or tableName not found
	 * @param \MySqliDb                $db    Instance of database class
	 */
	public static function init($db): void {
		self::$db = $db;
		Environment::$dirs->appRoot;
		self::$logConfig = Config::values();

		// check if we have a database ready:
		try {
			self::$db->connect();
		} catch(\Exception $e) {
			var_dump($e);
			self::$readyToWrite = false;
			ErrorHandler::add(20);
			ErrorHandler::sendErrors();
			exit;
		}


		$logTable = self::$db->rawQuery("SHOW tables like '".Config::getDbTable()."'");
		if(count($logTable)>0) { 
			self::$readyToWrite=true;
		} else {
			self::$readyToWrite = false;
			ErrorHandler::add(new Error(ErrorHandler::LOG_NO_TABLE));
			ErrorHandler::sendErrors();
		}
	}

	/** 
	*	Basic API Write to DBLog - Method.
	*	Can be overridden by special logs
	*	@param int $accountId      the account id of the current operating user
	*	@param string $type        the type of current action like 'update', 'delete', 'create'
	*	@param string $itemName    name of the manipulated item like 'post', 'comment', 'event' (has to match LogConfig items)
	*	@param mixed  $data        either an array with the manipulating data (as arriving from client), 
	*						       OR an object containing a LogDefaultFor and a LogDefaultMeta as data->for, data->meta[, data->dataset]
	*	
	**/
	public static function write($accountId, $type, $itemName, $data): null|int {
		if(!self::$readyToWrite) {
			return null;
		}
		// check if we have a 'for' and a 'meta' given in $data
		if(isset($data->for) && isset($data->meta)) {
			$for = $data->for;
			$meta = $data->meta;

		} else {
			// first Prepare from custom LogConfig
			// so let's see if we have a configuration for the given item:
			// echo "itemName:" .$itemName."\n";
			if (isset( self::$logConfig->{$itemName} )) {
				$for = self::extractDataFromConfig(self::$logConfig->{$itemName}->for, $data);
				$meta = self::extractDataFromConfig(self::$logConfig->{$itemName}->meta, $data);
			} else {
				// fallback to default
				$logForConfig = new TargetUsers(NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
				#var_dump($data);
				if(isset($data->{$itemName}['id']) && is_int($data->{$itemName}['id'])) {
					// if id is an int, store it in meta1
					$logMetaConfig = new Meta(Array($itemName,"id"),NULL,NULL,NULL,NULL);
				} else {
					// otherwise (for relational tables like accounts2workgroup) store it in meta4, which is a varchar
					$logMetaConfig = new Meta(NULL,NULL,NULL,Array($itemName,"id"),NULL);
				}
				$for = self::extractDataFromConfig($logForConfig, $data);
				$meta = self::extractDataFromConfig($logMetaConfig, $data);
			}
		}

		$data = new \stdClass();
		$data->user = $accountId;
		$data->type = $type;
		$data->item = $itemName;

		$dbData = array_merge((Array) $data, (Array) $for, (Array) $meta);
		$id = self::$db->insert(self::$dbTable, $dbData);
		return $id;		
	}

	/**
	 * Extracts infos from LogConfig based on given data and updates values
	 * 
	 * @param  object $logConfig The Log Configuration as defined in LogConfig.php of consuming app
	 * @param  object $data      The data passed in to the api request
	 * @return object The updated, adapted logConfig
	 */
	private static function extractDataFromConfig($logConfig, $data) {
		foreach ($logConfig as $key => $value) {
			#echo "extracting Values. value=".$value."\n";
			if(is_array($value)) {
				// extract specified values from $data
				if(isset($data->{$value[0]}[$value[1]])) {
					$v = $data->{$value[0]}[$value[1]];
				} else {
					$v = null;
				}
				$logConfig->{$key} = $v;
			}
		}
		return $logConfig;		
	}

	/**
	 * Collects all data we get get with `getUserAgent()` and `getGEoInfoArray()` and the current request and puts it into one array to be saved to Log-Table
	 * @return array 
	 */
	protected static function collectData(): array {
		// get some infos bout browser, os, ....
		$ua = self::getUserAgent();
		// get and check ip-adress
		$ip  = $_SERVER['REMOTE_ADDR'];
		$ip4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : "";
		$ip6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ip : "";

		$data = Array(
			'user' => static::$user,
			'authType' => static::$type,
			'loginattempt' => static::$loginattempt,
			'success' => static::$success,
			'timestamp' => self::$db->now(),
			'referer' => '',
			'userAgent' => $ua['userAgent'],
			'userAgentOs' => $ua['platform'],
			'userAgentBrowser' => $ua['browser'] ." ". $ua['version'],
			'ip4' => $ip4,
			'ip6' => $ip6
		);

		$geoInfo = self::getGeoInfoArray();
		$data = array_merge($data, $geoInfo);
		return $data;
	}


	/**
	* Tries to get the userAgent of current user.
	*
	* @return  array    all the info we could get
	* 
	* ```
	* array(
	*		'userAgent' => $u_agent,
	*		'browser'   => $bname,
	*		'version'   => $version,
	*		'platform'  => $platform,
	*		'pattern'   => $pattern
	* );
	* ```
	*/
	public static function getUserAgent(): array {
		$u_agent = $_SERVER['HTTP_USER_AGENT'] ?? "not defined"; 
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'Windows';
			if (preg_match('/NT 6.2/i', $u_agent)) { $platform .= ' 8'; }
			elseif (preg_match('/NT 10.0/i', $u_agent)) { $platform .= ' 10'; }
			elseif (preg_match('/NT 6.3/i', $u_agent)) { $platform .= ' 8.1'; }
			elseif (preg_match('/NT 6.1/i', $u_agent)) { $platform .= ' 7'; }
			elseif (preg_match('/NT 6.0/i', $u_agent)) { $platform .= ' Vista'; }
			elseif (preg_match('/NT 5.1/i', $u_agent)) { $platform .= ' XP'; }
			elseif (preg_match('/NT 5.0/i', $u_agent)) { $platform .= ' 2000'; }
			#if (preg_match('/WOW64/i', $u_agent) || preg_match('/x64/i', $u_agent)) { $platform .= ' (x64)'; }
		}
		
		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
		{ 
			$bname = 'Internet Explorer'; 
			$ub = "MSIE"; 
		} 
		elseif(preg_match('/Firefox/i',$u_agent)) 
		{ 
			$bname = 'Mozilla Firefox'; 
			$ub = "Firefox"; 
		} 
		elseif(preg_match('/Chrome/i',$u_agent)) 
		{ 
			$bname = 'Google Chrome'; 
			$ub = "Chrome"; 
		} 
		elseif(preg_match('/Safari/i',$u_agent)) 
		{ 
			$bname = 'Apple Safari'; 
			$ub = "Safari"; 
		} 
		elseif(preg_match('/Opera/i',$u_agent)) 
		{ 
			$bname = 'Opera'; 
			$ub = "Opera"; 
		} 
		elseif(preg_match('/Netscape/i',$u_agent)) 
		{ 
			$bname = 'Netscape'; 
			$ub = "Netscape"; 
		} 
		else
		{
			$bname = 'unknown';
			$ub = "unknown";
		}
		
		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
		
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0] ?? "unknown";
			}
			else {
				$version= $matches['version'][1] ?? "unknown";
			}
		}
		else {
			$version= $matches['version'][0] ?? "unknown";
		}
		
		// check if we have a number
		if ($version==null || $version=="") {$version="?";}
		
		return array(
			'userAgent' => $u_agent,
			'browser'   => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);

	}


	/**
	* Tries to get some geological information based on the user's IP-Adress.
	* @return object of infos as returned by used ipinfo-api {@see ipinfo.io}}
	*/
	public static function getGeoInfo(): bool|object {
		$ip  = $_SERVER['REMOTE_ADDR'];
		// $ip = "213.168.109.88";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://ipinfo.io/".$ip."/json");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($ch);

		curl_close($ch);
		$obj = json_decode($json);
		if(is_object($obj)) return $obj;
		else return false;
	}

	/**
	* Uses `getGeoInfo` to get the geoInfo, then transfers that into an assoc array
	* 
	* @return array  of infos:
	* 
	* ```
	* [
	*      'long': '-14.12345',
	*      'lat': '+42.12345',
	*      'geoCity': 'Vienna',
	*      'geoRegion': 'Vienna',
	*      'geoCountry': 'Austria',
	*      'geoOrg': 'Organisation',
	*      'geoPostal': 1234
	* ]
	* ```
	* 
	*/
	public static function getGeoInfoArray(): array {
		$geoInfo = self::getGeoInfo();
		$g = Array();
		if($geoInfo) {
			if(isset($geoInfo->loc)) {
				$longlat = explode(",",$geoInfo->loc);
				$g['long'] = $longlat[0];
				$g['lat'] = $longlat[1];
			}
			$g['geoCity'] = isset($geoInfo->city) ? $geoInfo->city : "";
			$g['geoRegion'] =  isset($geoInfo->region) ? $geoInfo->region : "";
			$g['geoCountry'] =  isset($geoInfo->country) ? $geoInfo->country : "";
			$g['geoOrg'] =  isset($geoInfo->org) ? $geoInfo->org : "";
			$g['geoPostal'] =  isset($geoInfo->postal) ? $geoInfo->postal : "";
		}
		return $g;
	}

	/**
	 * Returns the name of the db-table
	 * @return string db-tableName of Log
	 */
	public function getDbTable() {
		return $this->dbTable;
	}
}