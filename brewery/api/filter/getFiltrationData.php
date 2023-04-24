<?php

namespace Apeni\JWT;

use FilterDataManager;
use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$tankID = 0;
if (isset($_GET["tankID"]) && $_GET["tankID"] > 0)
    $tankID = $_GET["tankID"];
else
    dieWithDefaultHttpError("no tank id is set!", ERROR_CODE_MISSED_PARAM);

$myData = new MyData($dbLink);
$dbManager = new FilterDataManager();

$filtration = $dbManager->getCurrentFilteringDataByTankId($tankID);

if (empty($filtration))
    dieWithDefaultHttpError("found 0 active process on the tank", ERROR_CODE_EMPTY_RESULT);

$filtration["flowsIn"] = $dbManager->getFlowsIn($filtration["ID"]);
$filtration["flowsOut"] = $myData->pouredFromFiltrationToBarrels($filtration["ID"]);

echo json_encode($filtration);

$dbManager->closeConnection();
mysqli_close($dbLink);