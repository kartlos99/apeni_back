<?php
namespace Apeni\JWT;
// ---------- get fasebi ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();

//$dges = $_GET["tarigi"];


$sql = "SELECT obj_id, beer_id, fasi FROM `fasebi` ORDER BY obj_id, beer_id";
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);
