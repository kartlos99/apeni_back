<?php

namespace Apeni\JWT;

use YeastDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new YeastDataManager();

echo json_encode(
    $dbManager->insertYeast(
        $postData->name,
        $sessionData->userID
    )
);

$dbManager->closeConnection();