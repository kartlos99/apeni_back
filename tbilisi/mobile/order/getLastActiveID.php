<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

$clientID = $_GET["clientID"];

$getOrderSql = "
SELECT ifnull(max(o.ID), 0) AS orderID FROM `orders` o
LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
WHERE di.code = 'order_active' AND o.`clientID` = " . $clientID;

$result = mysqli_query($con, $getOrderSql);
// obieqtze bolo aqtiuri Sekvetis ID
$orderID = mysqli_fetch_assoc($result)['orderID'];

$response[DATA] = $orderID;

echo json_encode($response);

mysqli_close($con);