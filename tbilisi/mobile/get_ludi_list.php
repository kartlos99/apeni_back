<?php
namespace Apeni\JWT;
// ---------- get ludi list ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();

$sql = "SELECT * FROM ludi where `active`=1 order by id" ;
// $sql = "SELECT * FROM $CUSTOMER_TB where `active`=1 order by dasaxeleba" ;
$arr = array();
// $result = $con->query($sql);
$result = mysqli_query($con, $sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);