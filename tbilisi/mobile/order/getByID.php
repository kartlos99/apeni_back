<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

$receivedOrderID = $_GET["orderID"];

$orders = [];
$orderHelper = new OrderHelper($con);

$sql = "
SELECT o.`ID`, date(o.`orderDate`) AS orderDate, o.`orderStatusID`, o.`distributorID`, o.`clientID`, 
       o.`comment`, o.`modifyDate`, o.`modifyUserID`, di.code AS orderStatus FROM `orders` o
LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
WHERE o.ID = " . $receivedOrderID;

$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $orders[] = $rs;
}

if (count($orders) == 1) {
    $response[DATA] = $orderHelper->attachItemsToOrder($orders);
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't find order!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);