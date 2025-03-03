<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');

$dbManager = new \BaseDbManagerV2();

$sqlIdleInfo = "
SELECT `clientID`, TIMESTAMPDIFF(DAY,MAX(`saleDate`), LOCALTIMESTAMP) AS passedDays FROM `sales` s
LEFT JOIN customer c ON c.id = s.`clientID`
LEFT JOIN customer_to_region_map c_map ON c_map.customerID = c.id
WHERE c_map.regionID = {$sessionData->regionID} AND c_map.active = 1
GROUP BY `clientID`

";
/**
 * folowing line was removed from query as warning display logic moved
 * to application (in front)
 * */
// HAVING passedDays > (SELECT valueInt FROM dictionary_items WHERE code = 'customer_idle_warning')

echo json_encode(
    $dbManager->getDataAsArray($sqlIdleInfo)
);

$dbManager->closeConnection();