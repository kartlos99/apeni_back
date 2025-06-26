<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

const EMPTY_KEY = 'empty';
const FULL_KEY = 'full';
const FULL_BOTTLE_KEY = 'bottles';
const DATE_KEY = 'date';

global $dateOnServer;

$date = $_GET[DATE_KEY] ?? $dateOnServer;

$dataArr = [];

$chek = $_GET['chek'] ?? 0;

if ($chek == 0) {
    /** carieli kasrebi (obieqtebidan amogebuli da sawobidan gagzavnili sawarmoshi) */
    $sql = "

    SELECT
        k.id AS barrelID,
        IFNULL(sout.count, 0) AS outputEmptyFromStoreCount,
        IFNULL(SIN.count, 0) AS inputEmptyToStore
    FROM
        kasri k
    LEFT JOIN(
        SELECT
            `barrelID`,
            SUM(`count`) AS `count`
        FROM
            `storehousebarreloutput`
        WHERE
            DATE(`outputDate`) <= '$date' AND `regionID` = {$sessionData->regionID}
        GROUP BY
            `barrelID`
    ) sout
    ON
        k.id = sout.barrelID
    LEFT JOIN(
        SELECT
            `canTypeID`,
            SUM(`count`) AS `count`
        FROM
            `barrel_output`
        WHERE
            DATE(`outputDate`) <= '$date' AND `regionID` = {$sessionData->regionID}
        GROUP BY
            canTypeID
    ) SIN
    ON
        SIN.canTypeID = k.id
    WHERE IFNULL(sout.count, 0) > 0 OR IFNULL(SIN.count, 0) > 0
    ORDER BY
        k.sortValue
        
        ";

    $dataArr[EMPTY_KEY] = $dbManager->getDataAsArray($sql);

    /** savse kasrebi (sawyobshi shemosuli da obieqtebze darigebuli) */
    $sql = "
        SELECT `beerID`, `barrelID`, SUM(`inputToStore`) AS inputToStore, SUM(saleCount) AS saleCount 
    FROM (
        SELECT `beerID`, `canTypeID` AS barrelID , 0 AS inputToStore, SUM(`count`) AS saleCount FROM `sales` 
            WHERE DATE(`saleDate`) <= '$date' AND `regionID` = {$sessionData->regionID}
        GROUP BY `beerID`, `canTypeID`
    
        UNION ALL
    
        SELECT `beerID`, `barrelID` , SUM(`count`) inputToStore, 0 AS saleCount FROM `storehousebeerinpit` 
            WHERE DATE(`inputDate`) <= '$date' and chek = '0' AND `regionID` = {$sessionData->regionID}
        GROUP BY `beerID`, `barrelID`
    ) bf
        GROUP BY beerID, barrelID
        ORDER BY beerID, barrelID";

    $dataArr[FULL_KEY] = $dbManager->getDataAsArray($sql);
}

if ($chek == '1') {
    /** savse kasrebi (sawyobshi shetanili da shekvetili) */
    $dataArr[EMPTY_KEY] = null;

    $sql = "
    SELECT `beerID`, `barrelID`, SUM(`inputToStore`) AS inputToStore, SUM(saleCount) AS saleCount 
FROM (
    SELECT `beerID`, `canTypeID` AS barrelID, 0 AS inputToStore, sum(`count`) AS saleCount FROM `order_items` oi
    LEFT JOIN orders o ON o.ID = oi.orderID
    WHERE o.orderDate <= '$date' AND oi.`chek` = '1' AND o.`regionID` = {$sessionData->regionID}
    GROUP BY `beerID`, `canTypeID`

    UNION ALL

    SELECT `beerID`, `barrelID` , SUM(`count`) inputToStore, 0 AS saleCount FROM `storehousebeerinpit` 
        WHERE DATE(`inputDate`) <= '$date' and chek = '1' AND `regionID` = {$sessionData->regionID}
    GROUP BY `beerID`, `barrelID`
) bf
    GROUP BY beerID, barrelID
    ORDER BY beerID, barrelID";

    $dataArr[FULL_KEY] = $dbManager->getDataAsArray($sql);
}

/** calculating bottles i/o in storehouse */
$bottleBalanceSql = "
SELECT bottleID, SUM(`inputToStore`) AS inputToStore, SUM(saleCount) AS saleCount 
FROM (
    SELECT bottleID, SUM(`count`) AS inputToStore, 0 AS saleCount FROM storehouse_bottle_input
    WHERE DATE(`inputDate`) <= '$date' AND `regionID` = {$sessionData->regionID}
    GROUP BY bottleID
    
    UNION ALL
    
    SELECT bottleID, 0 AS inputToStore, SUM(`count`) AS saleCount FROM bottle_sales
    WHERE DATE(`saleDate`) <= '$date' AND `regionID` = {$sessionData->regionID}
    GROUP BY bottleID
    ) bb
GROUP BY bottleID  
";

$dataArr[FULL_BOTTLE_KEY] = $dbManager->getDataAsArray($bottleBalanceSql);

echo json_encode($dataArr);

$dbManager->closeConnection();
