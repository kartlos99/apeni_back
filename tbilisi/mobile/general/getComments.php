<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

$timeForOrder = date("Y-m-d", time()+4*3600-24*3600);
$timeForMitana = date("Y-m-d", time()+4*3600-3*24*3600);

$sql = "
SELECT a.comment, op, MAX(modifyDate) AS commentDate, ob.dasaxeleba FROM 
(
SELECT `clientID`, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'S' as op FROM `sales` s
WHERE s.`comment` <> '' AND date(s.`modifyDate`) >= '$timeForMitana'
UNION
SELECT `clientID`, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'O' as op FROM `orders` o
WHERE o.`comment` <> '' AND date(o.`modifyDate`) >= '$timeForOrder'
UNION
SELECT `obieqtis_id` AS clientID, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'M' as op FROM `moneyoutput` m
WHERE m.`comment` <> '' AND date(m.`modifyDate`) >= '$timeForMitana'
UNION
SELECT clientID, ifnull(`comment`, '') AS comment, `modifyDate`, `modifyUserID`, 'B' as op FROM `barrel_output` b
WHERE b.`comment` <> '' AND date(b.`modifyDate`) >= '$timeForMitana'
    ) a
    LEFT JOIN obieqtebi ob ON a.clientID = ob.id
    
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