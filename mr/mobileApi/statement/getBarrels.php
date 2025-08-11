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
    comment
    FROM `barrels_statement` b
WHERE 
    b.clientID = $clientID AND b.`regionID` = {$sessionData->regionID}
ORDER by b.tarigi DESC
LIMIT $offset, $pageSize ";

$totalPagesSql = "SELECT count(*) AS `totalCount` FROM `amonaweri_barrel` b WHERE obieqtis_id = $clientID AND `regionID` = {$sessionData->regionID}";

$totalRowCount = $dbManager->getSingleValue($dbManager->getDataAsArray($totalPagesSql), TOTAL_COUNT_KEY, 0);
$items = $dbManager->getDataAsArray($sql);

echo json_encode([
    TOTAL_COUNT_KEY => $totalRowCount,
    STATEMENTS_KEY => $items
]);

$dbManager->closeConnection();
