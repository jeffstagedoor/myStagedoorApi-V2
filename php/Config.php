<?php
/**
*	Configuration file
*
*	This File describes the basic api configuration such as database credentials, folder structure,
*	debug switches, urls, ..
*
*	@author Jeff Frohner
*	@version 2.0.0
*/

use myStagedoor\Config\Environment;
use myStagedoor\Log;

Environment::init();

// add routes that don't need authentication
Environment::addNoAuthRoutes([
					// "task/acceptInvitation",
					"task/account2workgroupInvitationGetData",
					"task/account2workgroupInvitationResponse",
					]);

#echo $_SERVER['SERVER_NAME'];

switch ($_SERVER['SERVER_NAME']) {
	// production urls
	case 'www.mystagedoor.de':
	case 'www.mystagedoor.at':
	case 'www.mystagedoor.eu':
	case 'mystagedoor.de':
	case 'mystagedoor.at':
	case 'mystagedoor.eu':		
		Environment::$urls->baseUrl = "https://www.mystagedoor.de";
		Environment::$database = Array( 
					"username" => "d03709d0",
					"password" => "5ZChSzXofz6tDsGV", 
					"host" => "dd48732",  
					"db" => "d03709d0" 
		);
		Environment::$urls->appUrl = "https://www.mystagedoor.de/";
		Environment::$api->allowOrigin = "https://www.mystagedoor.de";
		Environment::$api->possibleOrigins = ["https://www.mystagedoor.de", "https://mystagedoor.de", "https://api.mystagedoor.de"];
		Environment::$production = true;
		Environment::$development = false;

		break;


	// development urls
	case 'localhost':
	case 'mystagedoorv2.local':
	case '127.0.0.1':
		Environment::$production = false;
		Environment::$development = true;
		Environment::$debug = true;

		Environment::$database = Array( 
					"username" => "root",
					"password" => "", 
					"host" => "localhost",  
					"db" => "mystagedoor" 
		);

		Environment::$urls->baseUrl = "http://mystagedoorv2.local/php";
		Environment::$urls->appUrl = "http://localhost:4200/";
		
		Environment::$api->noAuth = false;
		Environment::$api->allowOrigin = "http://localhost:4200";  // should only be there in develepment
		Environment::$api->possibleOrigins = ["http://localhost:4200"];
		Environment::$logLevels = [Log\Level::CRITICAL, Log\Level::ERROR,Log\Level::NOTICE ,Log\LEVEL::INFO /*,Log\Level::DEBUG,Log\Level::TRACE*/];
		break;
}

