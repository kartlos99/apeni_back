<?php

namespace Apeni\JWT;

use BeerDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new BeerDataManager();

echo json_encode(
    $dbManager->updateBeer(
        $postData->id,
        $postData->name,
        $postData->price,
        $postData->status,
        $postData->color,
        $postData->sortValue
    )
);

$dbManager->closeConnection();