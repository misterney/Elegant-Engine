<?
class AutoLoader{
	public static function boot(array $autoloadMap){
		foreach ($autoloadMap as $includeFile){
			if(file_exists(ENGINE_PATH_APPLICATION.$includeFile)){
				include_once(ENGINE_PATH_APPLICATION.$includeFile);
			}
		}
	}
	
}