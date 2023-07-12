<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

const COMMENT_DURATION_DAYS = 7;

$timeForOrder = date("Y-m-d", time() + HOUR_DIFF_ON_SERVER * 3600 - 24 * 3600);
$fromDate = date("Y-m-d", time() + HOUR_DIFF_ON_SERVER * 3600 - COMMENT_DURATION_DAYS * 24 * 3600);

$sql = "
SELECT a.comment, op, MAX(modifyDate) AS commentDate, ifnull(ob.dasaxeleba, '') AS dasaxeleba, u.username FROM 
(
SELECT `clientID`, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'S' as op FROM `sales` s
WHERE s.`comment` <> '' AND date(s.`modifyDate`) >= '$fromDate' AND `regionID` = {$sessionData->regionID}
UNION
SELECT `clientID`, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'O' as op FROM `orders` o
WHERE o.`comment` <> '' AND date(o.`modifyDate`) >= '$timeForOrder' AND `regionID` = {$sessionData->regionID}
UNION
SELECT `obieqtis_id` AS clientID, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'M' as op FROM `moneyoutput` m
WHERE m.`comment` <> '' AND date(m.`modifyDate`) >= '$fromDate' AND `regionID` = {$sessionData->regionID}
UNION
SELECT clientID, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'B' as op FROM `barrel_output` b
WHERE b.`comment` <> '' AND date(b.`modifyDate`) >= '$fromDate' AND `regionID` = {$sessionData->regionID}
UNION
SELECT 0 AS clientID, `comment`, `modifyDate`, `modifyUserID`, 'E' as op FROM `comments` c
WHERE c.`comment` <> '' AND date(c.`modifyDate`) >= '$fromDate' AND `regionID` = {$sessionData->regionID}
    
    ) a
    LEFT JOIN $CUSTOMER_TB ob ON a.clientID = ob.id
    LEFT JOIN users u ON a.modifyUserID = u.id
    
GROUP BY a.clientID, a.comment
ORDER BY a.modifyDate DESC
";

$result = mysqli_query($con, $sql);

$arr = [];
if ($result) {
    while ($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);