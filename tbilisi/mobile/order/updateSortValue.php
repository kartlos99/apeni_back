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


$setOrderValueSql = "
UPDATE `orders` SET `sortValue` = '$postData->newSortValue' 
WHERE `ID` = '$postData->OrderID'";

if (mysqli_query($con, $setOrderValueSql)) {
    $response[DATA] = '0';
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't Update order sort value";
    $response[ERROR_CODE] = ER_CODE_ORDER_SORTING;
}

echo json_encode($response);

mysqli_close($con);