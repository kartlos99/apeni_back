<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


require_once('_load.php');


$payload = [
    'iat' => time(),
    'iss' => 'localhost',
    'exp' => time() + 10 * 60,
    'userID' => '15'
];

$token = JWT::encode($payload, SECRET_KEY);

echo $token;