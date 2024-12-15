<?php

namespace myStagedoor;
use myStagedoor\Request\Method;
use myStagedoor\Config\Environment;

class Request {
	private Request\Type $type;
	private Request\Method $method;
	private array|null $params;
	private object $data;
	
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->method = $this->findMethod();
		$this->data = $this->findData();
		$this->params = $this->findParams();
		$this->type = $this->findType();
	}
	
	/**
	 * findType
	 *
	 * @return Request\Type
	 */
	private function findType(): Request\Type {	
		if (!is_array($this->params) || $this->params[0] === '' || strtolower($this->params[0]) === 'apiInfo') {
			return Request\Type::INFO;
		}
		// check for comment2post type 'references'
		$references = explode("2", $this->params[0]);
		if (count($references) === 2) {
			$this->references = $references;
			return Request\Type::REFERENCE;
		}

		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'sort')) {
			return Request\Type::SORT;
		}
		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'search')) {
			return Request\Type::SEARCH;
		}
		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'getimage')) {
			return Request\Type::IMAGE;
		}
		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'task')) {
			return Request\Type::TASK;
		}
		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'file')) {
			return Request\Type::FILE;
		}
		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'folder')) {
			return Request\Type::FOLDER;
		}
		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'login')) {
			return Request\Type::LOGIN;
		}
		if ((isset($this->params[0]) && strtolower($this->params[0]) === 'signup')) {
			return Request\Type::SIGNUP;
		}
		if ((isset($this->params[1]) && $this->params[1] === 'multiple') || isset($this->data->ids)) {
			return Request\Type::COALESCE;
		}


		$Api = Api::getInstance();
		if (in_array($this->params[0], Environment::$specialVerbs)) {
			return Request\Type::SPECIAL;
		}

		if (isset($this->data->filter) || isset($this->data->gt) || isset($this->data->gte) || isset($this->data->lt) || isset($this->data->lte)) {
			return Request\Type::QUERY;
		}
		// default
		return Request\Type::BASIC;
	}	
	/**
	 * findMethod
	 *
	 * @return Method
	 */
	private function findMethod(): Method {	
		switch($_SERVER['REQUEST_METHOD']) {
			case 'GET':
				return Method::GET;
			case 'HEAD':
				return Method::HEAD;
			case 'POST':
				return Method::POST;
			case 'PUT':
				return Method::PUT;
			case 'DELETE':
				return Method::DELETE;
			case 'CONNECT':
				return Method::CONNECT;
			case 'OPTIONS':
				return Method::OPTIONS;
			case 'TRACE':
				return Method::TRACE;
			case 'PATCH':
				return Method::PATCH;
			default:
				return Method::GET;
	}

	}

	/**
	 *	tries to fetch data where ever it might be posted/put/...
	 *	@return object the posted data as stdClass
	 */
	private function findData():object
	{

		// check where the data came to (and if at all):
		$fgc = file_get_contents("php://input");

		// test if sent body is a json (that's the usual case for POST, DELETE requests)
		$json = json_decode($fgc, true);
		if ($json) {
			$inputData = (object) $json;
			if (isset($inputData) && count(get_object_vars($inputData)) > 0) {
				$data = $inputData;
				return $data;
			}
		} else {
			// check for PUT (or 'application/x-www-form-urlencoded')
			parse_str($fgc, $putData);
			if ($putData && count($putData) > 0) {
				$data = (object) $putData;
				return $data;
			}
		}
		$postObject = (object) $_POST;
		if (isset($postObject) && count(get_object_vars($postObject)) > 0) {
			$data = $postObject;
		}

		// check for get-parameters
		if (!isset($data)) {
			$data = (object) $_GET;
			unset($data->request);
		}
		// if nothing found anywhere make an empty object
		if (!isset($data)) {
			$data = new \stdClass();
		}
		return $data;
	}


	/**
	 *
	 * @return array containing the request-parameters 
	 *               When calling the api with http://example.com/api/modelName/5
	 *               this will contain everything after 'api' split by '/'
	 *               -> ['modelName', '5']
	 */
	public function findParams():array|bool|null
	{
		return isset($_GET['request']) ? explode("/", $_GET['request']) : null;
	}

	public function getMethod() :Request\Method	 {
		return $this->method;
	}
	public function getType() :Request\Type	 {
		return $this->type;
	}

}