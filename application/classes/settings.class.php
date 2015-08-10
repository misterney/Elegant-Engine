<?
if(!defined('SEAL_CHECK')) die('403 Foribbden... Please go to the <a href="../../">home page</a>');

class Settings {
	public static function load($name){
		if(file_exists(ENGINE_PATH_SETTINGS  .  $name  .  '.setting.php')){
			$config = include(ENGINE_PATH_SETTINGS  .  $name  .  '.setting.php');
			return $config;
		} else {
			Debug::log('Отстуствует файл конфигураций: ' . $name);
		}
	}
}