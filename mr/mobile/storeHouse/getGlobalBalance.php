<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

//$http_response_header
if (isset($_GET["date"])) {
    $date = $_GET["date"];
} else {
    $date = $dateOnServer;
}


$sqlGlobalBalance = "
SELECT k.id, k.dasaxeleba AS barrelName, k.initialAmount,
	ifnull(bout.val, 0) +
    ifnull(sbout.val, 0) AS globalIncome,
    ifnull(sal.val, 0) +
    ifnull(shinput.val, 0) AS globalOutput
FROM `kasri` k
LEFT JOIN (
    SELECT `canTypeID`, SUM(`count`) AS val FROM `barrel_output` brl
    LEFT JOIN regions r ON r.ID = brl.regionID
    WHERE r.ownStorage = 0 AND DATE(`outputDate`) <= '$date'
    GROUP BY `canTypeID`
) bout ON k.id = bout.canTypeID

LEFT JOIN (
    SELECT `barrelID`, SUM(`count`) AS val FROM `storehousebarreloutput` s
    LEFT JOIN regions r ON r.ID = s.regionID
    WHERE r.ownStorage = 1 AND DATE(`outputDate`) <= '$date'
    GROUP BY `barrelID`
) sbout ON k.id = sbout.barrelID

LEFT JOIN (
    SELECT `canTypeID`, SUM(`count`) AS val FROM `sales` s
    LEFT JOIN regions r ON r.ID = s.regionID
    WHERE r.ownStorage = 0 AND DATE(`saleDate`) <= '$date'
    GROUP BY `canTypeID`
) sal ON k.id = sal.canTypeID

LEFT JOIN (
    SELECT `barrelID`, SUM(`count`) AS val FROM `storehousebeerinpit` s
    LEFT JOIN regions r ON r.ID = s.regionID
    WHERE r.ownStorage = 1 AND `chek`=0 AND DATE(`inputDate`) <= '$date'
    GROUP BY `barrelID`
) shinput ON k.id = shinput.barrelID";

$arr = [];
$result = mysqli_query($con, $sqlGlobalBalance);
if ($result) {
    while ($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
    $response[DATA] = $arr;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't calculate Global Barrel Balance!";
    $response[ERROR_CODE] = 129;
    die(json_encode($response));
}

echo json_encode($response);

mysqli_close($con);