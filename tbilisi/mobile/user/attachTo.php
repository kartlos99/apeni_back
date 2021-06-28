<?php

namespace Apeni\JWT;

use DbKey;
use VersionControl;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');
$postData = json_decode($json);

if ($sessionData->userType != USERTYPE_ADMIN)
    dieWithError(ER_CODE_NO_PERMISSION, ER_TEXT_NO_PERMISSION);

$sqlDeleteExistingMapping = sprintf("DELETE FROM %s WHERE `userID` = %s", DbKey::$USER_MAP_TB, $postData->userID);

mysqli_query($con, $sqlDeleteExistingMapping);

$multiValue = "";
for ($i = 0; $i < count($postData->regionIDs); $i++) {
    $regionID = $postData->regionIDs[$i];
    if ($i > 0)
        $multiValue .= ",";
    $multiValue .= "('$postData->userID', '$regionID')";
}

$sqlInsertCustomerMap =
    sprintf("INSERT INTO %s (`userID`, `regionID`) VALUES ", DbKey::$USER_MAP_TB) . $multiValue;

if (mysqli_query($con, $sqlInsertCustomerMap)) {
    $response[DATA] = SUCCESS;
    $vc = new VersionControl($con);
    $vc->updateVersionFor(USER_VCS);
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);