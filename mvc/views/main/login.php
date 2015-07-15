<? 
if(SES::is_set("Logged", "1")){
	include(ENGINE_PATH."mvc/views/auth/alreadyLogged.php"); 
} else {
	include(ENGINE_PATH."mvc/views/auth/formLogin.php"); 
}

?>