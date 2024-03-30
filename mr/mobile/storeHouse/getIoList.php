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
    "SELECT `ID`, `groupID`, `inputDate` AS ioDate, `distributorID`, `beerID`, `barrelID`, `count`, `chek`, `comment` " .
    "FROM `storehousebeerinpit` " . $filterInput .
    "UNION " .
    "SELECT `ID`, `groupID`, `outputDate` AS ioDate, `distributorID`, 0 AS `beerID`, `barrelID`, `count`, `chek`, `comment` " .
    "FROM `storehousebarreloutput` " . $filterOutput .
    "ORDER BY ioDate DESC, beerID
    limit 50 ";

$sqlBottleInputs = "SELECT
                        `id`,
                        `regionID`,
                        `groupID`,
                        `inputDate`,
                        `distributorID`,
                        `bottleID`,
                        `count`,
                        `chek`,
                        `comment`
                    FROM
                        `storehouse_bottle_input` " . $filterInput;

$arr = [];
$result = mysqli_query($con, $sql);
if ($result) {
    while ($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
    $bottlesResult = mysqli_query($con, $sqlBottleInputs);
    $bArr = [];
    while ($rs = mysqli_fetch_assoc($bottlesResult)) {
        $bArr[] = $rs;
    }
    $response[DATA] = [
        'barrels' => $arr,
        'bottles' => $bArr
    ];
} else {
    dieWithError(
        mysqli_errno($con),
        mysqli_error($con)
    );
}

echo json_encode($response);

mysqli_close($con);