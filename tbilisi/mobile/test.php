<?php

// ---------- chanaweris washla (3 in 1) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

function getBalanceMap($dbConn, $clientID = 0)
{

    $sqlQuery = "CALL getBarrelBalanceByID($clientID)";
    $arr = [];
    $mMap = [];
    $result = mysqli_query($dbConn, $sqlQuery);
    while ($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
        $mMap[$rs['canTypeID']] = $rs['balance'];
    }
//return $arr;
    global $rt;
    $rt = 2;
    $nArr = array_filter($arr, function ($obj) {
        echo $obj['balance'] . " ";
        if ($obj['canTypeID'] == $rt) return true;
        return false;
    });


    return $nArr;
}

echo json_encode(count(getBalanceMap($con,73)));
die();
die(print_r($arr));

//$currTime = date("Y-m-d H:i:s", time()+4*3600);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json);

$id = $data->recordID * 2;
$table = $data->table;
$userid = $data->userid;

$responce['id'] = $id;
$responce['from_POST'] = $_POST['nin'];
$responce['myself'] = $data->me;

echo json_encode($responce);

die();
// 2020-04-19



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
    $sql = "select dasaxeleba from $CUSTOMER_TB where id = 27";
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