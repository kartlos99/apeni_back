<?php

namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

const TOTAL_COUNT_KEY = "totalCount";
const FIRST_OPERATION_DATE_KEY = "firstOperationDate";
const STATEMENTS_KEY = "statements";
const ITEM_COUNT_KEY = "itemCount";
const OFFSET_KEY = "offset";

date_default_timezone_set('Asia/Tbilisi');
$clientID = $_GET["clientID"];
$offsetDate = $_GET["offset"] ?? date('Y-m-d H:i', time());
//die($offsetDate);
$daysInResponse = 30;

$sql = "
SELECT
    DATE_FORMAT(b.tarigi, '%Y-%m-%d %H:%i') AS dt,
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
    FROM (SELECT DISTINCT DATE_FORMAT(d.tarigi, '%Y-%m-%d %H:%i') as tarigif FROM extraction_sale_money d
        WHERE d.clientID = $clientID AND d.`regionID` = {$sessionData->regionID}
            AND DATE_FORMAT(d.tarigi, '%Y-%m-%d %H:%i') < '$offsetDate'
        ORDER by d.tarigi DESC 
        LIMIT $daysInResponse
    ) ds LEFT JOIN `extraction_sale_money` b
ON ds.tarigif = DATE_FORMAT(b.tarigi, '%Y-%m-%d %H:%i')
WHERE
    clientID = $clientID AND b.`regionID` = {$sessionData->regionID}
ORDER by b.tarigi DESC 
";
//die($sql);
$totalPagesSql = "SELECT count(*) AS `totalCount` FROM `extraction_sale_money` b WHERE clientID = $clientID AND `regionID` = {$sessionData->regionID}";
$firstOpDateSql = "SELECT DATE_FORMAT(MIN(b.tarigi), '%Y-%m-%d %H:%i') AS firstOperationDate FROM `extraction_sale_money` b WHERE clientID = $clientID AND `regionID` = {$sessionData->regionID}";

$totalRowCount = $dbManager->getSingleValue($dbManager->getDataAsArray($totalPagesSql), TOTAL_COUNT_KEY, 0);
$firstOperationDate = $dbManager->getSingleValue($dbManager->getDataAsArray($firstOpDateSql), FIRST_OPERATION_DATE_KEY, '');
$items = $dbManager->getDataAsArray($sql);

$data = [
    TOTAL_COUNT_KEY => $totalRowCount,
    FIRST_OPERATION_DATE_KEY => $firstOperationDate,
    ITEM_COUNT_KEY => count($items),
    OFFSET_KEY => $offsetDate,
    STATEMENTS_KEY => $items
];
//$data[TOTAL_COUNT_KEY] = $totalRowCount;
//$data[STATEMENTS_KEY] = $items;

echo json_encode($data);

$dbManager->closeConnection();