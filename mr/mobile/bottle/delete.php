<?php

namespace Apeni\JWT;

use QueryHelper;
use DbKey;
use VersionControl;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
//$sessionData = checkToken();

$postData = json_decode(file_get_contents('php://input'));

$bottleID = $postData->bottleID;

if ($bottleID > 0) {

    $usageCheckSql = "
        SELECT id FROM `bottle_sales` 
        WHERE `bottleID` = $bottleID 
        UNION
        SELECT id FROM `order_items_bottle` 
        WHERE `bottleID` = $bottleID ";

    $usageResult = mysqli_query($con, $usageCheckSql);
    if (mysqli_num_rows($usageResult) > 0)
        $bottleStatus = BOTTLE_STATUS_DELETE_FROM_USAGE;
    else
        $bottleStatus = BOTTLE_STATUS_DELETE_FULL;

    $sql = "UPDATE `bottles` SET `status` = $bottleStatus WHERE `id`= $bottleID ";

    if (mysqli_query($con, $sql)) {
        if (mysqli_affected_rows($con) > 0) {
            $response[DATA] = $bottleID;
        } else {
            dieWithError(999, "no rows affected");
        }
    } else {
        dieWithError(mysqli_errno($con), "ERROR: Could not able to execute $sql" . mysqli_error($con));
    }

} else {
    dieWithError(999, "invalid bottle id");
}

//$vc = new VersionControl($con);
//$vc->updateVersionFor(BEER_VCS);
//$vc->updateVersionFor(PRICE_VCS);

echo json_encode($response);