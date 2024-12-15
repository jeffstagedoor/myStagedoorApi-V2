<?php
namespace myStagedoor;
use myStagedoor\Log;
use myStagedoor\Log\Api as ApiLog;
use myStagedoor\Error\ErrorHandler;

class Api {
	/** @var \myStagedoor\API */
	static private ? Api $instance = null;

	/** 
	 * for development only - disables authorization. Set in Environment.
	 * @var boolean 
	 */
	private bool $NOAUTH = false;

	/** 
	 * a collection of special verbs that will be treated as a 'special request'.
	 * usually verbs like _'login', 'signup', 'task', 'search'_.      
	 *
	 * These are the ___verbs pre-defined__, though the param can be overriden:      
	 *	
	 * ```
	 * Array('dbupdate','meta', 'login', 'signup', 'signin', 'task', 
	 *       'sort', 'search', 'count', 'apiInfo', 'getFile', 'getImage', 
	 *       'getFolder', 'fileUpload', 'changePassword', 'changeName');
	 * ```
	 * 
	 * @var array 	
	 */
	private array $specialVerbs = array(
		'dbupdate', 'meta', 'login', 'signup', 'signin', 'task', 'sort', 'import', 'search', 'count', 'apiInfo',
		'getFile', 'getImage', 'getFolder',
		'fileUpload', 'changePassword', 'changeName'
	);

	/** @var array all the models found in this installation */
	private array $models;

	/** @var Request an Object that describes the current request */
	private Request $request;

	/** @var object all the data that has been sent with the request */
	private object $data;

	/** @var \MysqliDb the Database-Class */
	private \MysqliDb $db;

	// /** @var Jeff\Api\Log\Log The Logging class */
	// private $log;


	/**
	 * Call this method to get singleton
	 *
	 * @return Api
	 */
	public static function getInstance(): Api
	{
		if (null === self::$instance) {
			self::$instance = new Api();
		}
		return self::$instance;
	}


	/**
	 * disallow __construct
	 */
	private function __construct()
	{ }


	/**
	 * disallow __clone
	 */
	protected function __clone()
	{ }

	/**
	 * The startup
	 *
	 * Will _instanciate_ an
	 *
	 * - MysqliDb (and assign to this->db)
	 * - Account
	 *	
	* will connect to database (throws Errors if not successfull).
	* 
	* will init Log\Log
	*
	* will analyse the made $request, get needed $models, authorize the current user     
	* and will delegate to Api\Get, Api\Post, Api\Put, Api\Delete depending on the request made.
	*	
	*/
	public function start(\MysqliDb $db ): void
	{
		$this->db = $db;
		try {
			$this->db->connect();
			ApiLog::write('database connected', Log\Level::TRACE);
		} catch (\Exception $e) {
			$this->db = NULL;
			ErrorHandler::add(array("DB Error", "Could not connect to database", 500, true, ErrorHandler::CRITICAL_ALL));
			ErrorHandler::sendErrors();
			ErrorHandler::sendApiErrors();
			exit;
		}

		// get Request
		$this->request = new Request();

		echo Api\Info::getApiInfo();
	}
}