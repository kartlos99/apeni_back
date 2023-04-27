<?php

namespace Apeni\JWT;

use DistributionDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();


$dbManager = new DistributionDataManager();

echo json_encode($dbManager->getTotalInfo());

$dbManager->closeConnection();
