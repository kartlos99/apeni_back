<?php
namespace Apeni\JWT;
//use function Apeni\JWT\checkToken;

use UserDataManager;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$dbManager = new UserDataManager();

echo json_encode($dbManager->getUsers());

$dbManager->closeConnection();