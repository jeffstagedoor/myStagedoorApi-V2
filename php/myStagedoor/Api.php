<?php
namespace myStagedoor;

class Api {
	/** @var instance of myStagedoor\API */
	static private $instance = null;

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

	/** @var object an Object that describes the current request */
	private $request;

	/** @var object all the data that has been sent with the request */
	private $data;

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
	 * will analyse the made $request, get needed $models, authorize the current user     
	 * and will delegate to ApiGet, ApiPost, ApiPut, ApiDelete depending on the request made.
	 *	
	 */
	public function start(): void
	{
		echo "starting API";

	}
}