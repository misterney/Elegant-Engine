<?php
	session_start();
	ini_set('pcre.backtrack_limit', 1024*1024);


	define( 'DIRSEP',                   DIRECTORY_SEPARATOR);
	define( 'ENGINE_PATH', 				realpath(dirname(__FILE__)) . DIRSEP);
	define( 'ENGINE_URL',  				'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']));
	define( 'TEMP_FOLDER', 				ENGINE_PATH			  .  'temp'		  .  DIRSEP);

	define( 'ENGINE_PATH_APPLICATION', 	ENGINE_PATH	       		  .  'application'	      .  DIRSEP);
	define( 'ENGINE_PATH_VENDOR', 		ENGINE_PATH			      .  'vendor'		      .  DIRSEP);
	define( 'ENGINE_PATH_CLASSES', 		ENGINE_PATH_APPLICATION	  .  'classes'		      .  DIRSEP);
	define( 'ENGINE_PATH_SETTINGS', 	ENGINE_PATH_APPLICATION	  .  'settings'	 	      .  DIRSEP);
	define( 'ENGINE_MVC',				ENGINE_PATH_APPLICATION	  .  'engine-mvc'	      .  DIRSEP);
	define( 'ENGINE_MVC_M',				ENGINE_MVC			      .  'models'	          .  DIRSEP);
	define( 'ENGINE_MVC_V',				ENGINE_MVC			      .  'views'		      .  DIRSEP);
	define( 'ENGINE_MVC_C',				ENGINE_MVC			      .  'controllers'		  .  DIRSEP);

	define( 'ENGINE_ASSETS', 			ENGINE_PATH_APPLICATION	  .  'engine-assets'	  .  DIRSEP);
	define( 'ENGINE_LIBRARIES', 		ENGINE_PATH_APPLICATION	  .  'engine-libraries'	  .  DIRSEP);
	define( 'ENGINE_MODULES', 			ENGINE_PATH_APPLICATION	  .  'engine-modules'	  .  DIRSEP);

	define( 'VENDOR',                   ENGINE_PATH_VENDOR);
	define( 'VENDOR_ASSETS', 			VENDOR				      .  'assets'	 	      .  DIRSEP);
	define( 'VENDOR_LIBRARIES', 		VENDOR				      .  'libraries'	      .  DIRSEP);
	define( 'VENDOR_MODULES', 			VENDOR				      .  'modules'		      .  DIRSEP);
	define( 'VENDOR_MVC', 				VENDOR				      .  'mvc'		          .  DIRSEP);
	define( 'VENDOR_MVC_M', 			VENDOR_MVC			      .  'models'		      .  DIRSEP);
	define( 'VENDOR_MVC_V', 			VENDOR_MVC			      .  'views'		      .  DIRSEP);
	define( 'VENDOR_MVC_C', 			VENDOR_MVC			      .  'controllers'	      .  DIRSEP);
	
	define( 'URL_ASSETS',  				ENGINE_URL                .  DIRSEP		          .  'vendor' .  DIRSEP.'assets'. DIRSEP);
	define( 'URL_JS',  				    URL_ASSETS			      .  'javascripts'	      .  DIRSEP);
	define( 'URL_CSS',  				URL_ASSETS			      .  'css'		          .  DIRSEP);
	define( 'URL_IMAGES',  				URL_ASSETS			      .  'images'		      .  DIRSEP);

	define( 'URL_ENGINE_ASSETS',  		ENGINE_URL 			      .  DIRSEP		          .  'application'  .  DIRSEP 	  .  'engine-assets'	  .  DIRSEP);
	define( 'URL_ENGINE_JS',  			URL_ENGINE_ASSETS	      .  'javascripts'	      .  DIRSEP);
	define( 'URL_ENGINE_CSS',  			URL_ENGINE_ASSETS		  .  'css'		          .  DIRSEP);
	define( 'URL_ENGINE_IMAGES',  		URL_ENGINE_ASSETS		  .  'images'		         .  DIRSEP);


	define( 'SEAL_CHECK',  				TRUE);

	include_once(ENGINE_PATH_CLASSES  .  'autoloader.class.php');

	Debug::reporting(FALSE);
	$Route = new Route();
	Statistic::write("obtainInfoAboutUser");
	