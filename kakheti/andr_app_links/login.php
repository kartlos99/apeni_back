<?php
// ------------------ login ---------------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$username 	= $_POST["username"];
$password 	= $_POST["password"];

$sql = "SELECT id, username, name, type FROM `users` 
        WHERE 
            `active` = 1 AND
            `username` = '$username' AND 
            `pass` = '$password'" ;
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

// $sql = "SELECT name FROM users where id=1 " ;
// $result = $link->query($sql);
    
// echo $result;    


// if ($arr == null){
// 	echo "sast";
// }else{
// 	echo $arr['data']['pass'];
// }

// $str = $arr['data']['id'];

// echo $str;

//echo "ქართული ";

// $myname = $arr['data'];

// echo $myname;

if(count($arr)==1){
    echo json_encode($arr);
}else{
    echo "uaryofa";
}



mysqli_close($con);

?>