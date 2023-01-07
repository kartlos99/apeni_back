<?php

namespace Apeni\JWT;
use DataProvider;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

$sqlIdleInfo = "
SELECT `clientID`, TIMESTAMPDIFF(DAY,MAX(`saleDate`), LOCALTIMESTAMP) AS passedDays FROM `sales` s
LEFT JOIN customer c ON c.id = s.`clientID`
LEFT JOIN customer_to_region_map c_map ON c_map.customerID = c.id
WHERE c.active = 1 AND c_map.regionID = {$sessionData->regionID} AND c_map.active = 1
GROUP BY `clientID`
HAVING passedDays > (SELECT valueInt FROM dictionary_items WHERE code = 'customer_idle_warning')
";

$result = mysqli_query($con, $sqlIdleInfo);

if ($result) {
    $idleInfo = [];
    while ($rs = mysqli_fetch_assoc($result)) {
        $idleInfo[] = $rs;
    }
    $response[DATA] = $idleInfo;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get customer idle info!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);