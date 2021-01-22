<?php
namespace Apeni\JWT;
//use function Apeni\JWT\checkToken;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
checkToken();

$sql = "SELECT a.`id`, a.`username`, a.`name`, a.`type`, a.`tel`, a.`adress`, IFNULL(b.username, 'x') as maker, a.`comment` 
        FROM
            `users` a LEFT JOIN `users` b on `a`.`maker` = `b`.`id`
        WHERE a.active = 1" ;
$arr = [];
$result = mysqli_query($dbLink, $sql);

while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($dbLink);