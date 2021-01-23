<?php
namespace Apeni\JWT;
//use function Apeni\JWT\checkToken;

use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$myData = new MyData($dbLink);

$sql = "SELECT a.`id`, a.`username`, a.`name`, a.`type`, a.`tel`, a.`adress`, IFNULL(b.username, 'x') as maker, a.`comment` 
        FROM
            `users` a LEFT JOIN `users` b on `a`.`maker` = `b`.`id`
        WHERE a.active = 1" ;

$response[DATA] = $myData->getDataAsArray($sql);

echo json_encode($response);

mysqli_close($dbLink);