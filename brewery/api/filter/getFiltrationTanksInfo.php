<?php

namespace Apeni\JWT;

use FilterDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$dbManager = new FilterDataManager();

$tanks = $dbManager->getFiltrationTanks();
$filtrations = $dbManager->getAllActiveFiltration();

$response = [];
foreach ($tanks as $tank) {
    $searchResult = array_filter($filtrations, function ($filtrationItem) {
        global $tank;
        return $filtrationItem["filterTankID"] == $tank["ID"];
    });
    $filtration = count($searchResult) > 0 ? array_values($searchResult)[0] : null;
    $response[] = [
        "tank" => $tank,
        "filtration" => $filtration
    ];
}

echo json_encode($response);

$dbManager->closeConnection();