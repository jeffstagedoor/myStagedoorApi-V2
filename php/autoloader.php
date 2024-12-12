<?php
spl_autoload_register(function ($class) {
	include /*__NAMESPACE__ . DIRECTORY_SEPERATOR . */ $class . '.php';
});