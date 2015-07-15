<?
class Model_Auth extends Model {	
	
	function __construct(){
		parent::__construct();
		$this->libraries->load('session');
	}

	public function getData()
	{
		$this->data["html"]["title"] = "ElegantCMS";
	}

	public function login(){ 
		if(!isset($_POST["login"]) OR !isset($_POST["password"])){
			echo 'UNKNOWN ERROR';
			exit();
		}

		if($_POST["login"] == ""){
			echo 'NO_LOGIN';
			exit();
		}

		if($_POST["password"] == ""){
			echo 'NO_PASSWORD';
			exit();
		}

		$r = $this->database->execute(
			"SELECT * FROM `el_users` WHERE `login` = :login AND `password` = :password",
			["login" => $_POST["login"], "password" => md5($_POST["password"])]
		);

		if(!empty($r["id"])) {
			$user_group = $this->database->execute(
				"SELECT * FROM `el_groups` WHERE `id` = :id",
				["id" => $r["id"]]
			);

			$user_rules = $this->database->execute(
				"SELECT * FROM `el_groups` WHERE `id` = :id",
				["id" => $user_group["id"]]
			);


			$rules = "";

			SES::set('Logged', '1');
			SES::set('login', $_POST["login"]);
			SES::set('group', $r["group"]);
			SES::set('mail',  $r["mail"]);
			SES::set('rules', $rules);
			
			
			
			
			
			

			echo 'ALL_GOOD';
		} else { 
			SES::set('Logged', '0');
			echo 'NOT_VALID_LOGIN_OR_PASSWORD';
		}
		
	}
	public function logout(){
		SES::destroy();
		header('location: '.ENGINE_URL);
	}
	public function register(){
		if(!isset($_POST["login"]) OR !isset($_POST["pass"]) OR !isset($_POST["checkPass"]) OR !isset($_POST["mail"])){
			echo "ERROR";
			exit();
		}
		if(preg_match('/^[a-zA-Z0-9_-]{3,16}$/', $_POST["login"])){
			$login = $_POST["login"];
		} else {
			echo "ERROR";
			exit();
		}

		if(strlen($_POST["pass"]) <= 32 AND strlen($_POST["pass"]) >= 3){
			$pass = $_POST["pass"];
		} else {
			echo "ERROR";
			exit();
		}
		
		if(strlen($_POST["checkPass"]) <= 32 AND strlen($_POST["checkPass"]) >= 3){
			$checkPass = $_POST["checkPass"];
		} else {
			echo "ERROR";
			exit();
		}

		if($pass != $checkPass) {
			echo "ERROR";
			exit();
		}

		if(preg_match('/^([a-zA-Z0-9_\.-]+)@([a-zA-Z0-9_\.-]+)\.([a-zA-Z\.]{2,6})$/', $_POST["mail"])){
			$mail = strtolower($_POST["mail"]);
		} else {
			echo "ERROR";
			exit();
		}
		
		$user = $this->database->execute(
			"SELECT * FROM `el_users` WHERE `login` = :login OR `mail` = :mail",
			["login" => $login, "mail" => $mail]
		);

		if(isset($user["id"])){
			echo "ERROR";
			exit();
		} 


		$this->database->execute(
			"INSERT INTO `el_users` (`login`, `password`, `mail`) VALUES (:login, :password, :mail)",
			["login" => $login, "password" => md5($pass), "mail" => $mail]
		);

		echo "ALLGOOD";
	}
	public function info($type = "")
	{
		
		switch($_POST["type"]){
			case "checkLogin":
				
				$r = $this->database->execute(
						"SELECT * FROM `el_users` WHERE `login` = :login",
						["login" => $_POST["login"]]
					);
				if(isset($r["id"])) echo false;
				else echo true;	
				break;
			case "checkMail":
				
				$r = $this->database->execute(
						"SELECT * FROM `el_users` WHERE `mail` = :mail",
						["mail" => $_POST["mail"]]
					);
				if(isset($r["id"])) echo false;
				else echo true;	
				break;
		}
		
	}
} 