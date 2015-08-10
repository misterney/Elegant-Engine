<?
class Statistic{
	function __construct(){
	}
	public static function write($type){
		$settings = Settings::load('router');
		$file = TEMP_FOLDER . 'statistic.txt';
		$col_zap = 999;
		switch($type){
		case "obtainInfoAboutUser":
		
			if (strstr($_SERVER['HTTP_USER_AGENT'], 'YandexBot')) {
				$bot='YandexBot';
			} //Выявляем поисковых ботов
			elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'Googlebot')) {
				$bot='Googlebot';
			} else { 
				$bot=$_SERVER['HTTP_USER_AGENT']; 
			}
			
			if (!empty($_SERVER['HTTP_CLIENT_IP'])){
				$ip=$_SERVER['HTTP_CLIENT_IP']; 
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR']; 
			} else { 
				$ip=$_SERVER['REMOTE_ADDR']; 
			
			}
			$date = date("H:i:s d.m.Y");
			
			$route = Route::getCurrentRoute();
			
			$route = implode('/', $route);
			if($route == "") {
				unset($route);
				$route[0] = $settings['route_default_controller'];
				$route[1] = $settings['route_default_action'];
				$route = implode('/', $route);
			}
			
			
			$lines = file($file);
			while(count($lines) > $col_zap) array_shift($lines);
			$lines[] = $date."|".$bot."|".$ip."|".$route."|".$type."|\r\n";

			file_put_contents($file, $lines);
			break;
		}

	}

}