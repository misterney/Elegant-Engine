<?
class V{
	public static function chktype($var){
		if(is_array($var)) 		return "a";
		if(is_float($var)) 		return "f";
		if(is_int($var)) 		return "i";
		if(is_object($var))  	return "o";
		if(is_resource($var)) 	return "r";
		if(is_string($var)) 	return "s";
	}
	public static function tr($str, $char_mask = " \t\n\r\0\x0B"){
		return trim($str, $char_mask);
	}
}