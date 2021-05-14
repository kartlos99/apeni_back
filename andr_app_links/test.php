<?php
// test -----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// echo "get_".$_GET["data"];
// echo "\nget_".$_GET["data1"];
// echo "\npost_".$_POST["data"];
// echo "\npost_".$_POST["data1"];

$act = $_GET["action"];
if( $act == 'logout'){
	echo $act;
}
	echo date("Y-m-d", time()+4*3600);
// $logged = $myop->login('index.php');

require_once('connection.php');
//header("Location: http://localhost/weblogin/login.php");

$myobj = "a";
    $sql = "select dasaxeleba from customer where id= 27";
    $res1 = $con->query($sql);
    while($r = mysqli_fetch_assoc($res1)){
    $myobj = $r["dasaxeleba"];
    }
   // echo $res1;
//echo array_map('mysql_real_escape_string', $res1);

//echo hash_hmac('sha256','2','kay');

$city = "fdlk88 77fd'l%ss'''''didu6^Zoi";

//$city = mysqli_real_escape_string($con, $city);

echo false;



// echo str_replace('p7hp', 'com', $_SERVER['REQUEST_URI']);

// $ww = $_GET["tarigi"];

$dro = date("Y-m-d H:i:s", time()+4*3600);

$sql = "
    SELECT * FROM $CUSTOMER_TB
    ";

$sql1 = "
SELECT DATE()
";

// date_default_timezone_set("Tbilisi/georgia");

// echo date("Y-m-d H:i:s", time()+4*3600);
// echo date("Y-m-d H:i:s", $dro);

// echo date("Y-m-d H:i:s", strtotime("yesterday"));

$result = $con->query($sql);

//echo false ? 'ki' : 'ara';
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
    //   echo implode(" * ",$rs);
    //   echo "\n";
    $id_arr[] = $rs['id'];
}

$sql = "    SELECT sum(ifnull(tanxa,0)) as money FROM `moneyoutput`    ";
    
    $result1 = $con->query($sql);
    $arr[] = mysqli_fetch_assoc($result1);
    
$dges_server = date("Y-m-d h:i:s", time()+4*3600);  
echo $dges_server;

mysqli_close($con);

?>