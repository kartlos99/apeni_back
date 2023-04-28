<?php

namespace Apeni\JWT;

use DistributionDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();
$selectedDate = $_GET['date'] ?? $dateOnServer;

$dbManager = new DistributionDataManager();

echo json_encode($dbManager->getTotalInfo($selectedDate));

$dbManager->closeConnection();
