<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();
$clientID = 0;
if (isset( $_GET["clientID"]))
    $clientID = $_GET["clientID"];
else dieWithError(COMMON_ERROR_CODE, "client is Not set!");

$sql =
    "SELECT
    r.ID,
    r.name,
    0 as `ownStorage`,
    IFNULL(cMap.active, 0) AS attached
FROM
    `regions` r
LEFT JOIN(
    SELECT * FROM
        `customer_to_region_map`
    WHERE
        customer_to_region_map.active = 1 AND customer_to_region_map.customerID = $clientID
) cMap
ON
    cMap.`regionID` = r.ID";

$arr = array();
$result = $con->query($sql);

while ($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);