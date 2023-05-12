<?php

namespace Apeni\JWT;

use YeastDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$dbManager = new YeastDataManager();

echo json_encode($dbManager->getYeasts());

$dbManager->closeConnection();