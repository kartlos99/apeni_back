<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

$groupID = $_GET['groupID'];

$filterInput = "WHERE `regionID` = {$sessionData->regionID} ";
$filterOutput = "WHERE `regionID` = {$sessionData->regionID} ";
if ($groupID != "") {
    $filterInput .= " AND groupID = '$groupID' ";
    $filterOutput .= " AND groupID = '$groupID' ";
}

$sql =
    "SELECT `ID`, `groupID`, `inputDate` AS ioDate, `distributorID`, `beerID`, `barrelID`, `count`, `chek`, `comment`, `modifyDate`, `modifyUserID` " .
    "FROM `storehousebeerinpit` " . $filterInput .
    "UNION " .
    "SELECT `ID`, `groupID`, `outputDate` AS ioDate, `distributorID`, 0 AS `beerID`, `barrelID`, `count`, `chek`, `comment`, `modifyDate`, `modifyUserID` " .
    "FROM `storehousebarreloutput` " . $filterOutput .
    "ORDER BY ioDate DESC, beerID
    limit 50 ";


$arr = [];
$result = mysqli_query($con, $sql);
if ($result) {
    while ($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
    $response[DATA] = $arr;
} else {
    dieWithError(
        mysqli_errno($con),
        mysqli_error($con)
    );
}

echo json_encode($response);

mysqli_close($con);