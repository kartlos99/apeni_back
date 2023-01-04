<?php

namespace Apeni\JWT;

use DataProvider;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
$json = file_get_contents('php://input');
$postData = json_decode($json);

$code = $postData->code;
$value = $postData->valueInt;

$sqlUpdateSettings = "
UPDATE `dictionary_items` SET 
    `valueInt` = '$value',
    `modifyDate` = '$timeOnServer',
    `modifyUserID` = '$sessionData->userID'
WHERE `code` = '$code' ";

$result = mysqli_query($con, $sqlUpdateSettings);

if ($result) {
    $response[DATA] = mysqli_insert_id($con);
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't update parameter for setting $code !";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);