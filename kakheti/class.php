<?php 

if(!class_exists('Webop')){

	/**
	*  main class , web operation
	*/
	class Webop
	{
	
		function __construct()
		{
			# code...
		}

		function register($redirect){
			global $db_f;

			$current = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

			//$referrer = $_SERVER['HTTP_REFERER'];

            // registracia ar gvchirdeba
			//if( !empty( $_POST['username'] ) && !empty( $_POST['password'] )){
			if (false){	
				require_once('db.php');
				$link = mysqli_connect(HOST, DB_user, DB_pass, DB_name);
				$table = 'users';
				$fields = array('name', 'username', 'pass', 'email', 'regdate');

				$name = $_POST['name'];
				$username = $_POST['username'];
				$pass = $_POST['password'];
				$email = $_POST['email'];
				$regdate = $_POST['regdate'];

				$hashedPass = $db_f->hash_password($pass);

				$values = array($name, $username, $hashedPass, $email, $regdate );

				$chawera = $db_f->insert($link, $table, $fields, $values);

				if($chawera == true){
					$url = "http". ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
					
					$url = str_replace('register.php', $redirect, $url);

					header("Location: $url?action=regtrue");
					
				}else{
					die('ver xerxdeba registracia, chawera');
				}
					
			}
				
		}


		function login($redirect){
			global $db_f;
			
			$username = $_COOKIE['kk_user'];
			$authID = $_COOKIE['kk_authID'];

			if( !empty($username)){
			    
				$table = 'users';
				$link = mysqli_connect(HOST, DB_user, DB_pass, DB_name);
				$sql = "SELECT * FROM $table WHERE username = '".$username."'";
// echo('errori' . $username);
// print_r($_COOKIE);
				$results = $link->query($sql);

				if(!$results){
					die('aseti momxmarebeli ar arsebobs!');
				}

				$results = mysqli_fetch_assoc($results);

				$storpass = $results['pass'];

				$authnonce = md5('cookie-'.$username);
				$storpass = hash_hmac('sha512', $storpass, $authnonce);

                mysqli_close($link);
                
				if( $storpass == $authID ){
				    $url = "http". ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
// die(' URL ' . $url);					
					$url = str_replace('login.php', $redirect, $url);
	
					header("Location: $url");
				}
				
			} 
			
			if( !empty( $_POST['username'] ) && !empty( $_POST['password'] )){
				
				$subname = $_POST['username'];
				$subpass = $_POST['password'];

				$link = mysqli_connect(HOST, DB_user, DB_pass, DB_name);
				$table = 'users';

				$sql = "SELECT * FROM $table WHERE username = '".$subname."'";

				$results = $link->query($sql);
				if(!$results){
					die('aseti momxmarebeli ar arsebobs!');
				}
				mysqli_close($link);

				$results = mysqli_fetch_assoc($results);

				$storpass = $results['pass'];

				//$subpass = $db_f->hash_password($subpass);

				if($subpass == $storpass){

					$authnonce = md5('cookie-'.$subname);
					$authID = hash_hmac('sha512', $subpass, $authnonce);

					setcookie('kk_user', $subname, 0, '', '', '', true);
					setcookie('kk_authID', $authID, 0, '', '', '', true);

					$url = "http". ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
					
					$url = str_replace('login.php', $redirect, $url);
	
					header("Location: $url");
				} else {
					return 'invalid';
				}

			} else {
				return 'empty';
			}
			
			

		}


		function logout(){
			$user_out = setcookie('kk_user', '', -3600, '', '', '', true);
			$id_out = setcookie('kk_authID', '', -3600, '', '', '', true);
// print_r($_COOKIE);
// die(' logout '.$user_out );
			if( $user_out == true && $id_out == true){
				return true;
			} else {
				return false;
			}
		}

		function checklogin(){
			global $db_f;

			$username = $_COOKIE['kk_user'];
			$authID = $_COOKIE['kk_authID'];

			if( !empty($username)){
				$table = 'users';
				$link = mysqli_connect(HOST, DB_user, DB_pass, DB_name);
				$sql = "SELECT * FROM $table WHERE username = '".$username."'";

				$results = $link->query($sql);

				if(!$results){
					die('aseti momxmarebeli ar arsebobs!');
				}

				$results = mysqli_fetch_assoc($results);

				$storpass = $results['pass'];

				$authnonce = md5('cookie-'.$username);
				$storpass = hash_hmac('sha512', $storpass, $authnonce);

                mysqli_close($link);
                
				if( $storpass == $authID ){
					return true;
				} else {
					return false;
				}
				
			} else {
			 //   die(' check ret' . $username);
				$url = "http". ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME']."/kakheti/login.php";
				//$redirect = str_replace('index.php', 'login.php', $url);
				header("Location: $url?action=chek_ans_no");
				exit;
			}
		}

	}


}

// insantiate the class
$myop = new Webop;

?>