<?php
namespace Apeni\JWT;
// ---------- get obieqts ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();

$sql = "SELECT * FROM $CUSTOMER_TB where `active`=1 order by dasaxeleba" ;
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);
