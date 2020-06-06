<?php
//include_once('imports.php');
require_once('/xampp/htdocs/apeni.localhost.com/imports.php');
if(!class_exists('Dbfunc')){
	/**
	* bazastan saurtuertobo klasi
	*/
	class Dbfunc
	{
		function __construct(){
			$this->connect();
			# code...
		}

		function connect(){

			$link = mysqli_connect(HOST, DB_user, DB_pass, DB_name);
			if(!$link){
				die('canNot connect db'.mysql_error());
			}

			// $db_selected = mysql_select_db(DB_name, $link);

			// if(!$db_selected){
			// 	die('can\'t select db : '. mysql_error());
			// }
		}

		function clean($array){

			// es monacemebi unda gavasuftaot arasasurveli simboloebisagan
			// jerjerobit igives vabrunebt
			return $array;
		}

		function hash_password($password){
			$secureHash = hash_hmac('sha512', $password, KAY);

			return $secureHash;
		}

		function insert($linki, $table, $fields, $values){
			$fields = implode(", ", $fields);
			$values = implode("', '", $values);
			$sql = "insert into $table (id, $fields) values ('', '$values')";

			$result = $linki->query($sql);

			if ($result == TRUE){
				return TRUE;
			}else{
				die('Error: INSINS '.mysqli_error($linki));
			}
		}

		function select($sql){
			$results = mysqli_query($sql);
			return $results;
		}

		function delta(){
			return "http://localhost/weblogin/login.php";
		}
	}
}

// insantiate the class
$db_f = new Dbfunc;

?>