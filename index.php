<?php
	session_start();

	ini_set('pcre.backtrack_limit', 1024*1024);

	$url = dirname($_SERVER['PHP_SELF']); if($url == '\\' | $url == '/') $url = ''; else $url = $url.'/';

	define('ENGINE_PATH', 			realpath(dirname(__FILE__)) . '/');
	define('ENGINE_URL',  			'http://' . $_SERVER['SERVER_NAME'] . $url);
	define('ENGINE_PATH_APPLICATION', 	ENGINE_PATH.'application/');
	define('ENGINE_PATH_SYS_MODULES', 	ENGINE_PATH_APPLICATION.'sys-modules/');
	define('ENGINE_PATH_RES_MODULES',	ENGINE_PATH_APPLICATION.'res-modules/');
	define('ENGINE_PATH_CLASSES', 		ENGINE_PATH_APPLICATION.'classes/');
	define('ENGINE_PATH_MVC', 		ENGINE_PATH.'mvc/');
	define('ENGINE_PATH_CONFIGS',  		ENGINE_PATH_APPLICATION.'configs/');
	define('ENGINE_PATH_LIBRARIES', 	ENGINE_PATH_APPLICATION.'libraries/');
	define('SEAL_CHECK',  			TRUE);
	define('URL_ASSETS',  			ENGINE_URL.'/assets/');
	define('URL_JS',  			ENGINE_URL.'/assets/js/');
	define('URL_CSS',  			ENGINE_URL.'/assets/css/');
	define('URL_IMAGES',  			ENGINE_URL.'/assets/images/');

	include_once(ENGINE_PATH_APPLICATION.'/classes/autoloader.class.php');

	$autoloadMap = [
		'classes/debug.class.php',
		'classes/_controller.class.php',
		'classes/_model.class.php',
		'classes/_view.class.php',
		'classes/settings.class.php',
		'classes/libraries.class.php',
		'classes/database.class.php',
		'classes/route.php',
		'sys-modules/database_pdo.php',
	];
	
	AutoLoader::boot($autoloadMap, $constantMap);
 	Debug::chrono();

	//Функция отладки: Включение: E_ALL. Отключение: false
	Debug::reporting(false);

	$Route = new Route();
	$Route->Run();

	Debug::chrono('Время обработки движка');
