<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
//$sessionData = checkToken();
require_once('../../BaseDbManager.php');
require_once('../../common_data.php');

$dbManager = new \BaseDbManager();

$infoSeparator = '^';

$sql = "
SELECT cl.*, users.username AS modifyUsername,
CASE
    WHEN `tableName` = 'barrel_output' THEN concat_ws('$infoSeparator', date(bo.outputDate), (SELECT dasaxeleba from customer WHERE id = bo.clientID), bo.count, ifnull(bo.comment, ''))
    WHEN `tableName` = 'customer' THEN concat_ws('^', date(c.reg_date), c.dasaxeleba, ifnull(c.comment, ''))
    ELSE 'unknown operation'
END AS shortInfo
FROM `changeslog` cl
LEFT JOIN barrel_output bo ON cl.`editedRecordID` = bo.ID
LEFT JOIN customer c ON cl.`editedRecordID` = c.id

LEFT JOIN users ON users.id = cl.`modifyUserID`

";


$arr = $dbManager->getDataAsArray($sql);
foreach ($arr as $key => $item) {
    $shortInfoList = explode($infoSeparator, $item['shortInfo']);
    $shortInfoMap = [];
    if ($item['tableName'] == $BARREL_OUTPUT_TB) {
        $shortInfoMap["ოპ.თარიღი"] = $shortInfoList[0];
        $shortInfoMap["ობიექტი"] = $shortInfoList[1];
        $shortInfoMap["რაოდენობა"] = $shortInfoList[2];
        $shortInfoMap["კომენტარი"] = $shortInfoList[3];
    }
    if ($item['tableName'] == $CUSTOMER_TB) {
        $shortInfoMap["რეგისტრაცია"] = $shortInfoList[0];
        $shortInfoMap["ობიექტი"] = $shortInfoList[1];
        $shortInfoMap["კომენტარი"] = $shortInfoList[2];
    }
    $item['shortInfo'] = $shortInfoMap;
    $arr[$key] = $item;
}

$response[DATA] = $arr;

echo json_encode($response);

$dbManager->closeConnection();
