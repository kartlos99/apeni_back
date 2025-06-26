<?php

namespace Apeni\JWT;

//use DbKey;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkTokenN();
//throwHttpError(401, "errorText", 401);

echo json_encode($sessionData);

