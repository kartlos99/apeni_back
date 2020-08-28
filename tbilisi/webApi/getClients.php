<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

checkToken();

$response[DATA] = 'test';


echo json_encode($response);