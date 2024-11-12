<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
 $sessionData = checkToken();
require_once('../../BaseDbManagerV2.php');
$dbManager = new \BaseDbManagerV2();

$userListSql = "SELECT
    a.`id`,
    a.`username`,
    a.`name`,
    a.`type`,
    a.`tel`,
    a.`adress`,
    IFNULL(b.username, 'x') AS maker,
    a.active AS userStatus,
    a.`comment`
FROM
    `users` a
LEFT JOIN `users` b ON
    `a`.`maker` = `b`.`id`
LEFT JOIN user_to_region_map um ON
    um.userID = a.id
WHERE
    um.regionID = {$sessionData->regionID} AND a.active >= 1 ";

$users = $dbManager->getDataAsArray($userListSql);

echo json_encode($users);

$dbManager->closeConnection();