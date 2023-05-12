<?php

namespace Apeni\JWT;

use BoilerDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$dbManager = new BoilerDataManager();

echo json_encode($dbManager->getIncompleteBoilings());

$dbManager->closeConnection();