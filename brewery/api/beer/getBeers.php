<?php

namespace Apeni\JWT;

use BeerDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$dbManager = new BeerDataManager();

echo json_encode($dbManager->getBeers());

$dbManager->closeConnection();
