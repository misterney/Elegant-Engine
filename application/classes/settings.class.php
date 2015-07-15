<?
class Settings {
	public static function load(string $name){
		$config = include(ENGINE_PATH_CONFIGS.$name.'.config.php');
		return $config;
	}
} 