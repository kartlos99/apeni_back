<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

const TOTAL_COUNT_KEY = "totalCount";
const STATEMENTS_KEY = "statements";

$clientID = $_GET["clientID"];
$offset = $_GET["offset"] ?? 0;
$pageSize = 100;

$sql = "
SELECT
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS dt,
    pr,
    pay,
    (SELECT sum(pr-pay) FROM `extraction_sale_money` a
    WHERE
        a.tarigi <= b.tarigi
        AND
        clientID = $clientID AND a.`regionID` = {$sessionData->regionID}
        ) AS `bal`,
    id AS `recId`,
    recordType,
    details,
    comment
    FROM `extraction_sale_money` b
WHERE
    clientID = $clientID AND b.`regionID` = {$sessionData->regionID}
ORDER by b.tarigi DESC 
LIMIT $offset, $pageSize";

$totalPagesSql = "SELECT count(*) AS `totalCount` FROM `extraction_sale_money` b WHERE clientID = $clientID AND `regionID` = {$sessionData->regionID}";

$totalRowCount = $dbManager->getSingleValue($dbManager->getDataAsArray($totalPagesSql), TOTAL_COUNT_KEY, 0);
$items = $dbManager->getDataAsArray($sql);

$data = [
    TOTAL_COUNT_KEY => $totalRowCount,
    STATEMENTS_KEY => $items
];
//$data[TOTAL_COUNT_KEY] = $totalRowCount;
//$data[STATEMENTS_KEY] = $items;

echo json_encode($data);

$dbManager->closeConnection();