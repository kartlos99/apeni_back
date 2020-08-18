<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

$operationTime = $_GET['operationTime'];

$filterInput = $operationTime == "" ? "" : "WHERE inputDate = '$operationTime' ";
$filterOutput = $operationTime == "" ? "" : "WHERE outputDate = '$operationTime' ";

$sql =
    "SELECT `ID`, `inputDate` AS ioDate, `distributorID`, `beerID`, `barrelID`, `count`, `chek`, `comment`, `modifyDate`, `modifyUserID` " .
    "FROM `storehousebeerinpit` " . $filterInput .
    "UNION " .
    "SELECT `ID`, `outputDate` AS ioDate, `distributorID`, 0 AS `beerID`, `barrelID`, `count`, `chek`, `comment`, `modifyDate`, `modifyUserID` " .
    "FROM `storehousebarreloutput` " . $filterOutput .
    "ORDER BY ioDate DESC, beerID";


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