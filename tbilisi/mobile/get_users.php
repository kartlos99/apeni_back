<?php
namespace Apeni\JWT;
// ---------- get users ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();

$sql = "SELECT a.`id`, a.`username`, a.`name`, a.`type`, a.`tel`, a.`adress`, IFNULL(b.username, 'x') as maker, a.`comment` 
        FROM
            `users` a LEFT JOIN `users` b on `a`.`maker` = `b`.`id`
        WHERE a.active = 1" ;
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);