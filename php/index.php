<?php
/**
 * This is the main entrance to this API, 
 * .htaccess shall redirect all calls to api.domain.com and domain.com/api to this file
 * 
 * 
 */

namespace myStagedoor;
use myStagedoor\Config\Environment;

include('autoloader.php');
include('Config.php');
	
// instatiate Database
require_once('vendor\joshcam\mysqli-database-class\MysqliDb.php');
$db = new \MysqliDb(Environment::$database);
// initialize Log and send db
Log::init($db);
$Api = Api::getInstance();
$Api->start($db);