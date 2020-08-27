<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

//$http_response_header
if (isset($_GET["date"])) {
    $date = $_GET["date"];
} else {
    $date = $dateOnServer;
}

$dataArr = [];

$chek = $_GET['chek'];

if ($chek == 0) {
// carieli kasrebi (obieqtebidan amogebuli da sawobidan gagzavnili sawarmoshi)
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
            DATE(`outputDate`) <= '$date'
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
            DATE(`outputDate`) <= '$date'
        GROUP BY
            canTypeID
    ) SIN
    ON
        SIN.canTypeID = k.id
    WHERE IFNULL(sout.count, 0) > 0 OR IFNULL(SIN.count, 0) > 0
    ORDER BY
        k.sortValue
        
        ";

    $arr = [];
    $result = mysqli_query($con, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $arr[] = $rs;
        }
        $dataArr['empty'] = $arr;
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = "can't compute Empty barrels Balance!";
        $response[ERROR_CODE] = 123;
        die(json_encode($response));
    }

// savse kasrebi (sawyobshi shemosuli da obieqtebze darigebuli)
    $sql = "
        SELECT `beerID`, `barrelID`, SUM(`inputToStore`) AS inputToStore, SUM(saleCount) AS saleCount 
    FROM (
        SELECT `beerID`, `canTypeID` AS barrelID , 0 AS inputToStore, SUM(`count`) AS saleCount FROM `sales` 
            WHERE DATE(`saleDate`) <= '$date'
        GROUP BY `beerID`, `canTypeID`
    
        UNION ALL
    
        SELECT `beerID`, `barrelID` , SUM(`count`) inputToStore, 0 AS saleCount FROM `storehousebeerinpit` 
            WHERE DATE(`inputDate`) <= '$date' and chek = '0'
        GROUP BY `beerID`, `barrelID`
    ) bf
        GROUP BY beerID, barrelID
        ORDER BY beerID, barrelID";

    $arr = [];
    $result = mysqli_query($con, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $arr[] = $rs;
        }
        $dataArr['full'] = $arr;
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = "can't compute Full store Balance!";
        $response[ERROR_CODE] = 123;
        die(json_encode($response));
    }
}

if ($chek == '1') {
    // savse kasrebi (sawyobshi shetanili da shekvetili)
    $dataArr['empty'] = null;

    $sql = "
    SELECT `beerID`, `barrelID`, SUM(`inputToStore`) AS inputToStore, SUM(saleCount) AS saleCount 
FROM (
    SELECT `beerID`, `canTypeID` AS barrelID, 0 AS inputToStore, sum(`count`) AS saleCount FROM `order_items` oi
    LEFT JOIN orders o ON o.ID = oi.orderID
    WHERE o.orderDate <= '$date' AND oi.`chek` = '1'
    GROUP BY `beerID`, `canTypeID`

    UNION ALL

    SELECT `beerID`, `barrelID` , SUM(`count`) inputToStore, 0 AS saleCount FROM `storehousebeerinpit` 
        WHERE DATE(`inputDate`) <= '$date' and chek = '1'
    GROUP BY `beerID`, `barrelID`
) bf
    GROUP BY beerID, barrelID
    ORDER BY beerID, barrelID";

    $arr = [];
    $result = mysqli_query($con, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $arr[] = $rs;
        }
        $dataArr['full'] = $arr;
    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = "can't compute Full checked store Balance!";
        $response[ERROR_CODE] = 123;
        die(json_encode($response));
    }
}


$response[DATA] = $dataArr;

echo json_encode($response);

mysqli_close($con);