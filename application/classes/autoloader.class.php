<?
if(!defined('SEAL_CHECK')) die('403 Foribbden... Please go to the <a href="../../">home page</a>');

function __autoload($class_name) {
	$filename = strtolower($class_name) . '.class.php';
	$file = ENGINE_PATH_CLASSES  . $filename;
	if (file_exists($file) == false) {
		$file = VENDOR_MODULES  .  $filename;
		if (file_exists($file) == false) {
			$file = VENDOR_LIBRARIES  .  $filename;
			if (file_exists($file) == false) {
				return false;
			} else include_once ($file);
		} else include_once ($file);
	} else include_once ($file);
}
