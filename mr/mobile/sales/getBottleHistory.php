<?php
namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$saleID = $_GET["saleID"];

$historyQuery = "
SELECT `hID`, `ID`, `regionID`, `saleDate`, `clientID`, `distributorID`, `bottleID`, `price`, `count`, `orderID`, `comment`, `modifyDate`, `modifyUserID`, `disrupterUserID` 
FROM `bottle_sales_history` 
WHERE ID = $saleID

UNION ALL

SELECT 0, `ID`, `regionID`, `saleDate`, `clientID`, `distributorID`, `bottleID`, `price`, `count`, `orderID`, `comment`, `modifyDate`, `modifyUserID`, 0 
FROM `bottle_sales`
WHERE ID = $saleID ";

$dataArr = [];
$result = mysqli_query($con, $historyQuery);
while ($rs = mysqli_fetch_assoc($result)) {
    $dataArr[] = $rs;
}

$response[DATA] = $dataArr;

echo json_encode($response);

mysqli_close($con);