<?php

namespace Apeni\JWT;

use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
$sessionData = checkToken();

$sqlAllCustomerForRegion =
    "SELECT
    c.*
FROM
    " . DbKey::$CUSTOMER_MAP_TB . " cm
    LEFT JOIN $CUSTOMER_TB c
    ON cm.customerID = c.id
WHERE
cm.regionID = {$sessionData->regionID} AND cm.active = 1 AND c.active = 1 
ORDER BY
    dasaxeleba ";
$arr = array();
$result = $con->query($sqlAllCustomerForRegion);

while ($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);
