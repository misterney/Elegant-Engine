<?
class SES {
	public static function set($key, $value){
		$_SESSION[$key] = $value;
	}
	public static function get($key) {
		if(isset($_SESSION[$key]))
		return $_SESSION[$key];
	}
	public static function destroy() {
		session_destroy();
	}
	public static function is_set($key, $value){
		if(!empty($_SESSION[$key])){
			if($_SESSION[$key] == $value) return true;
			else return false;
		} else return false;
	}
}