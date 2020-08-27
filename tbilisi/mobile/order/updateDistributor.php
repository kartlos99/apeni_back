<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);


$setOrderValueSql =
    "UPDATE `orders` SET " .
    "`distributorID` = '$postData->distributorID', " .
    "`modifyDate` = '$timeOnServer', " .
    "`modifyUserID` = $postData->modifyUserID " .
    " WHERE `ID` = '$postData->orderID'";

if (mysqli_query($con, $setOrderValueSql)) {
    $response[DATA] = '0';
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't Update order distributorID";
    $response[ERROR_CODE] = ER_CODE_ORDER_UPD_DISTRIBUTOR;
}

echo json_encode($response);

mysqli_close($con);