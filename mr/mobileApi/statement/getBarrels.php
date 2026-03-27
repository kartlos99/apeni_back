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
const FIRST_OPERATION_DATE_KEY = "firstOperationDate";
const ITEM_COUNT_KEY = "itemCount";
const OFFSET_KEY = "offset";

date_default_timezone_set('Asia/Tbilisi');
$clientID = $_GET["clientID"];
$offsetDate = $_GET["offset"] ?? date('Y-m-d H:i', time());
$daysInResponse = 30;
//die($offsetDate);
$sql = "
SELECT 
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS dt, 
    b.deliveredCount AS `countIn`, 
    b.returnedCount AS `countOut`,
    b.canType,
    
    ifnull((SELECT
        sum(a.deliveredCount - a.returnedCount) 
    FROM 
        `barrels_statement` a 
    WHERE 
        a.tarigi <= b.tarigi AND a.canType = 1  AND  a.clientID = $clientID AND a.`regionID` = {$sessionData->regionID}
        ), 0) AS `b50`,
    
    ifnull((SELECT
        sum(a.deliveredCount - a.returnedCount) 
    FROM 
        `barrels_statement` a 
    WHERE 
        a.tarigi <= b.tarigi AND a.canType = 2  AND  a.clientID = $clientID AND a.`regionID` = {$sessionData->regionID}
        ), 0) AS `b30`,
    
    ifnull((SELECT
        sum(a.deliveredCount - a.returnedCount) 
    FROM 
        `barrels_statement` a 
    WHERE 
        a.tarigi <= b.tarigi AND a.canType = 3  AND  a.clientID = $clientID AND a.`regionID` = {$sessionData->regionID}
        ), 0) AS `b20`,
    
    ifnull((SELECT
        sum(a.deliveredCount - a.returnedCount) 
    FROM 
        `barrels_statement` a 
    WHERE 
        a.tarigi <= b.tarigi AND a.canType = 4  AND  a.clientID = $clientID AND a.`regionID` = {$sessionData->regionID}
        ), 0) AS `b10`,

    b.recordId AS `recId`,
    b.comment
FROM 
     (SELECT DISTINCT DATE_FORMAT(d.tarigi, '%Y-%m-%d %H:%i') as tarigif FROM barrels_statement d
        WHERE d.clientID = $clientID AND d.`regionID` = {$sessionData->regionID}
            AND DATE_FORMAT(d.tarigi, '%Y-%m-%d %H:%i') < '$offsetDate'
        ORDER by d.tarigi DESC 
        LIMIT $daysInResponse
    ) ds LEFT JOIN `barrels_statement` b
ON ds.tarigif = DATE_FORMAT(b.tarigi, '%Y-%m-%d %H:%i')
WHERE 
    b.clientID = $clientID AND b.`regionID` = {$sessionData->regionID}
ORDER by b.tarigi DESC ";
//die($sql);
$totalPagesSql = "SELECT count(*) AS `totalCount` FROM `barrels_statement` b WHERE b.clientID = $clientID AND `regionID` = {$sessionData->regionID}";
$firstOpDateSql = "SELECT DATE_FORMAT(MIN(b.tarigi), '%Y-%m-%d %H:%i') AS firstOperationDate
    FROM `barrels_statement` b
    WHERE clientID = $clientID AND `regionID` = {$sessionData->regionID}";

$totalRowCount = $dbManager->getSingleValue($dbManager->getDataAsArray($totalPagesSql), TOTAL_COUNT_KEY, 0);
$firstOperationDate = $dbManager->getSingleValue($dbManager->getDataAsArray($firstOpDateSql), FIRST_OPERATION_DATE_KEY, '');
$items = $dbManager->getDataAsArray($sql);

echo json_encode([
    TOTAL_COUNT_KEY => $totalRowCount,
    FIRST_OPERATION_DATE_KEY => $firstOperationDate,
    ITEM_COUNT_KEY => count($items),
    OFFSET_KEY => $offsetDate,
    STATEMENTS_KEY => $items
]);

$dbManager->closeConnection();
