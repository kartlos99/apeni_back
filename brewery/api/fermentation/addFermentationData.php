<?php

namespace Apeni\JWT;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$myData = new MyData($dbLink);

$measurementDate = $postData->date;
if (empty($measurementDate))
    $measurementDate = $timeOnServer;

if ($postData->isSealing)
    $myData->updateFermentationSealingDate($postData->fermentationID, $measurementDate);

$sql = "INSERT INTO `f_data` 
            (`fID`, `dataType`, `value`, `measurementDate`, `comment`, `modifyDate`, `modifyUserID`) 
            VALUES 
            ";

$multiValue = "";
foreach ($postData->data as $item) {
    $fID = $postData->fermentationID;
    $dataType = $item->type;
    $value = $item->value;
    $userID = $sessionData->userID;
    $multiValue .= "($fID, $dataType, $value, '$measurementDate', null, CURRENT_TIMESTAMP, $userID),";
}
$values = trim($multiValue,',');

echo json_encode($myData->insertFermentationData($sql . $values));

mysqli_close($dbLink);