<?php

namespace Apeni\JWT;

use BoilerDataManager;
use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$myData = new MyData($dbLink);
$boilingDataManager = new BoilerDataManager();

$boilingDataManager->removeYeastFromFermentation($postData->yeastID);
$myData->terminateYeast($postData->yeastID, $sessionData->userID);

echo json_encode([RECORD_ID_KEY => 0]);

mysqli_close($dbLink);