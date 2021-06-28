<?php
namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$recordID = $_GET["recordID"];

$historyQuery = "
SELECT `hID`, `ID`, `tarigi`, `obieqtis_id`, `distributor_id`, `tanxa`, `paymentType`, `comment`, `modifyDate`, `modifyUserID`, `disrupterUserID` 
FROM `money_history` 
WHERE `ID` = $recordID
UNION ALL
SELECT 0, `ID`, `tarigi`, `obieqtis_id`, `distributor_id`, `tanxa`, `paymentType`, `comment`, `modifyDate`, `modifyUserID`, 0 
FROM `moneyoutput` 
WHERE `ID` = $recordID ";

$dataArr = [];
$result = mysqli_query($con, $historyQuery);
while ($rs = mysqli_fetch_assoc($result)) {
    $dataArr[] = $rs;
}

$response[DATA] = $dataArr;

echo json_encode($response);

mysqli_close($con);